{{-- Media Upload Section --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-base-content mb-1">Incident Media</h2>
    <p class="text-sm text-base-content/60 mb-6">Upload photos and videos of the incident</p>

    <div class="space-y-6">
        <!-- Photos Upload Section -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Photos <span class="text-error">*</span></span>
                <span class="label-text-alt text-base-content/60">Max 5 photos, 2MB each</span>
            </label>

            <input
                type="file"
                name="photos[]"
                id="photo-input"
                class="file-input file-input-bordered w-full focus:outline-primary @error('photos') file-input-error @enderror"
                accept="image/jpeg,image/png,image/jpg,image/gif"
                multiple
                required
                onchange="handlePhotoUpload(this)"
            >

            <div class="label">
                <span class="label-text-alt text-base-content/60">
                    <i class="fas fa-info-circle mr-1"></i>
                    Supported: JPG, PNG, GIF
                </span>
                <span id="photo-count-display" class="label-text-alt text-primary font-medium"></span>
            </div>

            @error('photos')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
            @error('photos.*')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror

            <!-- Photo Preview Section -->
            <div id="photo-preview-container" class="mt-4 hidden">
                <div class="bg-base-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-base-content">
                            Uploaded Images
                        </h3>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-base-content/60">
                                <span id="photo-count">0</span>/5 photos
                            </span>
                            <button
                                type="button"
                                onclick="clearAllPhotos()"
                                class="btn btn-ghost btn-xs text-error gap-1"
                            >
                                <i class="fas fa-trash"></i>
                                <span>Clear All</span>
                            </button>
                        </div>
                    </div>
                    <div id="photo-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        <!-- Previews will be inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Videos Upload Section -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Videos (Optional)</span>
                <span class="label-text-alt text-base-content/60">Max 2 videos, 10MB each</span>
            </label>

            <input
                type="file"
                name="videos[]"
                id="video-input"
                class="file-input file-input-bordered w-full focus:outline-primary @error('videos') file-input-error @enderror"
                accept="video/mp4,video/webm,video/quicktime"
                multiple
                onchange="handleVideoUpload(this)"
            >

            <div class="label">
                <span class="label-text-alt text-base-content/60">
                    <i class="fas fa-info-circle mr-1"></i>
                    Supported: MP4, WebM, MOV
                </span>
                <span id="video-count-display" class="label-text-alt text-secondary font-medium"></span>
            </div>

            @error('videos')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror
            @error('videos.*')
                <label class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </label>
            @enderror

            <!-- Video Preview Section -->
            <div id="video-preview-container" class="mt-4 hidden">
                <div class="bg-base-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-base-content">
                            Uploaded Videos
                        </h3>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-base-content/60">
                                <span id="video-count">0</span>/2 videos
                            </span>
                            <button
                                type="button"
                                onclick="clearAllVideos()"
                                class="btn btn-ghost btn-xs text-error gap-1"
                            >
                                <i class="fas fa-trash"></i>
                                <span>Clear All</span>
                            </button>
                        </div>
                    </div>
                    <div id="video-preview-grid" class="space-y-3">
                        <!-- Video previews will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

