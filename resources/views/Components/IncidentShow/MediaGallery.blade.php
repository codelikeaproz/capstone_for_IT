{{-- Enhanced Media Gallery with Photos and Videos --}}
@if((is_array($incident->photos) && count($incident->photos) > 0) || (is_array($incident->videos) && count($incident->videos) > 0))
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-photo-video text-purple-500"></i>
                Incident Media
            </h2>

            {{-- Photos Section --}}
            @if(is_array($incident->photos) && count($incident->photos) > 0)
                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3 flex items-center">
                        <i class="fas fa-images text-green-500 mr-2"></i>
                        Photos ({{ count($incident->photos) }})
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($incident->photos as $index => $photo)
                            <div class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 hover:border-blue-400 transition-all cursor-pointer">
                                <img src="{{ asset('storage/' . $photo) }}"
                                     alt="Incident photo {{ $index + 1 }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onclick="openLightbox('{{ asset('storage/' . $photo) }}', 'Photo {{ $index + 1 }}')">

                                {{-- Overlay on hover --}}
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>

                                {{-- Photo Number Badge --}}
                                <div class="absolute top-2 left-2 badge badge-sm badge-neutral">{{ $index + 1 }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Videos Section --}}
            @if(is_array($incident->videos) && count($incident->videos) > 0)
                <div>
                    <h3 class="font-semibold text-lg mb-3 flex items-center">
                        <i class="fas fa-video text-red-500 mr-2"></i>
                        Videos ({{ count($incident->videos) }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($incident->videos as $index => $video)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <video controls class="w-full" preload="metadata">
                                    <source src="{{ asset('storage/' . $video) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="p-2 bg-gray-50 text-sm text-gray-600">
                                    <i class="fas fa-video mr-1"></i>Video {{ $index + 1 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Lightbox Modal for Photos --}}
    <dialog id="lightbox_modal" class="modal">
        <div class="modal-box max-w-5xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="font-bold text-lg mb-4" id="lightbox_title">Photo</h3>
            <img id="lightbox_image" src="" alt="Full size" class="w-full rounded-lg">
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function openLightbox(imageSrc, title) {
            document.getElementById('lightbox_image').src = imageSrc;
            document.getElementById('lightbox_title').textContent = title;
            document.getElementById('lightbox_modal').showModal();
        }
    </script>
@endif

