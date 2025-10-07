@extends('Layouts.app')

@section('title', 'Emergency Heat Map - MDRRMO Maramag')

@section('body-class', 'heatmap-page')


@push('styles')
<style>
    /* Reduce top spacing for heat map page */
    .container-fluid {
        padding-top: 0.5rem !important;
    }

    /* Ensure map container has proper spacing */
    #heatMap {
        border-radius: 0.375rem;
        width: 100%;
        height: 600px;
    }

    /* Tooltip styles for incident markers */
    .tooltip-content {
        font-family: inherit;
        font-size: 12px;
        line-height: 1.3;
        min-width: 150px;
        padding: 5px;
    }

    .tooltip-title {
        font-size: 13px;
        margin-bottom: 2px;
        color: #000;
        font-weight: bold;
    }

    .tooltip-type {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 3px;
        text-transform: capitalize;
    }

    .tooltip-location {
        font-size: 11px;
        color: #495057;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .tooltip-date {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .tooltip-victims {
        font-size: 11px;
        color: #dc3545;
        font-weight: 500;
    }

    .tooltip-severity {
        margin-bottom: 2px;
    }

    .tooltip-image img {
        border: 1px solid #dee2e6;
    }

    /* Simplified Z-INDEX for better map rendering */

    /* Ensure navbar stays on top */
    .navbar {
        z-index: 1000 !important;
        position: relative;
    }

    /* Map container with proper z-index */
    #heatMap {
        z-index: 1;
        position: relative;
    }

    /* Leaflet popups should be visible */
    .leaflet-popup {
        z-index: 600;
    }

    /* Map controls stay visible */
    .leaflet-control-container {
        z-index: 400;
    }

    /* Dropdown menus above map but below navbar */
    .dropdown-content {
        z-index: 800;
    }

    /* Custom marker styling */
    .custom-marker {
        background: transparent !important;
        border: none !important;
    }

    /* Existing styles */
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .leaflet-popup-content {
        margin: 0;
        font-family: inherit;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .card {
        transition: all 0.2s ease;
    }

    .btn-sm {
        border-radius: 6px;
    }

    .badge {
        font-weight: 500;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 py-2">
    <!-- Page Header with Emergency Response Styling -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6">
        <div class="flex items-center mb-4 lg:mb-0">
            <div class="mr-4">
                <div class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-primary text-xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-base-content mb-1">Emergency Heat Map</h1>
                <p class="text-base-content text-opacity-60 text-sm">Visual incident analysis for Maramag, Bukidnon</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-outline btn-sm" onclick="toggleFilters()">
                <i class="fas fa-filter mr-1"></i>Filters
            </button>
            <button class="btn btn-primary btn-sm" onclick="refreshMap()">
                <i class="fas fa-sync-alt mr-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-100 shadow-md">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="mr-3">
                        <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content text-opacity-60 text-sm">Total Incidents</div>
                        <div class="text-2xl font-bold text-base-content">{{ $totalIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-md">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="mr-3">
                        <div class="w-10 h-10 rounded-full bg-info bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content text-opacity-60 text-sm">This Month</div>
                        <div class="text-2xl font-bold text-base-content">{{ $monthlyIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-md">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="mr-3">
                        <div class="w-10 h-10 rounded-full bg-warning bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-chart-area text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content text-opacity-60 text-sm">High Density Areas</div>
                        <div class="text-2xl font-bold text-base-content">{{ $hotspots ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-md">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="mr-3">
                        <div class="w-10 h-10 rounded-full bg-success bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-map-pin text-success"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content text-opacity-60 text-sm">Mapped Locations</div>
                        <div class="text-2xl font-bold text-base-content">{{ $mappedIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel (Initially Hidden) -->
    <div class="mb-6 hidden" id="filterPanel">
        <div class="card bg-base-100 shadow-md">
            <div class="card-header bg-base-200 border-b border-base-300">
                <h6 class="text-base font-medium text-base-content"><i class="fas fa-filter mr-2"></i>Filter Controls</h6>
            </div>
            <div class="card-body bg-base-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-medium">Emergency Type</span>
                        </label>
                        <select class="select select-bordered select-sm w-full" id="incidentTypeFilter">
                            <option value="">All Types</option>
                            <option value="vehicle_vs_vehicle">Vehicle Collision</option>
                            <option value="vehicle_vs_pedestrian">Vehicle vs Pedestrian</option>
                            <option value="vehicle_vs_animals">Vehicle vs Animals</option>
                            <option value="vehicle_vs_property">Vehicle vs Property</option>
                            <option value="vehicle_alone">Single Vehicle</option>
                            <option value="maternity">Medical Emergency</option>
                            <option value="stabbing_shooting">Violence Emergency</option>
                            <option value="transport_to_hospital">Medical Transport</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-medium">Severity Level</span>
                        </label>
                        <select class="select select-bordered select-sm w-full" id="severityFilter">
                            <option value="">All Levels</option>
                            <option value="minor">Minor</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-medium">Date From</span>
                        </label>
                        <input type="date" class="input input-bordered input-sm w-full" id="dateFromFilter">
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-medium">Date To</span>
                        </label>
                        <input type="date" class="input input-bordered input-sm w-full" id="dateToFilter">
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text text-sm font-medium">Actions</span>
                        </label>
                        <div class="flex gap-2">
                            <button class="btn btn-primary btn-sm" onclick="applyFilters()">
                                <i class="fas fa-search mr-1"></i>Apply
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="clearFilters()">
                                <i class="fas fa-times mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Map Container -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-md">
                <div class="card-header bg-base-200 border-b border-base-300 p-4">
                    <div class="flex justify-between items-center">
                        <h6 class="text-base font-medium text-base-content"><i class="fas fa-map mr-2"></i>Emergency Incident Heat Map</h6>
                        <div class="flex gap-2">
                            <button class="btn btn-outline btn-sm" onclick="toggleHeatLayer()">
                                <i class="fas fa-layer-group mr-1"></i>Toggle Heat
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="centerMap()">
                                <i class="fas fa-crosshairs mr-1"></i>Center
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="heatMap"></div>
                </div>
            </div>
        </div>

        <!-- Map Information Panel -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-md h-full">
                <div class="card-header bg-base-200 border-b border-base-300 p-4">
                    <h6 class="text-base font-medium text-base-content"><i class="fas fa-info-circle mr-2"></i>Map Information</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Heat Map Legend -->
                    <div class="mb-4">
                        <h6 class="text-base-content font-medium mb-3">Incident Density</h6>
                        <div class="flex items-center mb-2">
                            <div class="mr-3 w-5 h-5 rounded" style="background: linear-gradient(to right, #0dcaf0, #0d6efd);"></div>
                            <span class="text-sm text-base-content text-opacity-60">Low - Moderate</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <div class="mr-3 w-5 h-5 rounded" style="background: linear-gradient(to right, #fd7e14, #dc3545);"></div>
                            <span class="text-sm text-base-content text-opacity-60">High - Critical</span>
                        </div>
                    </div>

                    <!-- Severity Markers -->
                    <div class="mb-4">
                        <h6 class="text-base-content font-medium mb-3">Severity Levels</h6>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-circle text-info mr-2"></i>
                            <span class="text-sm text-base-content text-opacity-60">Minor Incidents</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-circle text-primary mr-2"></i>
                            <span class="text-sm text-base-content text-opacity-60">Moderate Incidents</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-circle text-warning mr-2"></i>
                            <span class="text-sm text-base-content text-opacity-60">Severe Incidents</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-circle text-error mr-2"></i>
                            <span class="text-sm text-base-content text-opacity-60">Critical Incidents</span>
                        </div>
                    </div>

                    <!-- Map Controls Info -->
                    <div class="mb-4">
                        <h6 class="text-base-content font-medium mb-3">Map Controls</h6>
                        <ul class="space-y-1">
                            <li class="text-sm text-base-content text-opacity-60"><i class="fas fa-mouse-pointer mr-2"></i>Click pins for details</li>
                            <li class="text-sm text-base-content text-opacity-60"><i class="fas fa-search-plus mr-2"></i>Scroll to zoom</li>
                            <li class="text-sm text-base-content text-opacity-60"><i class="fas fa-hand-rock mr-2"></i>Drag to pan</li>
                            <li class="text-sm text-base-content text-opacity-60"><i class="fas fa-layer-group mr-2"></i>Toggle heat overlay</li>
                        </ul>
                    </div>

                    <!-- Current View Status -->
                    <div class="bg-base-200 rounded p-3">
                        <h6 class="text-base-content font-medium mb-2">Current View</h6>
                        <div class="text-sm text-base-content text-opacity-60 space-y-1">
                            <div>Center: <span id="mapCenter">Maramag, Bukidnon</span></div>
                            <div>Zoom Level: <span id="mapZoom">12</span></div>
                            <div>Visible Incidents: <span id="visibleIncidents">{{ $totalIncidents ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents Table -->
    <div class="w-full">
        <div class="card bg-base-100 shadow-md">
            <div class="card-header bg-base-200 border-b border-base-300 p-4">
                <div class="flex justify-between items-center">
                    <h6 class="text-base font-medium text-base-content"><i class="fas fa-list mr-2"></i>Recent Incidents on Map</h6>
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i>View All
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                @if(isset($recentIncidents) && $recentIncidents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full hover">
                            <thead>
                                <tr>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Incident #</th>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Type</th>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Location</th>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Severity</th>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Date</th>
                                    <th class="text-base-content text-opacity-60 text-sm font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIncidents as $incident)
                                <tr onclick="centerMapOnIncident({{ $incident->latitude }}, {{ $incident->longitude }})" class="cursor-pointer hover:bg-base-200">
                                    <td class="text-primary font-medium">{{ $incident->incident_number }}</td>
                                    <td>
                                        <span class="badge badge-ghost badge-sm">
                                            {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-base-content text-opacity-60">{{ $incident->location }}</td>
                                    <td>
                                        @switch($incident->severity_level)
                                            @case('minor')
                                                <span class="badge badge-info badge-sm">Minor</span>
                                                @break
                                            @case('moderate')
                                                <span class="badge badge-primary badge-sm">Moderate</span>
                                                @break
                                            @case('severe')
                                                <span class="badge badge-warning badge-sm">Severe</span>
                                                @break
                                            @case('critical')
                                                <span class="badge badge-error badge-sm">Critical</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-base-content text-opacity-60 text-sm">{{ $incident->incident_date ? $incident->incident_date->format('M j, Y') : 'N/A' }}</td>
                                    <td>
                                        @switch($incident->status)
                                            @case('pending')
                                                <span class="badge badge-warning badge-sm">Pending</span>
                                                @break
                                            @case('responding')
                                                <span class="badge badge-info badge-sm">Responding</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge badge-success badge-sm">Resolved</span>
                                                @break
                                            @case('closed')
                                                <span class="badge badge-ghost badge-sm">Closed</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-map-marker-alt text-base-content text-opacity-40 text-4xl mb-4"></i>
                        <h6 class="text-base-content text-opacity-60">No incidents with location data found</h6>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Heat Map Implementation
let map, heatLayer, markers = [];
let isHeatLayerVisible = true;

// Initialize the map
function initMap() {
    try {
        // Check if map container exists
        const mapContainer = document.getElementById('heatMap');
        if (!mapContainer) {
            console.error('Map container not found');
            showErrorToast('Map container not found');
            return;
        }

        // Center on Maramag, Bukidnon
        map = L.map('heatMap').setView([7.7167, 125.0167], 12);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(map);

        // Get incident data from controller
        const incidentData = @json($incidents ?? []);
        console.log('Incident data loaded:', incidentData.length, 'incidents');

        if (incidentData.length > 0) {
            initHeatLayer(incidentData);
            addIncidentMarkers(incidentData);
            showSuccessToast(`Loaded ${incidentData.length} incidents on map`);
        } else {
            showSuccessToast('No incident data available for mapping');
        }

        // Update map info
        updateMapInfo();

        // Map event listeners
        map.on('zoomend moveend', updateMapInfo);

        console.log('Heatmap initialized successfully');

    } catch (error) {
        console.error('Error initializing map:', error);
        showErrorToast('Failed to initialize map: ' + error.message);
    }
}

function initHeatLayer(incidents) {
    try {
        const heatData = incidents.map(incident => {
            if (incident.latitude && incident.longitude) {
                const intensity = getSeverityWeight(incident.severity_level);
                return [incident.latitude, incident.longitude, intensity];
            }
        }).filter(point => point !== undefined);

        if (heatData.length > 0) {
            heatLayer = L.heatLayer(heatData, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                gradient: {
                    0.0: '#0dcaf0',
                    0.3: '#0d6efd',
                    0.6: '#fd7e14',
                    1.0: '#dc3545'
                }
            }).addTo(map);
            console.log('Heat layer created with', heatData.length, 'data points');
        } else {
            console.warn('No valid coordinates found for heat layer');
        }
    } catch (error) {
        console.error('Error creating heat layer:', error);
        showErrorToast('Failed to create heat layer. Please try refreshing the page.');
    }
}

function addIncidentMarkers(incidents) {
    try {
        let markersAdded = 0;
        incidents.forEach(incident => {
            if (incident.latitude && incident.longitude) {
                try {
                    const marker = L.marker([incident.latitude, incident.longitude])
                        .bindPopup(createPopupContent(incident));

                    // Add hover functionality
                    marker.bindTooltip(createTooltipContent(incident), {
                        permanent: false,
                        direction: 'top',
                        offset: [0, -10]
                    });

                    marker.options.icon = createSeverityIcon(incident.severity_level);
                    marker.addTo(map);
                    markers.push(marker);
                    markersAdded++;
                } catch (error) {
                    console.error('Error creating marker for incident:', incident.incident_number, error);
                }
            }
        });
        console.log('Added', markersAdded, 'markers to map');
    } catch (error) {
        console.error('Error adding incident markers:', error);
        showErrorToast('Failed to add incident markers. Please try refreshing the page.');
    }
}

function createSeverityIcon(severity) {
    const colors = {
        'minor': '#0dcaf0',
        'moderate': '#0d6efd',
        'severe': '#fd7e14',
        'critical': '#dc3545'
    };

    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${colors[severity] || '#6c757d'}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
}

function createPopupContent(incident) {
    const severityBadges = {
        'minor': '<span class="badge bg-info">Minor</span>',
        'moderate': '<span class="badge bg-primary">Moderate</span>',
        'severe': '<span class="badge bg-warning">Severe</span>',
        'critical': '<span class="badge bg-danger">Critical</span>'
    };

    // Format the date safely
    let dateHtml = '';
    if (incident.incident_datetime) {
        const formattedDate = new Date(incident.incident_datetime).toLocaleDateString();
        dateHtml = `<p class="mb-2"><strong>Date:</strong> ${formattedDate}</p>`;
    }

    return `
        <div class="p-2">
            <h6 class="mb-2 fw-bold">${incident.incident_number}</h6>
            <p class="mb-1"><strong>Type:</strong> ${incident.incident_type.replace('_', ' ')}</p>
            <p class="mb-1"><strong>Location:</strong> ${incident.location}</p>
            <p class="mb-1"><strong>Severity:</strong> ${severityBadges[incident.severity_level] || incident.severity_level}</p>
            ${dateHtml}
            <a href="/incidents/${incident.id}" class="btn btn-primary btn-sm">View Details</a>
        </div>
    `;
}

// New function to create tooltip content with image preview
function createTooltipContent(incident) {
    // Get the first photo if available
    let photoHtml = '';
    if (incident.photos && incident.photos.length > 0) {
        // Assuming photos are stored as file paths
        const firstPhoto = incident.photos[0];
        // Photos are stored in storage/app/public/incident_photos and accessed via /storage/
        const photoSrc = `/storage/${firstPhoto}`;
        photoHtml = `<div class="tooltip-image mb-1">
                        <img src="${photoSrc}" alt="Incident photo" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;">
                     </div>`;
    }

    // Get victim count if available
    const victimCount = incident.victims ? incident.victims.length : 0;
    const victimHtml = victimCount > 0 ? `<div class="tooltip-victims"><i class="fas fa-user-injured me-1"></i>${victimCount} victim(s)</div>` : '';

    // Get severity badge
    const severityBadges = {
        'minor': '<span class="badge bg-info badge-xs">Minor</span>',
        'moderate': '<span class="badge bg-primary badge-xs">Moderate</span>',
        'severe': '<span class="badge bg-warning badge-xs">Severe</span>',
        'critical': '<span class="badge bg-danger badge-xs">Critical</span>'
    };
    const severityHtml = severityBadges[incident.severity_level] || `<span class="badge badge-ghost badge-xs">${incident.severity_level}</span>`;

    // Format the date safely
    let dateHtml = '';
    if (incident.incident_datetime) {
        const formattedDate = new Date(incident.incident_datetime).toLocaleDateString();
        dateHtml = `<div class="tooltip-date"><i class="fas fa-calendar me-1"></i>${formattedDate}</div>`;
    }

    return `
        <div class="tooltip-content">
            ${photoHtml}
            <div class="tooltip-title fw-bold">${incident.incident_number}</div>
            <div class="tooltip-type">${incident.incident_type.replace('_', ' ')}</div>
            <div class="tooltip-location"><i class="fas fa-map-marker-alt me-1"></i>${incident.location}</div>
            ${dateHtml}
            <div class="tooltip-severity">${severityHtml}</div>
            ${victimHtml}
        </div>
    `;
}

function getSeverityWeight(severity) {
    const weights = {
        'minor': 0.3,
        'moderate': 0.5,
        'severe': 0.8,
        'critical': 1.0
    };
    return weights[severity] || 0.5;
}

function toggleHeatLayer() {
    if (isHeatLayerVisible && heatLayer) {
        map.removeLayer(heatLayer);
        isHeatLayerVisible = false;
    } else if (heatLayer) {
        map.addLayer(heatLayer);
        isHeatLayerVisible = true;
    }
}

function centerMap() {
    map.setView([7.7167, 125.0167], 12);
}

function centerMapOnIncident(lat, lng) {
    map.setView([lat, lng], 16);
}

function toggleFilters() {
    const panel = document.getElementById('filterPanel');
    panel.classList.toggle('hidden');
}

function refreshMap() {
    location.reload();
}

function updateMapInfo() {
    document.getElementById('mapZoom').textContent = map.getZoom();
    const center = map.getCenter();
    document.getElementById('mapCenter').textContent = `${center.lat.toFixed(4)}, ${center.lng.toFixed(4)}`;
}

function applyFilters() {
    showSuccessToast('Filters applied successfully');
}

function clearFilters() {
    document.getElementById('incidentTypeFilter').value = '';
    document.getElementById('severityFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    showSuccessToast('Filters cleared');
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Leaflet is loaded before initializing
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        console.error('Leaflet library not loaded');
        showErrorToast('Map failed to load: Missing Leaflet library');
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (map) {
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
});
</script>
@endsection
