<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Victim;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class IncidentService
{
    /**
     * Create incident with all related data (victims, media, assignments)
     * Uses database transaction for data integrity
     *
     * @param array $data
     * @return Incident
     * @throws \Exception
     */
    public function createIncident(array $data): Incident
    {
        return DB::transaction(function () use ($data) {
            // Extract and process media
            $photoPaths = [];
            $videoPaths = [];

            if (isset($data['photos'])) {
                $photoPaths = $this->processPhotos($data['photos']);
                unset($data['photos']);
            }

            if (isset($data['videos'])) {
                $videoPaths = $this->processVideos($data['videos']);
                unset($data['videos']);
            }

            // Extract victims data if any
            $victimsData = $data['victims'] ?? [];
            unset($data['victims']);

            // Generate incident number with built-in retry logic
            try {
                $incidentNumber = Incident::generateIncidentNumber();
            } catch (\RuntimeException $e) {
                Log::error('Incident number generation failed', [
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id(),
                ]);

                throw new \Exception(
                    'Unable to generate incident number. This may be due to high system load. Please try again in a moment.',
                    0,
                    $e
                );
            }

            // Create incident
            $incident = Incident::create([
                ...$data,
                'incident_number' => $incidentNumber,
                'reported_by' => auth()->id(),
                'status' => 'pending',
                'photos' => $photoPaths,
                'videos' => $videoPaths,
                'casualty_count' => 0,
                'injury_count' => 0,
                'fatality_count' => 0,
            ]);

            // Create victims if any
            if (!empty($victimsData) && is_array($victimsData)) {
                foreach ($victimsData as $victimData) {
                    if (!empty($victimData['first_name']) && !empty($victimData['last_name'])) {
                        $this->createVictimForIncident($incident, $victimData);
                    }
                }
            }

            // Update vehicle status if assigned
            if (!empty($data['assigned_vehicle_id'])) {
                $this->assignVehicle($incident, $data['assigned_vehicle_id']);
            }

            // Log activity
            activity()
                ->performedOn($incident)
                ->withProperties([
                    'incident_number' => $incident->incident_number,
                    'incident_type' => $incident->incident_type,
                    'victims_count' => count($victimsData)
                ])
                ->log('Incident created with ' . count($victimsData) . ' victim(s)');

            return $incident->load(['victims', 'assignedStaff', 'assignedVehicle']);
        });
    }

    /**
     * Update incident with all related data
     *
     * @param Incident $incident
     * @param array $data
     * @return Incident
     * @throws \Exception
     */
    public function updateIncident(Incident $incident, array $data): Incident
    {
        return DB::transaction(function () use ($incident, $data) {
            $oldValues = $incident->toArray();

            // Handle new photos
            if (isset($data['photos'])) {
                $newPhotos = $this->processPhotos($data['photos']);
                $existingPhotos = $incident->photos ?? [];
                $data['photos'] = array_merge($existingPhotos, $newPhotos);
            }

            // Handle new videos
            if (isset($data['videos'])) {
                $newVideos = $this->processVideos($data['videos']);
                $existingVideos = $incident->videos ?? [];
                $data['videos'] = array_merge($existingVideos, $newVideos);
            }

            // Update incident
            $incident->update($data);

            // Handle vehicle assignment change
            if (isset($data['assigned_vehicle_id']) && $data['assigned_vehicle_id'] !== $oldValues['assigned_vehicle_id']) {
                // Release old vehicle
                if ($oldValues['assigned_vehicle_id']) {
                    $this->releaseVehicle($oldValues['assigned_vehicle_id']);
                }

                // Assign new vehicle
                if ($data['assigned_vehicle_id']) {
                    $this->assignVehicle($incident, $data['assigned_vehicle_id']);
                }
            }

            // Log activity
            activity()
                ->performedOn($incident)
                ->withProperties(['old' => $oldValues, 'attributes' => $data])
                ->log('Incident updated');

            return $incident->fresh(['victims', 'assignedStaff', 'assignedVehicle']);
        });
    }

    /**
     * Process and store photo uploads
     *
     * @param array $photos
     * @return array
     */
    private function processPhotos(array $photos): array
    {
        $paths = [];
        foreach ($photos as $photo) {
            if ($photo && $photo->isValid()) {
                $path = $photo->store('incident_photos', 'public');
                $paths[] = $path;
            }
        }
        return $paths;
    }

    /**
     * Process and store video uploads
     *
     * @param array $videos
     * @return array
     */
    private function processVideos(array $videos): array
    {
        $paths = [];
        foreach ($videos as $video) {
            if ($video && $video->isValid()) {
                $path = $video->store('incident_videos', 'public');
                $paths[] = $path;
            }
        }
        return $paths;
    }

    /**
     * Create victim with automatic age category calculation
     *
     * @param Incident $incident
     * @param array $victimData
     * @return Victim
     */
    public function createVictimForIncident(Incident $incident, array $victimData): Victim
    {
        // Auto-calculate age category if age is provided
        if (isset($victimData['age'])) {
            $victimData['age_category'] = $this->calculateAgeCategory($victimData['age']);
        }

        // Auto-determine special care requirements
        if (!isset($victimData['requires_special_care'])) {
            $victimData['requires_special_care'] = $this->requiresSpecialCare($victimData);
        }

        $victim = $incident->victims()->create($victimData);

        // Update incident casualty counts
        $this->updateIncidentCounts($incident, $victimData['medical_status'] ?? 'uninjured', 'increment');

        return $victim;
    }

    /**
     * Calculate age category for special care determination
     *
     * @param int $age
     * @return string
     */
    private function calculateAgeCategory(int $age): string
    {
        return match (true) {
            $age < 13 => 'child',
            $age < 18 => 'teen',
            $age < 60 => 'adult',
            default => 'elderly'
        };
    }

    /**
     * Determine if victim requires special care
     *
     * @param array $victimData
     * @return bool
     */
    private function requiresSpecialCare(array $victimData): bool
    {
        // Children, elderly, pregnant women, or critical status require special care
        if (isset($victimData['age_category']) && in_array($victimData['age_category'], ['child', 'elderly'])) {
            return true;
        }

        if (isset($victimData['is_pregnant']) && $victimData['is_pregnant']) {
            return true;
        }

        if (isset($victimData['medical_status']) && in_array($victimData['medical_status'], ['critical', 'major_injury'])) {
            return true;
        }

        return false;
    }

    /**
     * Update incident casualty counts
     *
     * @param Incident $incident
     * @param string $medicalStatus
     * @param string $operation 'increment' or 'decrement'
     * @return void
     */
    public function updateIncidentCounts(Incident $incident, string $medicalStatus, string $operation = 'increment'): void
    {
        if ($operation === 'increment') {
            $incident->increment('casualty_count');

            if (in_array($medicalStatus, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->increment('injury_count');
            }

            if ($medicalStatus === 'deceased') {
                $incident->increment('fatality_count');
            }
        } else {
            $incident->decrement('casualty_count');

            if (in_array($medicalStatus, ['minor_injury', 'major_injury', 'critical'])) {
                $incident->decrement('injury_count');
            }

            if ($medicalStatus === 'deceased') {
                $incident->decrement('fatality_count');
            }
        }
    }

    /**
     * Assign vehicle and update its status
     *
     * @param Incident $incident
     * @param int $vehicleId
     * @return void
     */
    private function assignVehicle(Incident $incident, int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->update(['status' => 'in_use']);

        activity()
            ->performedOn($vehicle)
            ->withProperties([
                'incident_id' => $incident->id,
                'incident_number' => $incident->incident_number
            ])
            ->log('Vehicle assigned to incident');
    }

    /**
     * Release vehicle and update its status
     *
     * @param int $vehicleId
     * @return void
     */
    private function releaseVehicle(int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->update(['status' => 'available']);

        activity()
            ->performedOn($vehicle)
            ->log('Vehicle released from incident');
    }

    /**
     * Delete incident photo
     *
     * @param Incident $incident
     * @param string $photoPath
     * @return bool
     */
    public function deleteIncidentPhoto(Incident $incident, string $photoPath): bool
    {
        $photos = $incident->photos ?? [];

        if (($key = array_search($photoPath, $photos)) !== false) {
            unset($photos[$key]);
            $incident->update(['photos' => array_values($photos)]);

            // Delete from storage
            Storage::disk('public')->delete($photoPath);

            return true;
        }

        return false;
    }

    /**
     * Delete incident video
     *
     * @param Incident $incident
     * @param string $videoPath
     * @return bool
     */
    public function deleteIncidentVideo(Incident $incident, string $videoPath): bool
    {
        $videos = $incident->videos ?? [];

        if (($key = array_search($videoPath, $videos)) !== false) {
            unset($videos[$key]);
            $incident->update(['videos' => array_values($videos)]);

            // Delete from storage
            Storage::disk('public')->delete($videoPath);

            return true;
        }

        return false;
    }

    /**
     * Delete incident with all related resources
     *
     * @param Incident $incident
     * @return bool
     * @throws \Exception
     */
    public function deleteIncident(Incident $incident): bool
    {
        return DB::transaction(function () use ($incident) {
            // Delete all photos from storage
            if ($incident->photos && is_array($incident->photos)) {
                foreach ($incident->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            // Delete all videos from storage
            if ($incident->videos && is_array($incident->videos)) {
                foreach ($incident->videos as $video) {
                    Storage::disk('public')->delete($video);
                }
            }

            // Delete all documents from storage
            if ($incident->documents && is_array($incident->documents)) {
                foreach ($incident->documents as $document) {
                    Storage::disk('public')->delete($document);
                }
            }

            // Release assigned vehicle if any
            if ($incident->assigned_vehicle_id) {
                $this->releaseVehicle($incident->assigned_vehicle_id);
            }

            // Log deletion before deleting
            activity()
                ->performedOn($incident)
                ->withProperties([
                    'incident_number' => $incident->incident_number,
                    'incident_type' => $incident->incident_type,
                    'municipality' => $incident->municipality
                ])
                ->log('Incident deleted');

            // Soft delete the incident
            return $incident->delete();
        });
    }

    /**
     * Restore a soft-deleted incident
     *
     * @param Incident $incident
     * @return bool
     */
    public function restoreIncident(Incident $incident): bool
    {
        $restored = $incident->restore();

        if ($restored) {
            activity()
                ->performedOn($incident)
                ->withProperties([
                    'incident_number' => $incident->incident_number
                ])
                ->log('Incident restored');
        }

        return $restored;
    }

    /**
     * Permanently delete an incident
     *
     * @param Incident $incident
     * @return bool
     * @throws \Exception
     */
    public function forceDeleteIncident(Incident $incident): bool
    {
        return DB::transaction(function () use ($incident) {
            // Delete all media files
            $this->deleteIncident($incident);

            // Force delete (permanently remove from database)
            return $incident->forceDelete();
        });
    }
}

