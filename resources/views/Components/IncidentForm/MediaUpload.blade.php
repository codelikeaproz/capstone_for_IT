{{--
    Media Upload Component
    Handles photo and video uploads with preview functionality

    Features:
    - Photo upload with preview (max 5 photos, 2MB each)
    - Video upload with preview (max 2 videos, 10MB each)
    - Real-time validation
    - Clear all functionality
--}}

<div class="mb-10">
    <h2 class="text-lg font-semibold text-base-content mb-1">Incident Media</h2>
    <p class="text-sm text-base-content/60 mb-6">Upload photos and videos of the incident</p>

    <div class="space-y-6">
        {{-- Photos Upload Section --}}
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
                onchange="MediaUploadHandler.handlePhotoUpload(this)"
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

            {{-- Photo Preview Section --}}
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
                                onclick="MediaUploadHandler.clearAllPhotos()"
                                class="btn btn-ghost btn-xs text-error gap-1"
                            >
                                <i class="fas fa-trash"></i>
                                <span>Clear All</span>
                            </button>
                        </div>
                    </div>
                    <div id="photo-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        {{-- Previews will be inserted here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Videos Upload Section --}}
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
                onchange="MediaUploadHandler.handleVideoUpload(this)"
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

            {{-- Video Preview Section --}}
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
                                onclick="MediaUploadHandler.clearAllVideos()"
                                class="btn btn-ghost btn-xs text-error gap-1"
                            >
                                <i class="fas fa-trash"></i>
                                <span>Clear All</span>
                            </button>
                        </div>
                    </div>
                    <div id="video-preview-grid" class="space-y-3">
                        {{-- Video previews will be inserted here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Media Upload Handling --}}
<script>
/**
 * Media Upload Handler
 * Manages photo and video uploads with validation and preview
 */
