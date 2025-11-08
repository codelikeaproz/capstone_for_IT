{{--
    Video Gallery Partial
    Displays incident videos with playback controls

    Props:
    - $videos: array - Array of video paths
    - $storageLinkExists: bool - Whether storage symlink is configured
--}}

<div>
    <h3 class="font-semibold text-lg mb-3 flex items-center">
        <i class="fas fa-video text-red-500 mr-2"></i>
        Videos ({{ count($videos) }})
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($videos as $index => $videoPath)
            @php
                $videoUrl = asset('storage/' . $videoPath);
                $videoNumber = $index + 1;
                $fileExists = \Storage::disk('public')->exists($videoPath);
            @endphp

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                {{-- Video Player --}}
                <div class="relative bg-black">
                    <video controls
                           class="w-full"
                           preload="metadata"
                           data-incident-video="{{ $videoNumber }}"
                           data-video-path="{{ $videoPath }}"
                           onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-48 bg-red-50 text-red-600\'><div class=\'text-center\'><i class=\'fas fa-exclamation-triangle text-3xl mb-2\'></i><p>Video Failed to Load</p><p class=\'text-sm\'>Check console for details</p></div></div>';">
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        <source src="{{ $videoUrl }}" type="video/webm">
                        <source src="{{ $videoUrl }}" type="video/quicktime">
                        Your browser does not support the video tag.
                    </video>

                    {{-- Warning Overlay if storage link missing --}}
                    @if(!$storageLinkExists)
                        <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center pointer-events-none">
                            <div class="text-center text-white p-4">
                                <i class="fas fa-unlink text-3xl mb-2"></i>
                                <p class="font-semibold">Storage Link Missing</p>
                                <p class="text-sm">Run: php artisan storage:link</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Video Info Footer --}}
                <div class="p-3 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-video mr-2"></i>
                        <span class="font-medium">Video {{ $videoNumber }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- File Status Badge --}}
                        @if($fileExists)
                            <span class="badge badge-sm badge-success gap-1">
                                <i class="fas fa-check"></i>
                                Available
                            </span>
                        @else
                            <span class="badge badge-sm badge-error gap-1">
                                <i class="fas fa-times"></i>
                                Not Found
                            </span>
                        @endif

                        {{-- Download Button --}}
                        @if($fileExists && $storageLinkExists)
                            <a href="{{ $videoUrl }}"
                               download
                               class="btn btn-xs btn-ghost gap-1"
                               title="Download video">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                    </div>
                </div>
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
                    <div><strong>Total Videos:</strong> {{ count($videos) }}</div>
                    @foreach($videos as $index => $path)
                        @php $exists = \Storage::disk('public')->exists($path); @endphp
                        <div class="pl-4">
                            Video {{ $index + 1 }}: {{ $exists ? '✅' : '❌' }} <code>{{ $path }}</code>
                        </div>
                    @endforeach
                </div>
            </details>
        </div>
    @endif
</div>
