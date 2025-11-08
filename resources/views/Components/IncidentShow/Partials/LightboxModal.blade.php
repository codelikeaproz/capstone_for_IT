{{--
    Lightbox Modal Partial
    Modal for viewing full-size images
--}}

<dialog id="lightbox_modal" class="modal">
    <div class="modal-box max-w-5xl">
        {{-- Close Button --}}
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 z-10"
                    aria-label="Close lightbox">
                ✕
            </button>
        </form>

        {{-- Image Title --}}
        <h3 class="font-bold text-lg mb-4" id="lightbox_title">Photo</h3>

        {{-- Full Size Image --}}
        <div class="relative">
            <img id="lightbox_image"
                 src=""
                 alt="Full size"
                 class="w-full rounded-lg"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23fee2e2%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2216%22 fill=%22%23dc2626%22%3EImage Failed to Load%3C/text%3E%3C/svg%3E';">
        </div>

        {{-- Image Actions --}}
        <div class="mt-4 flex justify-end gap-2">
            <a id="lightbox_download"
               href=""
               download
               class="btn btn-sm btn-ghost gap-2"
               title="Download image">
                <i class="fas fa-download"></i>
                Download
            </a>
        </div>
    </div>

    {{-- Backdrop (click to close) --}}
    <form method="dialog" class="modal-backdrop">
        <button aria-label="Close lightbox">close</button>
    </form>
</dialog>

<script>
    // Update download link when lightbox opens
    document.addEventListener('DOMContentLoaded', function() {
        const lightboxImage = document.getElementById('lightbox_image');
        const lightboxDownload = document.getElementById('lightbox_download');

        if (lightboxImage && lightboxDownload) {
            // Update download link whenever image source changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'src') {
                        lightboxDownload.href = lightboxImage.src;
                    }
                });
            });

            observer.observe(lightboxImage, {
                attributes: true,
                attributeFilter: ['src']
            });
        }
    });
</script>
