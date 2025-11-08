@extends("Layouts.app")

@section('title', 'Report New Incident')

@section('content')
<div class="min-h-screen bg-base-200 py-8" role="main">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <header class="mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-error" aria-hidden="true"></i>
                        <span>Report New Incident</span>
                    </h1>
                    <p class="mt-2 text-base text-gray-600 leading-relaxed">
                        Complete the form below to report a new incident. Fields marked with <span class="text-error font-semibold">*</span> are required.
                    </p>
                </div>
                <a href="{{ route('incidents.index') }}"
                   class="btn btn-outline gap-2 w-full sm:w-auto min-h-[44px]"
                   aria-label="Back to incidents list">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    <span>Back</span>
                </a>
            </div>
        </header>

        <!-- Validation Errors Display -->
        @include('Components.ValidationErrors')

        <!-- Main Form -->
        <form action="{{ route('incidents.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white rounded-lg shadow-lg p-6 md:p-8 space-y-8"
              aria-label="New incident report form">
            @csrf

            {{-- Step 1: Basic Information --}}
            @include('Components.IncidentForm.BasicInformation')

            {{-- Step 2: Incident Type-Specific Fields (Conditional) --}}
            @include('Components.IncidentForm.TrafficAccidentFields')
            @include('Components.IncidentForm.MedicalEmergencyFields')
            @include('Components.IncidentForm.FireIncidentFields')
            @include('Components.IncidentForm.NaturalDisasterFields')
            @include('Components.IncidentForm.CriminalActivityFields')

            {{-- Step 3: Victim/Patient Management --}}
            @include('Components.IncidentForm.VictimInlineManagement')

            {{-- Step 4: Media Upload --}}
            @include('Components.IncidentForm.MediaUpload')

            {{-- Environmental Conditions (For applicable incident types) --}}
            <section id="environmental-conditions-section" style="display: none;" aria-labelledby="env-conditions-heading">
                <div class="border-t border-base-300 pt-6">
                    <h2 id="env-conditions-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-cloud-sun text-info" aria-hidden="true"></i>
                        <span>Environmental Conditions</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Weather conditions at the time of incident</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="weather-condition" class="label">
                                <span class="label-text font-semibold text-gray-700">Weather Condition</span>
                            </label>
                            <select name="weather_condition"
                                    id="weather-condition"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                <option value="">Select weather condition</option>
                                <option value="clear" {{ old('weather_condition') == 'clear' ? 'selected' : '' }}>Clear</option>
                                <option value="cloudy" {{ old('weather_condition') == 'cloudy' ? 'selected' : '' }}>Cloudy</option>
                                <option value="rainy" {{ old('weather_condition') == 'rainy' ? 'selected' : '' }}>Rainy</option>
                                <option value="stormy" {{ old('weather_condition') == 'stormy' ? 'selected' : '' }}>Stormy</option>
                                <option value="foggy" {{ old('weather_condition') == 'foggy' ? 'selected' : '' }}>Foggy</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>


            {{-- Step 5: Assignment --}}
            @include('Components.IncidentForm.AssignmentFields')

            <!-- Form Actions -->
            <div class="border-t border-base-300 pt-8 mt-8">
                <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3">
                    <a href="{{ route('incidents.index') }}"
                       class="btn btn-outline w-full sm:w-auto gap-2 min-h-[44px]"
                       aria-label="Cancel and return to incidents list">
                        <i class="fas fa-times" aria-hidden="true"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit"
                            class="btn btn-primary w-full sm:w-auto gap-2 min-h-[44px]"
                            aria-label="Submit incident report">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        <span>Submit Incident Report</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// MAIN INCIDENT TYPE HANDLER
// ============================================
function handleIncidentTypeChange(incidentType) {
    console.log('Incident type changed to:', incidentType);

    // Hide all incident-specific sections
    const sections = document.querySelectorAll('[data-incident-type]');
    sections.forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected incident type section
    if (incidentType) {
        const selectedSection = document.querySelector(`[data-incident-type="${incidentType}"]`);
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }

        // Show environmental conditions for applicable types
        const envSection = document.getElementById('environmental-conditions-section');
        if (['traffic_accident', 'natural_disaster'].includes(incidentType)) {
            if (envSection) envSection.style.display = 'block';
        } else {
            if (envSection) envSection.style.display = 'none';
        }
    }
}

