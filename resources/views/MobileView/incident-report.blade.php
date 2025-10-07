<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Incident Report - BukidnonAlert</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .bg-brick-orange { background-color: #c14a09; }
        .text-brick-orange { color: #c14a09; }
        
        /* Mobile optimizations */
        .mobile-form {
            padding: 1rem;
            max-width: 100%;
        }
        
        .form-section {
            background: white;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .location-display {
            background: #f0f9ff;
            border: 2px dashed #3b82f6;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
        }
        
        .photo-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.5rem;
            margin: 0.5rem 0;
        }
        
        .severity-selector .btn {
            min-height: 60px;
            margin: 0.25rem;
        }
        
        /* Touch-friendly controls */
        .form-control, .select, .textarea {
            min-height: 48px;
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0;
            }
            
            .severity-selector {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="navbar bg-white shadow-lg">
        <div class="navbar-start">
            <a href="{{ route('responder.dashboard') }}" class="btn btn-ghost">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="navbar-center">
            <h1 class="text-lg font-bold">Quick Incident Report</h1>
        </div>
        <div class="navbar-end">
            <button onclick="saveDraft()" class="btn btn-ghost btn-sm">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    <form id="mobile-incident-form" class="mobile-form" onsubmit="submitIncident(event)">
        @csrf
        
        <!-- Critical Information Section -->
        <div class="form-section">
            <h2 class="text-lg font-semibold mb-3 text-red-600">
                <i class="fas fa-exclamation-triangle mr-2"></i>Critical Information
            </h2>
            
            <!-- Severity Level -->
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-medium">Severity Level *</span>
                </label>
                <div class="severity-selector">
                    <button type="button" class="btn btn-error" onclick="setSeverity('critical', this)">
                        <i class="fas fa-skull-crossbones mr-2"></i>Critical
                    </button>
                    <button type="button" class="btn btn-warning" onclick="setSeverity('high', this)">
                        <i class="fas fa-exclamation-triangle mr-2"></i>High
                    </button>
                    <button type="button" class="btn btn-info" onclick="setSeverity('medium', this)">
                        <i class="fas fa-info-circle mr-2"></i>Medium
                    </button>
                    <button type="button" class="btn btn-success" onclick="setSeverity('low', this)">
                        <i class="fas fa-check-circle mr-2"></i>Low
                    </button>
                </div>
                <input type="hidden" name="severity_level" id="severity_level" required>
            </div>

            <!-- Incident Type -->
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-medium">Incident Type *</span>
                </label>
                <select name="incident_type" class="select select-bordered w-full" required>
                    <option value="">Select incident type</option>
                    <option value="fire">🔥 Fire Emergency</option>
                    <option value="medical">🚑 Medical Emergency</option>
                    <option value="accident">🚗 Traffic Accident</option>
                    <option value="natural_disaster">🌪️ Natural Disaster</option>
                    <option value="crime">👮 Crime/Security</option>
                    <option value="rescue">⛑️ Search & Rescue</option>
                    <option value="hazmat">☢️ Hazardous Material</option>
                    <option value="other">📋 Other Emergency</option>
                </select>
            </div>

            <!-- Quick Description -->
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-medium">Quick Description *</span>
                </label>
                <textarea name="description" class="textarea textarea-bordered h-20" 
                          placeholder="Brief description of the incident..." required></textarea>
            </div>
        </div>

        <!-- Location Section -->
        <div class="form-section">
            <h2 class="text-lg font-semibold mb-3 text-blue-600">
                <i class="fas fa-map-marker-alt mr-2"></i>Location
            </h2>
            
            <div class="location-display mb-4" id="location-display">
                <i class="fas fa-crosshairs text-2xl text-blue-500 mb-2"></i>
                <p class="text-sm">Getting your location...</p>
                <button type="button" onclick="getCurrentLocation()" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-location-arrow mr-1"></i>Get GPS Location
                </button>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Address/Landmark</span>
                </label>
                <input type="text" name="location" class="input input-bordered" 
                       placeholder="Street address or landmark">
            </div>
        </div>

        <!-- Casualties Section -->
        <div class="form-section">
            <h2 class="text-lg font-semibold mb-3 text-red-600">
                <i class="fas fa-user-injured mr-2"></i>Casualties
            </h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Injured</span>
                    </label>
                    <input type="number" name="total_injured" class="input input-bordered" 
                           value="0" min="0" max="999">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Fatalities</span>
                    </label>
                    <input type="number" name="total_fatalities" class="input input-bordered" 
                           value="0" min="0" max="999">
                </div>
            </div>
        </div>

        <!-- Photos Section -->
        <div class="form-section">
            <h2 class="text-lg font-semibold mb-3 text-green-600">
                <i class="fas fa-camera mr-2"></i>Photos (Optional)
            </h2>
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Capture Photos</span>
                </label>
                <input type="file" accept="image/*" capture="environment" multiple 
                       class="file-input file-input-bordered w-full" 
                       onchange="previewPhotos(this)">
            </div>
            
            <div id="photo-preview" class="mt-2"></div>
        </div>

        <!-- Quick Actions -->
        <div class="form-section">
            <h2 class="text-lg font-semibold mb-3 text-purple-600">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h2>
            
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="requestBackup()" class="btn btn-sm btn-warning">
                    <i class="fas fa-users mr-1"></i>Request Backup
                </button>
                <button type="button" onclick="requestAmbulance()" class="btn btn-sm btn-error">
                    <i class="fas fa-ambulance mr-1"></i>Call Ambulance
                </button>
                <button type="button" onclick="requestFireDept()" class="btn btn-sm btn-orange">
                    <i class="fas fa-fire-extinguisher mr-1"></i>Fire Department
                </button>
                <button type="button" onclick="notifyPolice()" class="btn btn-sm btn-info">
                    <i class="fas fa-shield-alt mr-1"></i>Police
                </button>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="form-section">
            <div class="flex flex-col space-y-3">
                <button type="submit" class="btn btn-primary btn-lg w-full">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Report
                </button>
                <button type="button" onclick="saveDraft()" class="btn btn-outline btn-sm w-full">
                    <i class="fas fa-save mr-2"></i>Save as Draft
                </button>
            </div>
        </div>
    </form>

    <!-- Loading Modal -->
    <div class="modal" id="loading-modal">
        <div class="modal-box text-center">
            <div class="loading loading-spinner loading-lg text-primary"></div>
            <p class="mt-4">Submitting incident report...</p>
        </div>
    </div>

    <script>
        let currentLocation = null;

        // GPS Location Functions
        function getCurrentLocation() {
            const display = document.getElementById('location-display');
            display.innerHTML = '<div class="loading loading-spinner text-primary"></div><p class="text-sm mt-2">Getting GPS location...</p>';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        currentLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        };

                        document.getElementById('latitude').value = currentLocation.lat;
                        document.getElementById('longitude').value = currentLocation.lng;

                        display.innerHTML = `
                            <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
                            <p class="text-sm font-medium">Location Captured</p>
                            <p class="text-xs text-gray-600">Lat: ${currentLocation.lat.toFixed(6)}</p>
                            <p class="text-xs text-gray-600">Lng: ${currentLocation.lng.toFixed(6)}</p>
                            <p class="text-xs text-gray-500">Accuracy: ±${Math.round(currentLocation.accuracy)}m</p>
                        `;
                    },
                    function(error) {
                        display.innerHTML = `
                            <i class="fas fa-exclamation-triangle text-2xl text-red-500 mb-2"></i>
                            <p class="text-sm text-red-600">Location access denied</p>
                            <button type="button" onclick="getCurrentLocation()" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-location-arrow mr-1"></i>Try Again
                            </button>
                        `;
                    }
                );
            } else {
                display.innerHTML = `
                    <i class="fas fa-times-circle text-2xl text-red-500 mb-2"></i>
                    <p class="text-sm text-red-600">GPS not supported</p>
                `;
            }
        }

        // Severity Selection
        function setSeverity(level, button) {
            // Remove active state from all buttons
            document.querySelectorAll('.severity-selector .btn').forEach(btn => {
                btn.classList.remove('btn-active');
            });
            
            // Add active state to selected button
            button.classList.add('btn-active');
            document.getElementById('severity_level').value = level;
        }

        // Photo Preview
        function previewPhotos(input) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'photo-preview';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Quick Action Functions
        function requestBackup() {
            if (confirm('Request backup units to your location?')) {
                // Implementation for requesting backup
                alert('Backup request sent to dispatch');
            }
        }

        function requestAmbulance() {
            if (confirm('Request ambulance to the incident location?')) {
                // Implementation for requesting ambulance
                alert('Ambulance requested');
            }
        }

        function requestFireDept() {
            if (confirm('Contact fire department for this incident?')) {
                // Implementation for fire department request
                alert('Fire department notified');
            }
        }

        function notifyPolice() {
            if (confirm('Notify police about this incident?')) {
                // Implementation for police notification
                alert('Police notified');
            }
        }

        // Form Submission
        function submitIncident(event) {
            event.preventDefault();
            
            const modal = document.getElementById('loading-modal');
            modal.classList.add('modal-open');

            const formData = new FormData(event.target);
            
            fetch('{{ route("incidents.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                modal.classList.remove('modal-open');
                if (data.success) {
                    alert('Incident reported successfully!');
                    window.location.href = '{{ route("responder.dashboard") }}';
                } else {
                    alert('Error submitting report. Please try again.');
                }
            })
            .catch(error => {
                modal.classList.remove('modal-open');
                console.error('Error:', error);
                alert('Error submitting report. Please check your connection.');
            });
        }

        // Draft Saving
        function saveDraft() {
            const formData = new FormData(document.getElementById('mobile-incident-form'));
            localStorage.setItem('incident_draft', JSON.stringify(Object.fromEntries(formData)));
            alert('Draft saved locally');
        }

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            getCurrentLocation();
            
            // Load saved draft
            const draft = localStorage.getItem('incident_draft');
            if (draft) {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(key => {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = data[key];
                    }
                });
            }
        });
    </script>
</body>
</html>