# Media Upload Refactoring Plan - Laravel MVC Clean Code

**Project:** MDRRMC Incident Management System
**Date:** October 24, 2025
**Status:** 📋 Planning Phase

---

## 🎯 **Refactoring Goals**

### **Current Issues:**
1. ❌ **PHP logic in Blade views** - Business logic mixed with presentation
2. ❌ **Large inline JavaScript** - 495 lines of JS in view template
3. ❌ **Service class doing too much** - IncidentService handles media processing
4. ❌ **No proper separation of concerns** - Validation, storage, processing all mixed
5. ❌ **No reusability** - Media upload logic tied to incidents only
6. ❌ **Hard to test** - Logic scattered across views, services, controllers
7. ❌ **No image optimization** - Raw uploads with no compression/resizing

### **Desired Architecture:**
✅ **Single Responsibility Principle** - Each class has one job
✅ **Dependency Injection** - Proper Laravel service container usage
✅ **Testable Components** - Unit and feature tests possible
✅ **Reusable Services** - Can be used for incidents, vehicles, requests, etc.
✅ **Clean MVC Structure** - Clear separation of Model, View, Controller
✅ **Configuration-Driven** - Settings in config files, not hardcoded
✅ **Event-Driven** - Use Laravel events for side effects

---

## 📐 **Proposed Architecture**

### **Layer 1: Configuration**
```
config/
└── media.php                    (Media upload settings)
```

### **Layer 2: Models & Contracts**
```
app/Models/
└── Concerns/
    └── HasMedia.php            (Trait for models with media)

app/Contracts/
└── MediaServiceInterface.php   (Interface for media services)
```

### **Layer 3: Services**
```
app/Services/Media/
├── MediaService.php            (Main media service)
├── ImageProcessor.php          (Image optimization)
├── VideoProcessor.php          (Video processing)
└── MediaValidator.php          (File validation)
```

### **Layer 4: DTOs (Data Transfer Objects)**
```
app/DataTransferObjects/
└── MediaUploadResult.php       (Encapsulates upload results)
```

### **Layer 5: Form Requests**
```
app/Http/Requests/
├── StoreIncidentRequest.php    (Already exists, update validation)
└── MediaUploadRequest.php      (Dedicated media validation)
```

### **Layer 6: Controllers**
```
app/Http/Controllers/
├── IncidentController.php      (Simplified, delegates to services)
└── MediaController.php         (NEW: Handles media-specific routes)
```

### **Layer 7: View Components**
```
app/View/Components/
└── MediaUpload.php             (Blade component class)

resources/views/components/
├── media-upload.blade.php      (Component template)
└── media-gallery.blade.php     (Display component)
```

### **Layer 8: Frontend Assets**
```
resources/js/
└── components/
    └── media-upload.js         (External JS file)

resources/css/
└── components/
    └── media-upload.css        (Component styles)
```

### **Layer 9: Events & Listeners**
```
app/Events/
└── MediaUploaded.php           (Fired when media uploaded)

app/Listeners/
├── OptimizeUploadedImage.php   (Optimize images)
└── GenerateThumbnails.php      (Create thumbnails)
```

---

## 📋 **Step-by-Step Implementation Plan**

### **Phase 1: Configuration & Setup** (30 minutes)

#### **Step 1.1: Create Media Configuration File**
```bash
# Create config file
touch config/media.php
```

**File:** `config/media.php`
```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure media upload limits, allowed types, and storage settings
    |
    */

    'photos' => [
        'max_count' => env('MEDIA_MAX_PHOTOS', 5),
        'max_size' => env('MEDIA_PHOTO_MAX_SIZE', 2048), // KB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'storage_disk' => 'public',
        'storage_path' => 'incident_photos',
        'optimize' => env('MEDIA_OPTIMIZE_IMAGES', true),
        'max_width' => 1920,
        'max_height' => 1080,
        'quality' => 85,
        'generate_thumbnails' => true,
        'thumbnail_width' => 300,
        'thumbnail_height' => 300,
    ],

    'videos' => [
        'max_count' => env('MEDIA_MAX_VIDEOS', 2),
        'max_size' => env('MEDIA_VIDEO_MAX_SIZE', 10240), // KB
        'allowed_types' => ['video/mp4', 'video/webm', 'video/quicktime'],
        'allowed_extensions' => ['mp4', 'webm', 'mov'],
        'storage_disk' => 'public',
        'storage_path' => 'incident_videos',
    ],

    /*
    | Intervention Image Driver
    | Options: 'gd', 'imagick'
    */
    'image_driver' => env('MEDIA_IMAGE_DRIVER', 'gd'),
];
```