const MediaUploadHandler = {
    // Configuration
    config: {
        maxPhotos: 5,
        maxVideos: 2,
        maxPhotoSize: 2 * 1024 * 1024, // 2MB in bytes
        maxVideoSize: 10 * 1024 * 1024, // 10MB in bytes
        allowedPhotoTypes: ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'],
        allowedVideoTypes: ['video/mp4', 'video/webm', 'video/quicktime']
    },

    // State
    state: {
        selectedPhotos: [],
        selectedVideos: []
    },

    /**
     * Handle photo file selection
     * @param {HTMLInputElement} input - File input element
     */
    handlePhotoUpload(input) {
        const files = Array.from(input.files);

        // Validate file count
        if (files.length > this.config.maxPhotos) {
            this.showError(`You can only upload up to ${this.config.maxPhotos} photos`);
            input.value = '';
            return;
        }

        // Validate each file
        const validFiles = [];
        for (const file of files) {
            if (!this.validatePhoto(file)) {
                input.value = '';
                return;
            }
            validFiles.push(file);
        }

        // Store files and show previews
        this.state.selectedPhotos = validFiles;
        this.displayPhotopreviews(validFiles);
        this.updatePhotoCount(validFiles.length);
    },

    /**
     * Validate individual photo file
     * @param {File} file - Photo file to validate
     * @returns {boolean}
     */
    validatePhoto(file) {
        // Check file type
        if (!this.config.allowedPhotoTypes.includes(file.type)) {
            this.showError(`Invalid file type: ${file.name}. Only JPG, PNG, and GIF are allowed.`);
            return false;
        }

        // Check file size
        if (file.size > this.config.maxPhotoSize) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            this.showError(`File too large: ${file.name} (${sizeMB}MB). Maximum size is 2MB.`);
            return false;
        }

        return true;
    },

    /**
     * Display photo previews
     * @param {File[]} files - Array of photo files
     */
    displayPhotopreviews(files) {
        const container = document.getElementById('photo-preview-container');
        const grid = document.getElementById('photo-preview-grid');

        if (!container || !grid) return;

        // Clear existing previews
        grid.innerHTML = '';

        // Show container
        container.classList.remove('hidden');

        // Create preview for each file
        files.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = (e) => {
                const previewHtml = `
                    <div class="relative group">
                        <div class="aspect-square rounded-lg overflow-hidden border-2 border-base-300">
                            <img src="${e.target.result}"
                                 alt="Preview ${index + 1}"
                                 class="w-full h-full object-cover">
                        </div>
                        <button type="button"
                                onclick="MediaUploadHandler.removePhoto(${index})"
                                class="absolute -top-2 -right-2 btn btn-circle btn-xs btn-error opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Remove photo">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="absolute bottom-2 left-2 badge badge-sm badge-neutral">
                            ${index + 1}
                        </div>
                    </div>
                `;
                grid.insertAdjacentHTML('beforeend', previewHtml);
            };

            reader.readAsDataURL(file);
        });
    },

    /**
     * Remove a photo from selection
     * @param {number} index - Index of photo to remove
     */
    removePhoto(index) {
        this.state.selectedPhotos.splice(index, 1);

        // Update file input
        const input = document.getElementById('photo-input');
        const dt = new DataTransfer();
        this.state.selectedPhotos.forEach(file => dt.items.add(file));
        input.files = dt.files;

        // Refresh previews
        if (this.state.selectedPhotos.length > 0) {
            this.displayPhotopreviews(this.state.selectedPhotos);
            this.updatePhotoCount(this.state.selectedPhotos.length);
        } else {
            this.clearAllPhotos();
        }
    },

    /**
     * Clear all selected photos
     */
    clearAllPhotos() {
        this.state.selectedPhotos = [];
        document.getElementById('photo-input').value = '';
        document.getElementById('photo-preview-container').classList.add('hidden');
        document.getElementById('photo-preview-grid').innerHTML = '';
        this.updatePhotoCount(0);
    },

    /**
     * Update photo count display
     * @param {number} count - Number of photos
     */
    updatePhotoCount(count) {
        const countDisplay = document.getElementById('photo-count-display');
        const countElement = document.getElementById('photo-count');

        if (countElement) {
            countElement.textContent = count;
        }

        if (countDisplay) {
            if (count > 0) {
                countDisplay.textContent = `${count} photo${count !== 1 ? 's' : ''} selected`;
            } else {
                countDisplay.textContent = '';
            }
        }
    },

    /**
     * Handle video file selection
     * @param {HTMLInputElement} input - File input element
     */
    handleVideoUpload(input) {
        const files = Array.from(input.files);

        // Validate file count
        if (files.length > this.config.maxVideos) {
            this.showError(`You can only upload up to ${this.config.maxVideos} videos`);
            input.value = '';
            return;
        }

        // Validate each file
        const validFiles = [];
        for (const file of files) {
            if (!this.validateVideo(file)) {
                input.value = '';
                return;
            }
            validFiles.push(file);
        }

        // Store files and show previews
        this.state.selectedVideos = validFiles;
        this.displayVideoPreviews(validFiles);
        this.updateVideoCount(validFiles.length);
    },

    /**
     * Validate individual video file
     * @param {File} file - Video file to validate
     * @returns {boolean}
     */
    validateVideo(file) {
        // Check file type
        if (!this.config.allowedVideoTypes.includes(file.type)) {
            this.showError(`Invalid file type: ${file.name}. Only MP4, WebM, and MOV are allowed.`);
            return false;
        }

        // Check file size
        if (file.size > this.config.maxVideoSize) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            this.showError(`File too large: ${file.name} (${sizeMB}MB). Maximum size is 10MB.`);
            return false;
        }

        return true;
    },

    /**
     * Display video previews
     * @param {File[]} files - Array of video files
     */
    displayVideoPreviews(files) {
        const container = document.getElementById('video-preview-container');
        const grid = document.getElementById('video-preview-grid');

        if (!container || !grid) return;

        // Clear existing previews
        grid.innerHTML = '';

        // Show container
        container.classList.remove('hidden');

        // Create preview for each file
        files.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = (e) => {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const previewHtml = `
                    <div class="relative group border border-base-300 rounded-lg overflow-hidden">
                        <div class="flex items-center gap-3 p-3 bg-base-100">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-base-300 rounded flex items-center justify-center">
                                    <i class="fas fa-video text-2xl text-base-content/50"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate">${file.name}</p>
                                <p class="text-xs text-base-content/60">${sizeMB} MB</p>
                            </div>
                            <button type="button"
                                    onclick="MediaUploadHandler.removeVideo(${index})"
                                    class="btn btn-circle btn-xs btn-error"
                                    title="Remove video">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                grid.insertAdjacentHTML('beforeend', previewHtml);
            };

            reader.readAsDataURL(file);
        });
    },

    /**
     * Remove a video from selection
     * @param {number} index - Index of video to remove
     */
    removeVideo(index) {
        this.state.selectedVideos.splice(index, 1);

        // Update file input
        const input = document.getElementById('video-input');
        const dt = new DataTransfer();
        this.state.selectedVideos.forEach(file => dt.items.add(file));
        input.files = dt.files;

        // Refresh previews
        if (this.state.selectedVideos.length > 0) {
            this.displayVideoPreviews(this.state.selectedVideos);
            this.updateVideoCount(this.state.selectedVideos.length);
        } else {
            this.clearAllVideos();
        }
    },

    /**
     * Clear all selected videos
     */
    clearAllVideos() {
        this.state.selectedVideos = [];
        document.getElementById('video-input').value = '';
        document.getElementById('video-preview-container').classList.add('hidden');
        document.getElementById('video-preview-grid').innerHTML = '';
        this.updateVideoCount(0);
    },

    /**
     * Update video count display
     * @param {number} count - Number of videos
     */
    updateVideoCount(count) {
        const countDisplay = document.getElementById('video-count-display');
        const countElement = document.getElementById('video-count');

        if (countElement) {
            countElement.textContent = count;
        }

        if (countDisplay) {
            if (count > 0) {
                countDisplay.textContent = `${count} video${count !== 1 ? 's' : ''} selected`;
            } else {
                countDisplay.textContent = '';
            }
        }
    },

    /**
     * Show error message to user
     * @param {string} message - Error message
     */
    showError(message) {
        alert(message); // Simple alert for now, can be replaced with toast notification
        console.error('Media Upload Error:', message);
    }
};

// Expose functions to global scope for backward compatibility
window.handlePhotoUpload = MediaUploadHandler.handlePhotoUpload.bind(MediaUploadHandler);
window.clearAllPhotos = MediaUploadHandler.clearAllPhotos.bind(MediaUploadHandler);
window.handleVideoUpload = MediaUploadHandler.handleVideoUpload.bind(MediaUploadHandler);
window.clearAllVideos = MediaUploadHandler.clearAllVideos.bind(MediaUploadHandler);
</script>
