<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Dashboard - BukidnonAlert</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .bg-brick-orange { background-color: #c14a09; }
        .text-brick-orange { color: #c14a09; }
        
        .status-online { color: #10b981; }
        .status-busy { color: #f59e0b; }
        .status-offline { color: #ef4444; }
        
        .incident-card {
            border-left: 4px solid #3b82f6;
            transition: transform 0.2s;
        }
        
        .incident-card.critical {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        .incident-card.high {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        
        .incident-card:active {
            transform: scale(0.98);
        }
        
        .quick-action-btn {
            min-height: 80px;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .vehicle-status {
            border-radius: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }
            
            .grid {
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('Layouts.navbar')
    
    <div class="container mx-auto px-4 py-4">
        <!-- Status Header -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <div>
                        <h1 class="text-lg font-bold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h1>
                        <p class="text-sm text-gray-600">Responder • {{ auth()->user()->municipality }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Status</div>
                    <select class="select select-bordered select-sm" onchange="updateStatus(this.value)">
                        <option value="available" class="text-green-600">Available</option>
                        <option value="busy" class="text-yellow-600">On Call</option>
                        <option value="offline" class="text-red-600">Off Duty</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <a href="{{ route('incidents.create') }}" class="btn btn-error quick-action-btn">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
                <span class="text-sm font-medium">Report Incident</span>
            </a>
            <button onclick="shareLocation()" class="btn btn-info quick-action-btn">
                <i class="fas fa-map-marker-alt text-2xl"></i>
                <span class="text-sm font-medium">Share Location</span>
            </button>
        </div>

        <!-- Vehicle Status -->
        @if($myVehicle)
        <div class="vehicle-status mb-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">My Vehicle</h3>
                <span class="badge badge-success">{{ ucfirst($myVehicle->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="opacity-80">Vehicle</p>
                    <p class="font-medium">{{ $myVehicle->vehicle_number }}</p>
                    <p class="text-xs opacity-70">{{ $myVehicle->make }} {{ $myVehicle->model }}</p>
                </div>
                <div>
                    <p class="opacity-80">Fuel Level</p>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 bg-white bg-opacity-20 rounded-full h-2">
                            <div class="bg-white h-2 rounded-full" style="width: {{ $myVehicle->current_fuel_level }}%"></div>
                        </div>
                        <span class="text-xs font-medium">{{ $myVehicle->current_fuel_level }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Active Incidents -->
        <div class="bg-white rounded-lg shadow-md mb-4">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Active Incidents</h2>
                    <span class="badge badge-error">{{ count($activeIncidents) }}</span>
                </div>
            </div>
            <div class="p-2">
                @forelse($activeIncidents as $incident)
                <div class="incident-card {{ $incident->severity_level }} bg-white border rounded-lg p-3 mb-2"
                     onclick="viewIncident('{{ $incident->id }}')">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="badge badge-sm 
                                    {{ $incident->severity_level === 'critical' ? 'badge-error' : 
                                       ($incident->severity_level === 'high' ? 'badge-warning' : 'badge-info') }}">
                                    {{ ucfirst($incident->severity_level) }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $incident->incident_date->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-medium text-sm mb-1">{{ $incident->incident_number }}</h4>
                            <p class="text-xs text-gray-600 mb-1">{{ $incident->incident_type }} • {{ $incident->location }}</p>
                            <p class="text-xs text-gray-500">{{ Str::limit($incident->description, 80) }}</p>
                        </div>
                        <div class="text-right">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                            @if($incident->latitude && $incident->longitude)
                            <div class="mt-1">
                                <button onclick="event.stopPropagation(); openMaps({{ $incident->latitude }}, {{ $incident->longitude }})" 
                                        class="btn btn-xs btn-outline">
                                    <i class="fas fa-map text-xs"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <p>No active incidents in your area</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Recent Activity</h2>
            </div>
            <div class="p-2">
                <div class="space-y-2">
                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm">Incident INC-2025-001 resolved</p>
                            <p class="text-xs text-gray-500">10 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm">Vehicle inspection completed</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm">Low fuel alert - Vehicle #001</p>
                            <p class="text-xs text-gray-500">3 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="dropdown dropdown-top dropdown-end">
            <label tabindex="0" class="btn btn-circle btn-lg bg-red-600 text-white hover:bg-red-700 shadow-lg">
                <i class="fas fa-plus text-xl"></i>
            </label>
            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 mb-2">
                <li><a href="{{ route('incidents.create') }}" class="text-red-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Report
                </a></li>
                <li><a href="#" onclick="quickCall('911')" class="text-green-600">
                    <i class="fas fa-phone mr-2"></i>Call 911
                </a></li>
                <li><a href="#" onclick="requestBackup()" class="text-blue-600">
                    <i class="fas fa-users mr-2"></i>Request Backup
                </a></li>
                <li><a href="#" onclick="updateLocation()" class="text-purple-600">
                    <i class="fas fa-map-marker-alt mr-2"></i>Update Location
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Emergency Contact Modal -->
    <div class="modal" id="emergency-modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Emergency Contacts</h3>
            <div class="space-y-3">
                <button onclick="quickCall('911')" class="btn btn-error btn-block justify-start">
                    <i class="fas fa-phone mr-2"></i>Emergency Services (911)
                </button>
                <button onclick="quickCall('117')" class="btn btn-warning btn-block justify-start">
                    <i class="fas fa-fire mr-2"></i>Fire Department (117)
                </button>
                <button onclick="quickCall('143')" class="btn btn-info btn-block justify-start">
                    <i class="fas fa-ambulance mr-2"></i>Medical Emergency (143)
                </button>
                <button onclick="quickCall('116')" class="btn btn-primary btn-block justify-start">
                    <i class="fas fa-shield-alt mr-2"></i>Police (116)
                </button>
            </div>
            <div class="modal-action">
                <label for="emergency-modal" class="btn">Close</label>
            </div>
        </div>
    </div>

    <script>
        // GPS Functions
        function shareLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    fetch('/api/responder/location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lng })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Location shared with dispatch center');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to share location');
                    });
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        function updateLocation() {
            shareLocation();
        }

        function openMaps(lat, lng) {
            const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
            window.open(url, '_blank');
        }

        // Incident Functions
        function viewIncident(incidentId) {
            window.location.href = `/incidents/${incidentId}`;
        }

        // Communication Functions
        function quickCall(number) {
            if (confirm(`Call ${number}?`)) {
                window.location.href = `tel:${number}`;
            }
        }

        function requestBackup() {
            if (confirm('Request backup to your current location?')) {
                fetch('/api/responder/backup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Backup request sent to dispatch');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to request backup');
                });
            }
        }

        // Status Management
        function updateStatus(status) {
            fetch('/api/responder/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Status updated:', status);
            })
            .catch(error => {
                console.error('Error updating status:', error);
            });
        }

        // Auto-refresh data every 30 seconds
        setInterval(() => {
            fetch('/api/responder/dashboard-data')
                .then(response => response.json())
                .then(data => {
                    // Update dashboard data without full page reload
                    console.log('Dashboard data refreshed');
                })
                .catch(error => console.error('Error refreshing data:', error));
        }, 30000);

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial status
            updateStatus('available');
            
            // Request location permission on load
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        console.log('Location access granted');
                    },
                    function(error) {
                        console.log('Location access denied');
                    }
                );
            }
        });
    </script>
</body>
</html>