**Benefits:**
- ✅ Centralized configuration
- ✅ Environment-specific settings
- ✅ Easy to modify without code changes
- ✅ Type-safe with array structure

---

### **Phase 2: Create Service Layer** (2 hours)

#### **Step 2.1: Create MediaServiceInterface**

**File:** `app/Contracts/MediaServiceInterface.php`
```php
<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;
use App\DataTransferObjects\MediaUploadResult;

interface MediaServiceInterface
{
    /**
     * Upload and process photos
     *
     * @param array $photos Array of UploadedFile instances
     * @param string|null $context Context (e.g., 'incident', 'vehicle')
     * @return MediaUploadResult
     */
    public function uploadPhotos(array $photos, ?string $context = null): MediaUploadResult;

    /**
     * Upload and process videos
     *
     * @param array $videos Array of UploadedFile instances
     * @param string|null $context Context
     * @return MediaUploadResult
     */
    public function uploadVideos(array $videos, ?string $context = null): MediaUploadResult;

    /**
     * Delete media file
     *
     * @param string $path File path
     * @param string $type Type ('photo' or 'video')
     * @return bool
     */
    public function deleteMedia(string $path, string $type = 'photo'): bool;

    /**
     * Get media URL
     *
     * @param string $path File path
     * @return string
     */
    public function getMediaUrl(string $path): string;
}
```

**Benefits:**
- ✅ Contract-based programming
- ✅ Easy to mock for testing
- ✅ Can swap implementations

---

#### **Step 2.2: Create MediaUploadResult DTO**

**File:** `app/DataTransferObjects/MediaUploadResult.php`
```php
<?php

namespace App\DataTransferObjects;

class MediaUploadResult
{
    public function __construct(
        public readonly array $paths,
        public readonly array $failures = [],
        public readonly int $successCount = 0,
        public readonly int $failureCount = 0,
    ) {}

    /**
     * Create from arrays
     *
     * @param array $paths Successful upload paths
     * @param array $failures Failed uploads with reasons
     * @return static
     */
    public static function create(array $paths, array $failures = []): static
    {
        return new static(
            paths: $paths,
            failures: $failures,
            successCount: count($paths),
            failureCount: count($failures),
        );
    }

    /**
     * Check if all uploads succeeded
     *
     * @return bool
     */
    public function isFullySuccessful(): bool
    {
        return $this->failureCount === 0 && $this->successCount > 0;
    }

    /**
     * Check if any uploads succeeded
     *
     * @return bool
     */
    public function hasSuccesses(): bool
    {
        return $this->successCount > 0;
    }

    /**
     * Get failure messages
     *
     * @return array
     */
    public function getFailureMessages(): array
    {
        return array_column($this->failures, 'message');
    }
}
```

**Benefits:**
- ✅ Type-safe result handling
- ✅ Immutable data
- ✅ Convenient helper methods

---

#### **Step 2.3: Create MediaValidator Service**

