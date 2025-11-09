<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediaService
{
    /**
     * Upload and process photos with compression
     *
     * @param array $photos Array of UploadedFile instances
     * @param string $municipality
     * @param string $incidentNumber
     * @return array Array of file paths
     * @throws Exception
     */
    public function uploadPhotos(array $photos, string $municipality, string $incidentNumber): array
    {
        $paths = [];
        $config = config('media.photos');

        foreach ($photos as $index => $photo) {
            if (!$photo || !$photo->isValid()) {
                Log::warning('Invalid photo upload', ['index' => $index]);
                continue;
            }

            // Validate photo
            $this->validatePhoto($photo);

            // Generate organized path
            $basePath = $this->generatePath($municipality, $incidentNumber, 'photos');

            // Process and compress photo
            $filename = $this->generateFilename($photo, $index);

            if ($config['compress']['enabled']) {
                // Compress and save
                $compressedPath = $this->compressPhoto($photo, $basePath, $filename);
                $paths[] = $compressedPath;

                // Generate thumbnails
                if ($config['thumbnails']['enabled']) {
                    $this->generateThumbnails($photo, $basePath, $filename);
                }
            } else {
                // Save without compression
                $path = $photo->storeAs($basePath, $filename, 'public');
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Upload and process videos
     *
     * @param array $videos Array of UploadedFile instances
     * @param string $municipality
     * @param string $incidentNumber
     * @return array Array of file paths
     * @throws Exception
     */
    public function uploadVideos(array $videos, string $municipality, string $incidentNumber): array
    {
        $paths = [];
        $config = config('media.videos');

        foreach ($videos as $index => $video) {
            if (!$video || !$video->isValid()) {
                Log::warning('Invalid video upload', ['index' => $index]);
                continue;
            }

            // Validate video
            $this->validateVideo($video);

            // Generate organized path
            $basePath = $this->generatePath($municipality, $incidentNumber, 'videos');

            // Generate filename
            $filename = $this->generateFilename($video, $index);

            // Store video (compression requires FFmpeg - to be implemented)
            $path = $video->storeAs($basePath . '/original', $filename, 'public');
            $paths[] = $path;

            // TODO: Implement video compression when FFmpeg is available
            // $compressedPath = $this->compressVideo($video, $basePath, $filename);
        }

        return $paths;
    }

    /**
     * Compress photo using GD library
     *
     * @param UploadedFile $photo
     * @param string $basePath
     * @param string $filename
     * @return string Path to compressed photo
     * @throws Exception
     */
    private function compressPhoto(UploadedFile $photo, string $basePath, string $filename): string
    {
        $config = config('media.photos.compress');

        try {
            // Read image
            $imageInfo = getimagesize($photo->getRealPath());
            $mimeType = $imageInfo['mime'];

            // Create image resource based on type
            $image = match ($mimeType) {
                'image/jpeg' => imagecreatefromjpeg($photo->getRealPath()),
                'image/png' => imagecreatefrompng($photo->getRealPath()),
                'image/gif' => imagecreatefromgif($photo->getRealPath()),
                'image/webp' => imagecreatefromwebp($photo->getRealPath()),
                default => throw new Exception('Unsupported image type: ' . $mimeType)
            };

            if (!$image) {
                throw new Exception('Failed to create image resource');
            }

            // Get original dimensions
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calculate new dimensions while maintaining aspect ratio
            $newDimensions = $this->calculateDimensions(
                $originalWidth,
                $originalHeight,
                $config['max_width'],
                $config['max_height']
            );

            // Create resized image
            $resizedImage = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);

            // Preserve transparency for PNG
            if ($mimeType === 'image/png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            // Resize
            imagecopyresampled(
                $resizedImage,
                $image,
                0, 0, 0, 0,
                $newDimensions['width'],
                $newDimensions['height'],
                $originalWidth,
                $originalHeight
            );

            // Save compressed image
            $compressedPath = $basePath . '/compressed/' . $filename;
            $fullPath = storage_path('app/public/' . $compressedPath);

            // Ensure directory exists
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save based on configured format
            $format = $config['format'] ?? 'jpg';
            $quality = $config['quality'] ?? 75;

            if ($format === 'jpg' || $format === 'jpeg') {
                // Change extension to jpg
                $compressedPath = preg_replace('/\.[^.]+$/', '.jpg', $compressedPath);
                $fullPath = preg_replace('/\.[^.]+$/', '.jpg', $fullPath);
                imagejpeg($resizedImage, $fullPath, $quality);
            } else {
                imagejpeg($resizedImage, $fullPath, $quality);
            }

            // Free memory
            imagedestroy($image);
            imagedestroy($resizedImage);

            Log::info('Photo compressed successfully', [
                'original_size' => $photo->getSize(),
                'original_dimensions' => "{$originalWidth}x{$originalHeight}",
                'new_dimensions' => "{$newDimensions['width']}x{$newDimensions['height']}",
                'path' => $compressedPath
            ]);

            return $compressedPath;

        } catch (Exception $e) {
            Log::error('Photo compression failed', [
                'error' => $e->getMessage(),
                'photo' => $photo->getClientOriginalName()
            ]);

            // Fallback: save original without compression
            return $photo->storeAs($basePath . '/original', $filename, 'public');
        }
    }

    /**
     * Generate thumbnails for photo
     *
     * @param UploadedFile $photo
     * @param string $basePath
     * @param string $filename
     * @return array Paths to generated thumbnails
     */
    private function generateThumbnails(UploadedFile $photo, string $basePath, string $filename): array
    {
        $thumbnails = [];
        $sizes = config('media.photos.thumbnails.sizes', []);

        try {
            $imageInfo = getimagesize($photo->getRealPath());
            $mimeType = $imageInfo['mime'];

            $image = match ($mimeType) {
                'image/jpeg' => imagecreatefromjpeg($photo->getRealPath()),
                'image/png' => imagecreatefrompng($photo->getRealPath()),
                'image/gif' => imagecreatefromgif($photo->getRealPath()),
                'image/webp' => imagecreatefromwebp($photo->getRealPath()),
                default => null
            };

            if (!$image) {
                return [];
            }

            foreach ($sizes as $sizeName => $dimensions) {
                [$width, $height] = $dimensions;

                // Create thumbnail
                $thumbnail = imagecreatetruecolor($width, $height);

                // Preserve transparency
                if ($mimeType === 'image/png') {
                    imagealphablending($thumbnail, false);
                    imagesavealpha($thumbnail, true);
                }

                imagecopyresampled(
                    $thumbnail,
                    $image,
                    0, 0, 0, 0,
                    $width,
                    $height,
                    imagesx($image),
                    imagesy($image)
                );

                // Save thumbnail
                $thumbnailPath = $basePath . "/thumbnails/{$sizeName}/" . $filename;
                $fullPath = storage_path('app/public/' . $thumbnailPath);

                $directory = dirname($fullPath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Change to jpg and save
                $thumbnailPath = preg_replace('/\.[^.]+$/', '.jpg', $thumbnailPath);
                $fullPath = preg_replace('/\.[^.]+$/', '.jpg', $fullPath);
                imagejpeg($thumbnail, $fullPath, 80);

                imagedestroy($thumbnail);
                $thumbnails[$sizeName] = $thumbnailPath;
            }

            imagedestroy($image);

            Log::info('Thumbnails generated', [
                'count' => count($thumbnails),
                'sizes' => array_keys($thumbnails)
            ]);

        } catch (Exception $e) {
            Log::error('Thumbnail generation failed', [
                'error' => $e->getMessage()
            ]);
        }

        return $thumbnails;
    }

    /**
     * Calculate new dimensions maintaining aspect ratio
     *
     * @param int $originalWidth
     * @param int $originalHeight
     * @param int $maxWidth
     * @param int $maxHeight
     * @return array
     */
    private function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth, int $maxHeight): array
    {
        // If image is smaller than max, keep original size
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        // Calculate aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        return [
            'width' => (int) round($originalWidth * $ratio),
            'height' => (int) round($originalHeight * $ratio)
        ];
    }

    /**
     * Generate organized file path
     *
     * @param string $municipality
     * @param string $incidentNumber
     * @param string $type 'photos' or 'videos'
     * @return string
     */
    private function generatePath(string $municipality, string $incidentNumber, string $type): string
    {
        // Parse incident number to get year and month (format: INC-YYYY-XXX)
        preg_match('/INC-(\d{4})-\d+/', $incidentNumber, $matches);
        $year = $matches[1] ?? date('Y');
        $month = date('m');

        // Clean municipality name
        $municipalitySlug = Str::slug($municipality);

        // Generate path: incidents/{municipality}/{year}/{month}/{incident_number}/{type}
        return "incidents/{$municipalitySlug}/{$year}/{$month}/{$incidentNumber}/{$type}";
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @param int $index
     * @return string
     */
    private function generateFilename(UploadedFile $file, int $index): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = Str::random(8);

        return "{$timestamp}_{$random}_{$index}.{$extension}";
    }

    /**
     * Validate photo upload
     *
     * @param UploadedFile $photo
     * @throws Exception
     */
    private function validatePhoto(UploadedFile $photo): void
    {
        $config = config('media.photos');

        // Check file size
        if ($photo->getSize() > $config['max_size']) {
            throw new Exception(
                "Photo exceeds maximum size of " .
                $this->formatBytes($config['max_size'])
            );
        }

        // Check mime type
        if (!in_array($photo->getMimeType(), $config['allowed_types'])) {
            throw new Exception("Invalid photo type: " . $photo->getMimeType());
        }

        // Check dimensions if configured
        if (isset($config['max_dimensions'])) {
            $imageInfo = getimagesize($photo->getRealPath());
            [$width, $height] = $imageInfo;
            [$maxWidth, $maxHeight] = $config['max_dimensions'];

            if ($width > $maxWidth || $height > $maxHeight) {
                throw new Exception(
                    "Photo dimensions ({$width}x{$height}) exceed maximum allowed ({$maxWidth}x{$maxHeight})"
                );
            }
        }
    }

    /**
     * Validate video upload
     *
     * @param UploadedFile $video
     * @throws Exception
     */
    private function validateVideo(UploadedFile $video): void
    {
        $config = config('media.videos');

        // Check file size
        if ($video->getSize() > $config['max_size']) {
            throw new Exception(
                "Video exceeds maximum size of " .
                $this->formatBytes($config['max_size'])
            );
        }

        // Check mime type
        if (!in_array($video->getMimeType(), $config['allowed_types'])) {
            throw new Exception("Invalid video type: " . $video->getMimeType());
        }

        // TODO: Add duration validation when FFmpeg is available
    }

    /**
     * Delete photo from storage
     *
     * @param string $path
     * @return bool
     */
    public function deletePhoto(string $path): bool
    {
        try {
            // Delete compressed version
            Storage::disk('public')->delete($path);

            // Delete thumbnails if they exist
            $pathInfo = pathinfo($path);
            $basePath = str_replace('/compressed/', '/thumbnails/', dirname($path));

            foreach (config('media.photos.thumbnails.sizes', []) as $sizeName => $dimensions) {
                $thumbnailPath = "{$basePath}/{$sizeName}/" . $pathInfo['basename'];
                Storage::disk('public')->delete($thumbnailPath);
            }

            // Delete original if it exists
            $originalPath = str_replace('/compressed/', '/original/', $path);
            Storage::disk('public')->delete($originalPath);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete photo', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Delete video from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteVideo(string $path): bool
    {
        try {
            Storage::disk('public')->delete($path);

            // Delete compressed version if exists
            $compressedPath = str_replace('/original/', '/compressed/', $path);
            Storage::disk('public')->delete($compressedPath);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete video', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get thumbnail path for a photo
     *
     * @param string $photoPath
     * @param string $size 'small' or 'medium'
     * @return string
     */
    public function getThumbnailPath(string $photoPath, string $size = 'medium'): string
    {
        $pathInfo = pathinfo($photoPath);
        $basePath = str_replace('/compressed/', '/thumbnails/', dirname($photoPath));

        return "{$basePath}/{$size}/" . preg_replace('/\.[^.]+$/', '.jpg', $pathInfo['basename']);
    }
}