// ============================================
// LOCATION FUNCTIONALITY
// ============================================
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude.toFixed(8);
            document.querySelector('input[name="longitude"]').value = position.coords.longitude.toFixed(8);
            showSuccessToast('Location captured successfully!');
        }, function(error) {
            showErrorToast('Failed to get location: ' + error.message);
        });
    } else {
        showErrorToast('Geolocation is not supported by this browser.');
    }
}

// ============================================
// FILE UPLOAD WITH VALIDATION & PREVIEW
// ============================================
const MAX_PHOTOS = 5;
const MAX_VIDEOS = 2;
const MAX_PHOTO_SIZE = 2 * 1024 * 1024; // 2MB
const MAX_VIDEO_SIZE = 10 * 1024 * 1024; // 10MB

// Store files in arrays for manipulation
let photoFiles = [];
let videoFiles = [];

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

// Update file input with current files
function updatePhotoInput() {
    const input = document.getElementById('photo-input');
    const dataTransfer = new DataTransfer();

    photoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

function updateVideoInput() {
    const input = document.getElementById('video-input');
    const dataTransfer = new DataTransfer();

    videoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

// Handle photo upload with validation
function handlePhotoUpload(input) {
    const files = Array.from(input.files);

    console.log('Files selected:', files.length);

    // Validate file count
    if (files.length > MAX_PHOTOS) {
        showErrorToast(`Maximum ${MAX_PHOTOS} photos allowed. You selected ${files.length}.`);
        input.value = '';
        return;
    }

    // Validate each file
    photoFiles = [];
    let hasErrors = false;

    for (let file of files) {
        console.log('Processing file:', file.name, 'Type:', file.type, 'Size:', file.size);

        // Check size
        if (file.size > MAX_PHOTO_SIZE) {
            showErrorToast(`${file.name} exceeds 2MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        if (!file.type.startsWith('image/')) {
            showErrorToast(`${file.name} is not a valid image file`);
            hasErrors = true;
            continue;
        }

        photoFiles.push(file);
    }

    console.log('Valid files:', photoFiles.length);

    if (hasErrors && photoFiles.length === 0) {
        input.value = '';
        hidePhotoPreview();
        return;
    }

    // Update input with valid files
    updatePhotoInput();

    // Render previews
    renderPhotoPreview();
}

// Render photo preview with proper error handling
function renderPhotoPreview() {
    const container = document.getElementById('photo-preview-container');
    const grid = document.getElementById('photo-preview-grid');
    const countDisplay = document.getElementById('photo-count');
    const textDisplay = document.getElementById('photo-count-display');

    console.log('Rendering preview for', photoFiles.length, 'files');

    if (photoFiles.length === 0) {
        hidePhotoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = photoFiles.length;
    textDisplay.textContent = `${photoFiles.length} file${photoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Show container
    container.classList.remove('hidden');

    // Generate previews
    photoFiles.forEach((file, index) => {
        console.log(`Reading file ${index + 1}:`, file.name);
        const reader = new FileReader();

        reader.onload = function(e) {
            const imageUrl = e.target.result;
            console.log(`File ${file.name} loaded, URL length:`, imageUrl ? imageUrl.length : 0);

            // Create preview card
            const previewCard = document.createElement('div');
            previewCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 group';

            // Build the HTML structure
            previewCard.innerHTML = `
                <div class="relative bg-base-200" style="padding-top: 100%; position: relative;">
                    <img
                        src="${imageUrl}"
                        alt="${file.name}"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        onload="console.log('Image loaded:', '${file.name}')"
                        onerror="console.error('Image failed to load:', '${file.name}')"
                    >
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0); transition: background-color 0.3s; display: flex; align-items: center; justify-content: center;" class="group-hover:bg-opacity-40 group-hover:bg-black">
                        <button
                            type="button"
                            onclick="removePhoto(${index})"
                            class="btn btn-circle btn-sm btn-error opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            title="Remove photo"
                            style="z-index: 10;"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-2">
                    <p class="text-xs text-base-content truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
            `;

            grid.appendChild(previewCard);
            console.log(`Preview card added for ${file.name}`);
        };

        reader.onerror = function(error) {
            console.error('Error reading file:', file.name, error);
            showErrorToast(`Failed to read ${file.name}`);
        };

        // Start reading the file
        reader.readAsDataURL(file);
    });
}

// Hide photo preview
function hidePhotoPreview() {
    const container = document.getElementById('photo-preview-container');
    const textDisplay = document.getElementById('photo-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual photo
function removePhoto(index) {
    photoFiles.splice(index, 1);
    updatePhotoInput();
    renderPhotoPreview();
    showSuccessToast('Photo removed successfully');
}

// Clear all photos
function clearAllPhotos() {
    if (confirm('Are you sure you want to remove all photos?')) {
        photoFiles = [];
        document.getElementById('photo-input').value = '';
        hidePhotoPreview();
        showSuccessToast('All photos cleared');
    }
}

// Handle video upload with validation
function handleVideoUpload(input) {
    const files = Array.from(input.files);

    // Validate file count
    if (files.length > MAX_VIDEOS) {
        showErrorToast(`Maximum ${MAX_VIDEOS} videos allowed. You selected ${files.length}.`);
        input.value = '';
        return;
    }

    // Validate each file
    videoFiles = [];
    let hasErrors = false;

    for (let file of files) {
        // Check size
        if (file.size > MAX_VIDEO_SIZE) {
            showErrorToast(`${file.name} exceeds 10MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        const validVideoTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        if (!validVideoTypes.includes(file.type.toLowerCase())) {
            showErrorToast(`${file.name} is not a valid video file`);
            hasErrors = true;
            continue;
        }

        videoFiles.push(file);
    }

    if (hasErrors && videoFiles.length === 0) {
        input.value = '';
        hideVideoPreview();
        return;
    }

    // Update input with valid files
    updateVideoInput();

    // Render previews
    renderVideoPreview();
}

// Render video preview
function renderVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const grid = document.getElementById('video-preview-grid');
    const countDisplay = document.getElementById('video-count');
    const textDisplay = document.getElementById('video-count-display');

    if (videoFiles.length === 0) {
        hideVideoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = videoFiles.length;
    textDisplay.textContent = `${videoFiles.length} file${videoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Generate video previews
    videoFiles.forEach((file, index) => {
        const videoCard = document.createElement('div');
        videoCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 p-3';
        videoCard.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-video text-3xl text-secondary"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-base-content truncate font-medium" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
                <button
                    type="button"
                    onclick="removeVideo(${index})"
                    class="btn btn-circle btn-sm btn-error flex-shrink-0"
                    title="Remove video"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        grid.appendChild(videoCard);
    });

    container.classList.remove('hidden');
}

// Hide video preview
function hideVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const textDisplay = document.getElementById('video-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual video
function removeVideo(index) {
    videoFiles.splice(index, 1);
    updateVideoInput();
    renderVideoPreview();
    showSuccessToast('Video removed successfully');
}

// Clear all videos
function clearAllVideos() {
    if (confirm('Are you sure you want to remove all videos?')) {
        videoFiles = [];
        document.getElementById('video-input').value = '';
        hideVideoPreview();
        showSuccessToast('All videos cleared');
    }
}

// Handle video upload with validation
function handleVideoUpload(input) {
    const files = Array.from(input.files);

    // Validate file count
    if (files.length > MAX_VIDEOS) {
        showErrorToast(`Maximum ${MAX_VIDEOS} videos allowed. You selected ${files.length}.`);
        input.value = ''; // Clear selection
        return;
    }

    // Validate each file
    videoFiles = []; // Reset
    let hasErrors = false;

    for (let file of files) {
        // Check size
        if (file.size > MAX_VIDEO_SIZE) {
            showErrorToast(`${file.name} exceeds 10MB limit (${formatFileSize(file.size)})`);
            hasErrors = true;
            continue;
        }

        // Check type
        if (!file.type.match('video.*')) {
            showErrorToast(`${file.name} is not a valid video file`);
            hasErrors = true;
            continue;
        }

        videoFiles.push(file);
    }

    if (hasErrors && videoFiles.length === 0) {
        input.value = ''; // Clear all if none valid
        hideVideoPreview();
        return;
    }

    // Update input with valid files
    updateVideoInput();

    // Render previews
    renderVideoPreview();
}

// Render video preview
function renderVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const grid = document.getElementById('video-preview-grid');
    const countDisplay = document.getElementById('video-count');
    const textDisplay = document.getElementById('video-count-display');

    if (videoFiles.length === 0) {
        hideVideoPreview();
        return;
    }

    // Show count
    countDisplay.textContent = videoFiles.length;
    textDisplay.textContent = `${videoFiles.length} file${videoFiles.length > 1 ? 's' : ''} selected`;

    // Clear previous previews
    grid.innerHTML = '';

    // Generate video previews
    videoFiles.forEach((file, index) => {
        const videoCard = document.createElement('div');
        videoCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 p-3';
        videoCard.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-video text-3xl text-secondary"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-base-content truncate font-medium" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                </div>
                <button
                    type="button"
                    onclick="removeVideo(${index})"
                    class="btn btn-circle btn-sm btn-error flex-shrink-0"
                    title="Remove video"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        grid.appendChild(videoCard);
    });

    container.classList.remove('hidden');
}

// Hide video preview
function hideVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const textDisplay = document.getElementById('video-count-display');
    container.classList.add('hidden');
    textDisplay.textContent = '';
}

// Remove individual video
function removeVideo(index) {
    videoFiles.splice(index, 1);
    updateVideoInput();
    renderVideoPreview();
    showSuccessToast('Video removed successfully');
}

// Clear all videos
function clearAllVideos() {
    if (confirm('Are you sure you want to remove all videos?')) {
        videoFiles = [];
        document.getElementById('video-input').value = '';
        hideVideoPreview();
        showSuccessToast('All videos cleared');
    }
}

// ============================================
// DYNAMIC BARANGAY LOADING & INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const municipalitySelect = document.getElementById('municipality-select');
    const barangaySelect = document.getElementById('barangay-select');
    const incidentTypeSelect = document.getElementById('incident_type');

    // Load barangays when municipality changes
    if (municipalitySelect && barangaySelect) {
    municipalitySelect.addEventListener('change', function() {
        const municipality = this.value;

        // Reset barangay select
        barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
        barangaySelect.disabled = true;

        if (!municipality) {
            barangaySelect.innerHTML = '<option value="">Select municipality first</option>';
            return;
        }

        // Fetch barangays for selected municipality
        fetch(`{{ route('api.barangays') }}?municipality=${encodeURIComponent(municipality)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.barangays) {
                    // Clear and populate barangay select
                    barangaySelect.innerHTML = '<option value="">Select barangay</option>';

                    data.barangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;

                        // Restore old value if exists
                        if ('{{ old("barangay") }}' === barangay) {
                            option.selected = true;
                        }

                        barangaySelect.appendChild(option);
                    });

                    barangaySelect.disabled = false;
                } else {
                    barangaySelect.innerHTML = '<option value="">No barangays found</option>';
                }
            })
            .catch(error => {
                console.error('Error loading barangays:', error);
                barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
                showErrorToast('Failed to load barangays. Please try again.');
            });
    });

    // Trigger change event if municipality is pre-selected (for old values)
    if (municipalitySelect.value) {
        municipalitySelect.dispatchEvent(new Event('change'));
    }
    }

    // Initialize incident type display on page load
    if (incidentTypeSelect) {
        incidentTypeSelect.addEventListener('change', function() {
            handleIncidentTypeChange(this.value);
        });

        // Initialize on page load if there's a value
        if (incidentTypeSelect.value) {
            handleIncidentTypeChange(incidentTypeSelect.value);
        }
    }
});

// ============================================
// TOAST NOTIFICATIONS
// ============================================
function showSuccessToast(message) {
    console.log('Success:', message);
    // You can integrate with toast library here
}

function showErrorToast(message) {
    console.error('Error:', message);
    alert(message);
}
</script>
@endpush
@endsection