**File:** `app/Services/Media/MediaValidator.php`
```php
<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MediaValidator
{
    /**
     * Validate photo upload
     *
     * @param UploadedFile $file
     * @throws ValidationException
     * @return void
     */
    public function validatePhoto(UploadedFile $file): void
    {
        $config = config('media.photos');

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'image',
                    'mimes:' . implode(',', $config['allowed_extensions']),
                    'max:' . $config['max_size'],
                ],
            ],
            [
                'file.max' => 'Photo must not exceed ' . ($config['max_size'] / 1024) . 'MB.',
                'file.mimes' => 'Photo must be: ' . implode(', ', $config['allowed_extensions']),
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate video upload
     *
     * @param UploadedFile $file
     * @throws ValidationException
     * @return void
     */
    public function validateVideo(UploadedFile $file): void
    {
        $config = config('media.videos');

        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'mimetypes:' . implode(',', $config['allowed_types']),
                    'max:' . $config['max_size'],
                ],
            ],
            [
                'file.max' => 'Video must not exceed ' . ($config['max_size'] / 1024) . 'MB.',
                'file.mimetypes' => 'Video must be: ' . implode(', ', $config['allowed_extensions']),
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate photo count
     *
     * @param array $photos
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validatePhotoCount(array $photos): void
    {
        $maxCount = config('media.photos.max_count');

        if (count($photos) > $maxCount) {
            throw new \InvalidArgumentException("Cannot upload more than {$maxCount} photos");
        }
    }

    /**
     * Validate video count
     *
     * @param array $videos
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validateVideoCount(array $videos): void
    {
        $maxCount = config('media.videos.max_count');

        if (count($videos) > $maxCount) {
            throw new \InvalidArgumentException("Cannot upload more than {$maxCount} videos");
        }
    }
}
```

**Benefits:**
- ✅ Centralized validation logic
- ✅ Reusable across controllers
- ✅ Config-driven validation rules

---

#### **Step 2.4: Create ImageProcessor Service**

**File:** `app/Services/Media/ImageProcessor.php`
```php
<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageProcessor
{
    /**
     * Process and store image
     *
     * @param UploadedFile $file
     * @param string $storagePath
     * @return array ['original' => string, 'thumbnail' => string|null]
     */
    public function process(UploadedFile $file, string $storagePath): array
    {
        $config = config('media.photos');
        $filename = $this->generateFilename($file);

        // Original path
        $originalPath = $storagePath . '/' . $filename;

        if ($config['optimize']) {
            // Optimize and resize image
            $image = Image::make($file)
                ->resize($config['max_width'], $config['max_height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', $config['quality']);

            Storage::disk($config['storage_disk'])->put($originalPath, $image);
        } else {
            // Store without optimization
            Storage::disk($config['storage_disk'])->putFileAs(
                $storagePath,
                $file,
                $filename
            );
        }

        // Generate thumbnail if enabled
        $thumbnailPath = null;
        if ($config['generate_thumbnails']) {
            $thumbnailPath = $this->generateThumbnail($file, $storagePath, $filename);
        }

        return [
            'original' => $originalPath,
            'thumbnail' => $thumbnailPath,
        ];
    }

    /**
     * Generate thumbnail
     *
     * @param UploadedFile $file
     * @param string $storagePath
     * @param string $filename
     * @return string
     */
    protected function generateThumbnail(UploadedFile $file, string $storagePath, string $filename): string
    {
        $config = config('media.photos');
        $thumbnailPath = $storagePath . '/thumbnails/' . $filename;

        $thumbnail = Image::make($file)
            ->fit($config['thumbnail_width'], $config['thumbnail_height'])
            ->encode('jpg', 80);

        Storage::disk($config['storage_disk'])->put($thumbnailPath, $thumbnail);

        return $thumbnailPath;
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFilename(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
}
```

**Benefits:**
- ✅ Image optimization
- ✅ Thumbnail generation
- ✅ Memory-efficient processing

---

#### **Step 2.5: Create VideoProcessor Service**

**File:** `app/Services/Media/VideoProcessor.php`
```php
<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoProcessor
{
    /**
     * Process and store video
     *
     * @param UploadedFile $file
     * @param string $storagePath
     * @return string Path to stored video
     */
    public function process(UploadedFile $file, string $storagePath): string
    {
        $config = config('media.videos');
        $filename = $this->generateFilename($file);
        $path = $storagePath . '/' . $filename;

        Storage::disk($config['storage_disk'])->putFileAs(
            $storagePath,
            $file,
            $filename
        );

        return $path;
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFilename(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
}
```

**Benefits:**
- ✅ Consistent video handling
- ✅ Easy to extend (add transcoding, compression)

---

#### **Step 2.6: Create Main MediaService**

