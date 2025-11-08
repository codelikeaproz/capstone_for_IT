{{--
    Enhanced Media Gallery Component
    Displays photos and videos for an incident with improved error handling

    Props:
    - $incident: Incident model with photos and videos arrays
--}}

@php
    /**
     * Helper: Check if storage link exists
     * Laravel requires: php artisan storage:link
     * Note: On Windows, uses file_exists() which works for junctions/symlinks
     */
    function isStorageLinkConfigured(): bool {
        $storagePath = public_path('storage');
        // file_exists() works for symlinks, junctions, and directories on all platforms
        return file_exists($storagePath);
    }

    /**
     * Helper: Generate full URL for stored media
     * @param string $path - Relative path from storage/app/public
     * @return string
     */
    function getMediaUrl(string $path): string {
        return asset('storage/' . $path);
    }

    /**
     * Helper: Check if media file exists
     * @param string $path - Relative path from storage/app/public
     * @return bool
     */
    function mediaFileExists(string $path): bool {
        return \Storage::disk('public')->exists($path);
    }

    /**
     * Helper: Get fallback image SVG
     * @param string $message - Error message to display
     * @return string
     */
    function getFallbackImageSvg(string $message = 'Image Not Found'): string {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect fill='%23fee2e2' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='12' fill='%23dc2626'%3E" . urlencode($message) . "%3C/text%3E%3C/svg%3E";
    }

    // Check if we have any media to display
    $hasPhotos = is_array($incident->photos) && count($incident->photos) > 0;
    $hasVideos = is_array($incident->videos) && count($incident->videos) > 0;
    $hasMedia = $hasPhotos || $hasVideos;

    // Check storage configuration
    $storageLinkExists = isStorageLinkConfigured();
@endphp

@if($hasMedia)
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            {{-- Header --}}
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-photo-video text-purple-500"></i>
                Incident Media
            </h2>

            {{-- Storage Link Warning --}}
            @if(!$storageLinkExists)
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <h3 class="font-bold">Storage Link Not Configured</h3>
                        <div class="text-sm">
                            Media files cannot be displayed. Please run:
                            <code class="bg-base-300 px-2 py-1 rounded">php artisan storage:link</code>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Photos Section --}}
            @if($hasPhotos)
                @include('Components.IncidentShow.Partials.PhotoGallery', [
                    'photos' => $incident->photos,
                    'storageLinkExists' => $storageLinkExists
                ])
            @endif

            {{-- Videos Section --}}
            @if($hasVideos)
                @include('Components.IncidentShow.Partials.VideoGallery', [
                    'videos' => $incident->videos,
                    'storageLinkExists' => $storageLinkExists
                ])
            @endif
        </div>
    </div>

    {{-- Lightbox Modal --}}
    @include('Components.IncidentShow.Partials.LightboxModal')

    {{-- JavaScript for Media Gallery --}}
    <script>
        /**
         * Media Gallery Controller
         * Handles lightbox and debugging for incident media
         */
        const MediaGallery = {
            /**
             * Open image in lightbox modal
             * @param {string} imageSrc - Full URL to image
             * @param {string} title - Title to display
             */
            openLightbox(imageSrc, title) {
                const lightboxImage = document.getElementById('lightbox_image');
                const lightboxTitle = document.getElementById('lightbox_title');
                const lightboxModal = document.getElementById('lightbox_modal');

                if (lightboxImage && lightboxTitle && lightboxModal) {
                    lightboxImage.src = imageSrc;
                    lightboxTitle.textContent = title;
                    lightboxModal.showModal();
                } else {
                    console.error('Lightbox elements not found in DOM');
                }
            },

            /**
             * Initialize image load monitoring
             * Logs success/failure for debugging
             */
            initImageMonitoring() {
                const images = document.querySelectorAll('img[data-incident-photo]');

                console.group('📸 Incident Photos Loading Status');
                console.log(`Total photos to load: ${images.length}`);

                images.forEach((img, index) => {
                    const photoNumber = index + 1;

                    img.addEventListener('load', function() {
                        if (this.naturalWidth > 0) {
                            console.log(`✅ Photo ${photoNumber} loaded successfully`);
                            console.log(`   URL: ${this.src}`);
                            console.log(`   Dimensions: ${this.naturalWidth}x${this.naturalHeight}px`);
                        }
                    });

                    img.addEventListener('error', function() {
                        console.error(`❌ Photo ${photoNumber} failed to load`);
                        console.error(`   URL: ${this.src}`);
                        console.error(`   Possible causes:`);
                        console.error(`   - File does not exist at path`);
                        console.error(`   - Storage symlink not created (run: php artisan storage:link)`);
                        console.error(`   - Incorrect file permissions`);
                    });

                    // Check if already loaded/failed
                    if (img.complete) {
                        if (img.naturalWidth === 0) {
                            console.error(`❌ Photo ${photoNumber} failed (already attempted)`);
                            console.error(`   URL: ${img.src}`);
                        } else {
                            console.log(`✅ Photo ${photoNumber} already loaded`);
                        }
                    }
                });

                console.groupEnd();
            },

            /**
             * Check storage link configuration
             */
            checkStorageLink() {
                const storageLinkExists = {{ $storageLinkExists ? 'true' : 'false' }};

                if (!storageLinkExists) {
                    console.warn('⚠️ Storage symlink not detected!');
                    console.warn('   Run: php artisan storage:link');
                    console.warn('   This creates: public/storage -> storage/app/public');
                }
            }
        };

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            MediaGallery.checkStorageLink();
            MediaGallery.initImageMonitoring();
        });

        // Expose to global scope for onclick handlers
        window.openLightbox = MediaGallery.openLightbox.bind(MediaGallery);
    </script>
@endif
