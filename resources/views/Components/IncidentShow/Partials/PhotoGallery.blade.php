{{--
    Photo Gallery Partial
    Displays a grid of incident photos with lightbox functionality

    Props:
    - $photos: array - Array of photo paths
    - $storageLinkExists: bool - Whether storage symlink is configured
--}}

<div class="mb-6">
    <h3 class="font-semibold text-lg mb-3 flex items-center">
        <i class="fas fa-images text-green-500 mr-2"></i>
        Photos ({{ count($photos) }})
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($photos as $index => $photoPath)
            @php
                $photoUrl = asset('storage/' . $photoPath);
                $photoNumber = $index + 1;
                $photoTitle = "Photo {$photoNumber}";
                $fileExists = \Storage::disk('public')->exists($photoPath);
            @endphp

            <div class="group relative rounded-lg border border-gray-200 hover:border-blue-400 transition-all cursor-pointer bg-gray-100 overflow-hidden"
                 style="aspect-ratio: 1/1; min-height: 200px;"
                 title="{{ $fileExists ? 'Click to view full size' : 'Image file not found' }}"
                 onclick="openLightbox('{{ $photoUrl }}', '{{ $photoTitle }}')">

                <img src="{{ $photoUrl }}"
                     alt="Incident photo {{ $photoNumber }}"
                     data-incident-photo="{{ $photoNumber }}"
                     data-photo-path="{{ $photoPath }}"
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                     style="z-index: 1;"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23fee2e2%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2210%22 fill=%22%23dc2626%22%3EImage Failed%3C/text%3E%3Ctext x=%2250%25%22 y=%2265%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%228%22 fill=%22%23dc2626%22%3ECheck Console%3C/text%3E%3C/svg%3E';">

                {{-- Hover Overlay --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all"
                     style="z-index: 2; background-color: rgba(0, 0, 0, 0);"
                     onmouseenter="this.style.backgroundColor='rgba(0, 0, 0, 0.4)'"
                     onmouseleave="this.style.backgroundColor='rgba(0, 0, 0, 0)'">
                    <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                </div>

                {{-- Photo Number Badge --}}
                <div class="absolute top-2 left-2 badge badge-sm badge-neutral" style="z-index: 10;">
                    {{ $photoNumber }}
                </div>

                {{-- Warning Badge if file doesn't exist --}}
                @if(!$fileExists)
                    <div class="absolute top-2 right-2 badge badge-sm badge-error" style="z-index: 10;" title="File not found in storage">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                @endif

                {{-- Storage Link Warning Badge --}}
                @if(!$storageLinkExists)
                    <div class="absolute bottom-2 left-2 right-2 badge badge-sm badge-warning text-xs" style="z-index: 10;">
                        <i class="fas fa-unlink mr-1"></i>
                        Storage link missing
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Debug Info (only in development) --}}
    @if(config('app.debug'))
        <div class="mt-4 p-3 bg-base-200 rounded text-xs">
            <details>
                <summary class="cursor-pointer font-semibold">
                    <i class="fas fa-bug mr-1"></i>
                    Debug Info (Development Only)
                </summary>
                <div class="mt-2 space-y-1">
                    <div><strong>Storage Link:</strong> {{ $storageLinkExists ? '✅ Configured' : '❌ Missing' }}</div>
                    <div><strong>Total Photos:</strong> {{ count($photos) }}</div>
                    <div><strong>Storage Path:</strong> <code>storage/app/public/</code></div>
                    <div><strong>Public Path:</strong> <code>public/storage/</code></div>
                    @foreach($photos as $index => $path)
                        @php $exists = \Storage::disk('public')->exists($path); @endphp
                        <div class="pl-4">
                            Photo {{ $index + 1 }}: {{ $exists ? '✅' : '❌' }} <code>{{ $path }}</code>
                        </div>
                    @endforeach
                </div>
            </details>
        </div>
    @endif
</div>