**File:** `app/Services/Media/MediaService.php`
```php
<?php

namespace App\Services\Media;

use App\Contracts\MediaServiceInterface;
use App\DataTransferObjects\MediaUploadResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        protected MediaValidator $validator,
        protected ImageProcessor $imageProcessor,
        protected VideoProcessor $videoProcessor,
    ) {}

    /**
     * Upload and process photos
     *
     * @param array $photos Array of UploadedFile instances
     * @param string|null $context
     * @return MediaUploadResult
     */
    public function uploadPhotos(array $photos, ?string $context = null): MediaUploadResult
    {
        try {
            $this->validator->validatePhotoCount($photos);
        } catch (\InvalidArgumentException $e) {
            return MediaUploadResult::create([], [['message' => $e->getMessage()]]);
        }

        $storagePath = config('media.photos.storage_path');
        $successfulPaths = [];
        $failures = [];

        foreach ($photos as $index => $photo) {
            try {
                // Validate individual photo
                $this->validator->validatePhoto($photo);

                // Process and store
                $result = $this->imageProcessor->process($photo, $storagePath);
                $successfulPaths[] = $result['original'];

                Log::info('Photo uploaded successfully', [
                    'path' => $result['original'],
                    'context' => $context,
                ]);

            } catch (ValidationException $e) {
                $failures[] = [
                    'file' => $photo->getClientOriginalName(),
                    'message' => $e->getMessage(),
                    'index' => $index,
                ];

                Log::warning('Photo validation failed', [
                    'file' => $photo->getClientOriginalName(),
                    'errors' => $e->errors(),
                ]);

            } catch (\Exception $e) {
                $failures[] = [
                    'file' => $photo->getClientOriginalName(),
                    'message' => 'Upload failed: ' . $e->getMessage(),
                    'index' => $index,
                ];

                Log::error('Photo upload failed', [
                    'file' => $photo->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return MediaUploadResult::create($successfulPaths, $failures);
    }

    /**
     * Upload and process videos
     *
     * @param array $videos Array of UploadedFile instances
     * @param string|null $context
     * @return MediaUploadResult
     */
    public function uploadVideos(array $videos, ?string $context = null): MediaUploadResult
    {
        try {
            $this->validator->validateVideoCount($videos);
        } catch (\InvalidArgumentException $e) {
            return MediaUploadResult::create([], [['message' => $e->getMessage()]]);
        }

        $storagePath = config('media.videos.storage_path');
        $successfulPaths = [];
        $failures = [];

        foreach ($videos as $index => $video) {
            try {
                // Validate individual video
                $this->validator->validateVideo($video);

                // Process and store
                $path = $this->videoProcessor->process($video, $storagePath);
                $successfulPaths[] = $path;

                Log::info('Video uploaded successfully', [
                    'path' => $path,
                    'context' => $context,
                ]);

            } catch (ValidationException $e) {
                $failures[] = [
                    'file' => $video->getClientOriginalName(),
                    'message' => $e->getMessage(),
                    'index' => $index,
                ];

            } catch (\Exception $e) {
                $failures[] = [
                    'file' => $video->getClientOriginalName(),
                    'message' => 'Upload failed: ' . $e->getMessage(),
                    'index' => $index,
                ];

                Log::error('Video upload failed', [
                    'file' => $video->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return MediaUploadResult::create($successfulPaths, $failures);
    }

    /**
     * Delete media file
     *
     * @param string $path File path
     * @param string $type Type ('photo' or 'video')
     * @return bool
     */
    public function deleteMedia(string $path, string $type = 'photo'): bool
    {
        $disk = $type === 'photo'
            ? config('media.photos.storage_disk')
            : config('media.videos.storage_disk');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get media URL
     *
     * @param string $path File path
     * @return string
     */
    public function getMediaUrl(string $path): string
    {
        return asset('storage/' . $path);
    }
}
```

**Benefits:**
- ✅ Single responsibility
- ✅ Dependency injection
- ✅ Comprehensive error handling
- ✅ Logging for debugging

---

### **Phase 3: Update Service Provider** (15 minutes)

#### **Step 3.1: Register MediaService in AppServiceProvider**

**File:** `app/Providers/AppServiceProvider.php`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\MediaServiceInterface;
use App\Services\Media\MediaService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind MediaService to its interface
        $this->app->bind(MediaServiceInterface::class, MediaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

**Benefits:**
- ✅ Dependency injection works automatically
- ✅ Easy to swap implementations
- ✅ Testable with mocks

---

### **Phase 4: Refactor Controller** (30 minutes)

#### **Step 4.1: Update IncidentController**

**File:** `app/Http/Controllers/IncidentController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Services\IncidentService;
use App\Contracts\MediaServiceInterface;
use App\Http\Requests\StoreIncidentRequest;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    public function __construct(
        protected IncidentService $incidentService,
        protected MediaServiceInterface $mediaService,
    ) {}

    /**
     * Store a newly created incident
     */
    public function store(StoreIncidentRequest $request)
    {
        try {
            $validated = $request->validated();

            // Handle media uploads BEFORE creating incident
            if ($request->hasFile('photos')) {
                $photoResult = $this->mediaService->uploadPhotos(
                    $request->file('photos'),
                    'incident'
                );

                if (!$photoResult->isFullySuccessful()) {
                    Log::warning('Some photos failed to upload', [
                        'failures' => $photoResult->failures,
                    ]);
                }

                $validated['photos'] = $photoResult->paths;
            }

            if ($request->hasFile('videos')) {
                $videoResult = $this->mediaService->uploadVideos(
                    $request->file('videos'),
                    'incident'
                );

                $validated['videos'] = $videoResult->paths;
            }

            // Process license plates
            if (isset($validated['license_plates_input'])) {
                $validated['license_plates'] = array_map(
                    'trim',
                    explode(',', $validated['license_plates_input'])
                );
                unset($validated['license_plates_input']);
            }

            // Create incident using service
            $incident = $this->incidentService->createIncident($validated);

            return redirect()
                ->route('incidents.show', $incident)
                ->with('success', "Incident {$incident->incident_number} reported successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to create incident', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create incident: ' . $e->getMessage());
        }
    }
}
```

**Key Changes:**
- ✅ Inject MediaService via constructor
- ✅ Handle media uploads BEFORE creating incident
- ✅ Cleaner error handling
- ✅ Controller is now orchestrator, not implementor

---

### **Phase 5: Simplify IncidentService** (15 minutes)

#### **Step 5.1: Remove Media Processing from IncidentService**

**File:** `app/Services/IncidentService.php`
```php
<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Support\Facades\DB;

class IncidentService
{
    /**
     * Create incident with all related data
     *
     * @param array $data
     * @return Incident
     */
    public function createIncident(array $data): Incident
    {
        return DB::transaction(function () use ($data) {
            // Extract victims data
            $victimsData = $data['victims'] ?? [];
            unset($data['victims']);

            // Generate incident number
            $incidentNumber = Incident::generateIncidentNumber();

            // Create incident (photos and videos already processed and included in $data)
            $incident = Incident::create([
                ...$data,
                'incident_number' => $incidentNumber,
                'reported_by' => auth()->id(),
                'status' => 'pending',
                'photos' => $data['photos'] ?? [],
                'videos' => $data['videos'] ?? [],
                'casualty_count' => 0,
                'injury_count' => 0,
                'fatality_count' => 0,
            ]);

            // Create victims if any
            if (!empty($victimsData)) {
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
                ])
                ->log('Incident created');

            return $incident->load(['victims', 'assignedStaff', 'assignedVehicle']);
        });
    }

    // Remove processPhotos() and processVideos() methods
    // They're now handled by MediaService
}
```

**Key Changes:**
- ✅ Removed `processPhotos()` method
- ✅ Removed `processVideos()` method
- ✅ Service now focuses on incident business logic only
- ✅ Photos/videos passed as already-processed paths

---

### **Phase 6: Create Blade Component** (30 minutes)

#### **Step 6.1: Create MediaUpload Component Class**

**File:** `app/View/Components/MediaUpload.php`
```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MediaUpload extends Component
{
    public function __construct(
        public bool $required = true,
        public int $maxPhotos = null,
        public int $maxVideos = null,
        public bool $showVideos = true,
    ) {
        $this->maxPhotos = $maxPhotos ?? config('media.photos.max_count');
        $this->maxVideos = $maxVideos ?? config('media.videos.max_count');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.media-upload', [
            'photoConfig' => config('media.photos'),
            'videoConfig' => config('media.videos'),
        ]);
    }
}
```

---

#### **Step 6.2: Create Component Blade Template**

**File:** `resources/views/components/media-upload.blade.php`
```blade
{{-- Media Upload Component --}}
<div class="media-upload-component" x-data="mediaUploadComponent()">
    {{-- Photos Section --}}
    <div class="form-control mb-6">
        <label class="label">
            <span class="label-text font-medium">
                Photos
                @if($required)<span class="text-error">*</span>@endif
            </span>
            <span class="label-text-alt">Max {{ $maxPhotos }} photos, {{ $photoConfig['max_size'] / 1024 }}MB each</span>
        </label>

        <input type="file"
               name="photos[]"
               class="file-input file-input-bordered"
               accept="{{ implode(',', array_map(fn($ext) => '.' . $ext, $photoConfig['allowed_extensions'])) }}"
               multiple
               @if($required) required @endif
               x-on:change="handlePhotoUpload($event)">

        {{-- Photo Preview Grid --}}
        <div x-show="photos.length > 0" class="mt-4">
            <template x-for="(photo, index) in photos" :key="index">
                <div class="relative inline-block">
                    <img :src="photo.preview" class="w-24 h-24 object-cover rounded">
                    <button type="button"
                            @click="removePhoto(index)"
                            class="absolute -top-2 -right-2 btn btn-circle btn-xs btn-error">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
        </div>

        @error('photos')
            <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
        @enderror
    </div>

    {{-- Videos Section (Optional) --}}
    @if($showVideos)
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Videos (Optional)</span>
                <span class="label-text-alt">Max {{ $maxVideos }} videos, {{ $videoConfig['max_size'] / 1024 }}MB each</span>
            </label>

            <input type="file"
                   name="videos[]"
                   class="file-input file-input-bordered"
                   accept="{{ implode(',', array_map(fn($ext) => '.' . $ext, $videoConfig['allowed_extensions'])) }}"
                   multiple
                   x-on:change="handleVideoUpload($event)">

            @error('videos')
                <div class="label"><span class="label-text-alt text-error">{{ $message }}</span></div>
            @enderror
        </div>
    @endif
</div>

@push('scripts')
<script>
    function mediaUploadComponent() {
        return {
            photos: [],
            videos: [],

            handlePhotoUpload(event) {
                // Implement using Alpine.js
            },

            handleVideoUpload(event) {
                // Implement using Alpine.js
            },

            removePhoto(index) {
                this.photos.splice(index, 1);
            }
        };
    }
</script>
@endpush
```

**Benefits:**
- ✅ Reusable across entire app
- ✅ Props for customization
- ✅ Clean separation from business logic

---

### **Phase 7: Extract JavaScript to External File** (45 minutes)

#### **Step 7.1: Create External JS Module**

**File:** `resources/js/components/media-upload.js`
```javascript
/**
 * Media Upload Component
 * Handles photo and video uploads with preview and validation
 */

export class MediaUploadComponent {
    constructor(config = {}) {
        this.config = {
            maxPhotos: config.maxPhotos || 5,
            maxVideos: config.maxVideos || 2,
            maxPhotoSize: config.maxPhotoSize || 2 * 1024 * 1024, // 2MB
            maxVideoSize: config.maxVideoSize || 10 * 1024 * 1024, // 10MB
            ...config
        };

        this.state = {
            selectedPhotos: [],
            selectedVideos: []
        };
    }

    /**
     * Initialize component
     */
    init() {
        this.bindEvents();
    }

    /**
     * Bind DOM events
     */
    bindEvents() {
        const photoInput = document.querySelector('[name="photos[]"]');
        const videoInput = document.querySelector('[name="videos[]"]');

        if (photoInput) {
            photoInput.addEventListener('change', (e) => this.handlePhotoUpload(e));
        }

        if (videoInput) {
            videoInput.addEventListener('change', (e) => this.handleVideoUpload(e));
        }
    }

    /**
     * Handle photo uploads
     */
    async handlePhotoUpload(event) {
        const files = Array.from(event.target.files);

        // Validate
        if (!this.validatePhotos(files)) {
            event.target.value = '';
            return;
        }

        // Store and preview
        this.state.selectedPhotos = files;
        await this.renderPhotoPreviews(files);
    }

    /**
     * Validate photos
     */
    validatePhotos(files) {
        if (files.length > this.config.maxPhotos) {
            this.showError(`Maximum ${this.config.maxPhotos} photos allowed`);
            return false;
        }

        for (const file of files) {
            if (file.size > this.config.maxPhotoSize) {
                this.showError(`${file.name} is too large`);
                return false;
            }

            if (!file.type.startsWith('image/')) {
                this.showError(`${file.name} is not an image`);
                return false;
            }
        }

        return true;
    }

    /**
     * Render photo previews
     */
    async renderPhotoPreviews(files) {
        // Implementation using modern JS
    }

    /**
     * Show error message
     */
    showError(message) {
        // Use toast notification or alert
        console.error(message);
        alert(message);
    }
}

// Export for use in other modules
export default MediaUploadComponent;
```

**Benefits:**
- ✅ Modular, importable JavaScript
- ✅ ES6+ syntax
- ✅ Testable with Jest/Vitest
- ✅ Reusable across pages

---

#### **Step 7.2: Import in Main JS File**

**File:** `resources/js/app.js`
```javascript
import './bootstrap';
import { MediaUploadComponent } from './components/media-upload';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.media-upload-component')) {
        const mediaUpload = new MediaUploadComponent({
            maxPhotos: 5,
            maxVideos: 2
        });
        mediaUpload.init();
    }
});
```

---

### **Phase 8: Add Events & Listeners** (Optional, 30 minutes)

#### **Step 8.1: Create MediaUploaded Event**

**File:** `app/Events/MediaUploaded.php`
```php
<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $path,
        public string $type, // 'photo' or 'video'
        public ?string $context = null,
    ) {}
}
```

---

#### **Step 8.2: Create OptimizeUploadedImage Listener**

**File:** `app/Listeners/OptimizeUploadedImage.php`
```php
<?php

namespace App\Listeners;

use App\Events\MediaUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;

class OptimizeUploadedImage implements ShouldQueue
{
    public function handle(MediaUploaded $event): void
    {
        if ($event->type !== 'photo') {
            return;
        }

        // Queue image optimization
        // This runs asynchronously
    }
}
```

---

### **Phase 9: Add Unit Tests** (1 hour)

#### **Step 9.1: Test MediaService**

**File:** `tests/Unit/Services/MediaServiceTest.php`
```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Media\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaServiceTest extends TestCase
{
    protected MediaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->service = app(MediaService::class);
    }

    /** @test */
    public function it_can_upload_photos()
    {
        $photos = [
            UploadedFile::fake()->image('photo1.jpg', 600, 400),
            UploadedFile::fake()->image('photo2.jpg', 800, 600),
        ];

        $result = $this->service->uploadPhotos($photos);

        $this->assertTrue($result->isFullySuccessful());
        $this->assertEquals(2, $result->successCount);
        $this->assertEquals(0, $result->failureCount);
    }

    /** @test */
    public function it_rejects_oversized_photos()
    {
        $photo = UploadedFile::fake()->create('huge.jpg', 3000); // 3MB

        $result = $this->service->uploadPhotos([$photo]);

        $this->assertFalse($result->isFullySuccessful());
        $this->assertEquals(0, $result->successCount);
        $this->assertEquals(1, $result->failureCount);
    }
}
```

---

### **Phase 10: Update Usage in Views** (30 minutes)

#### **Step 10.1: Replace Old Component with New One**

**OLD (in create.blade.php):**
```blade
@include('Components.IncidentForm.MediaUpload')
```

**NEW:**
```blade
<x-media-upload :required="true" :show-videos="true" />
```

**Benefits:**
- ✅ One-line usage
- ✅ Props for customization
- ✅ Consistent across all forms

---

## 📊 **Benefits Summary**

### **Before Refactoring:**
- ❌ 495 lines of JS in Blade view
- ❌ PHP logic mixed with HTML
- ❌ Hard to test
- ❌ Not reusable
- ❌ No image optimization
- ❌ Validation scattered everywhere

### **After Refactoring:**
- ✅ Clean separation of concerns
- ✅ Reusable components
- ✅ Testable with unit tests
- ✅ Config-driven settings
- ✅ Image optimization built-in
- ✅ Event-driven architecture
- ✅ Single responsibility per class
- ✅ Easy to maintain and extend

---

## ⏱️ **Time Estimate**

| Phase | Time | Priority |
|-------|------|----------|
| Phase 1: Configuration | 30 min | 🔴 High |
| Phase 2: Service Layer | 2 hours | 🔴 High |
| Phase 3: Service Provider | 15 min | 🔴 High |
| Phase 4: Controller Refactor | 30 min | 🔴 High |
| Phase 5: Simplify IncidentService | 15 min | 🔴 High |
| Phase 6: Blade Component | 30 min | 🟡 Medium |
| Phase 7: External JavaScript | 45 min | 🟡 Medium |
| Phase 8: Events & Listeners | 30 min | 🟢 Low (Optional) |
| Phase 9: Unit Tests | 1 hour | 🟡 Medium |
| Phase 10: Update Views | 30 min | 🔴 High |

**Total Time:** ~6-7 hours

---

## 🚀 **Quick Start Implementation Order**

### **Day 1: Core Services (Priority 1)**
1. ✅ Create config/media.php
2. ✅ Create MediaServiceInterface
3. ✅ Create MediaUploadResult DTO
4. ✅ Create MediaValidator
5. ✅ Create ImageProcessor (basic version without Intervention)
6. ✅ Create VideoProcessor
7. ✅ Create MediaService
8. ✅ Register in AppServiceProvider
9. ✅ Update IncidentController
10. ✅ Simplify IncidentService

**Result:** Media uploads work with cleaner architecture

---

### **Day 2: Component & Frontend (Priority 2)**
1. ✅ Create MediaUpload Blade component
2. ✅ Extract JavaScript to external file
3. ✅ Update views to use new component
4. ✅ Test thoroughly

**Result:** Reusable component across app

---

### **Day 3: Polish & Tests (Priority 3)**
1. ✅ Add image optimization with Intervention/Image
2. ✅ Write unit tests
3. ✅ Add events & listeners (optional)
4. ✅ Documentation

**Result:** Production-ready, well-tested code

---

## 📦 **Required Packages**

### **For Image Optimization:**
```bash
composer require intervention/image
```

**Configure in config/media.php:**
```php
'image_driver' => env('MEDIA_IMAGE_DRIVER', 'gd'), // or 'imagick'
```

---

## ✅ **Acceptance Criteria**

### **Must Have:**
- [ ] Config file created with all settings
- [ ] MediaService implemented and registered
- [ ] Image validation working
- [ ] Photos store successfully
- [ ] Videos store successfully
- [ ] Controller simplified and clean
- [ ] IncidentService no longer handles media
- [ ] Blade component created and working
- [ ] Old inline JavaScript removed from views

### **Nice to Have:**
- [ ] Image optimization with Intervention/Image
- [ ] Thumbnail generation
- [ ] External JavaScript file
- [ ] Unit tests for MediaService
- [ ] Events & listeners
- [ ] Feature tests for upload flow

---

## 🎯 **Success Metrics**

1. **Code Quality:**
   - Reduced view file size by 80%
   - Each class < 200 lines
   - 100% test coverage for services

2. **Performance:**
   - Image optimization reduces file sizes by 50-70%
   - Thumbnails load instantly

3. **Maintainability:**
   - Easy to add new media types (documents, etc.)
   - Settings changed via config, not code
   - Clear separation of concerns

4. **Developer Experience:**
   - New developers understand architecture quickly
   - Easy to test and debug
   - Reusable across multiple models

---

## 📚 **Documentation Files to Create**

After implementation, create these docs:

1. **MEDIA_SERVICE_USAGE.md** - How to use MediaService in other features
2. **MEDIA_UPLOAD_COMPONENT.md** - Blade component props and examples
3. **MEDIA_CONFIG_REFERENCE.md** - All config options explained

---

**Created By:** Claude (Anthropic)
**Date:** October 24, 2025
**Status:** 📋 **Ready for Implementation**

