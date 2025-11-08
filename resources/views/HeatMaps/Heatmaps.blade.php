@extends('Layouts.app')

@section('title', 'Emergency Heat Map - MDRRMO Maramag')

@section('body-class', 'heatmap-page')

@push('styles')
<style>
    /* Ensure map container has proper sizing */
    #heatMap {
        border-radius: 0.5rem;
        width: 100%;
        height: 600px;
        min-height: 600px;
    }

    /* Tooltip styles for incident markers - Improved Typography Hierarchy */
    .tooltip-content {
        font-family: inherit;
        font-size: 13px;
        line-height: 1.5;
        min-width: 200px;
        max-width: 250px;
        padding: 8px;
    }

    .tooltip-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1E40AF;
        letter-spacing: -0.02em;
    }

    .tooltip-type {
        font-size: 12px;
        color: #374151;
        margin-bottom: 4px;
        text-transform: capitalize;
        font-weight: 500;
    }

    .tooltip-location {
        font-size: 12px;
        color: #4B5563;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 230px;
        font-weight: 400;
    }

    .tooltip-date {
        font-size: 11px;
        color: #6B7280;
        margin-bottom: 4px;
        font-weight: 400;
    }

    .tooltip-victims {
        font-size: 12px;
        color: #DC2626;
        font-weight: 600;
        margin-top: 4px;
    }

    .tooltip-severity {
        margin-bottom: 4px;
        margin-top: 4px;
    }

    .tooltip-image img {
        border: 2px solid #E5E7EB;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    /* Simplified z-index for map */
    #heatMap {
        z-index: 1;
        position: relative;
    }

    .leaflet-popup {
        z-index: 500;
    }

    /* Custom marker styling */
    .custom-marker {
        background: transparent !important;
        border: none !important;
        z-index: 100 !important;
    }

    /* Leaflet popup styling - Enhanced */
    .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        padding: 4px;
    }

    .leaflet-popup-content {
        margin: 0;
        font-family: inherit;
        min-width: 280px;
    }

    /* Popup content styles */
    .popup-container {
        padding: 16px;
    }

    .popup-header {
        font-size: 16px;
        font-weight: 700;
        color: #1E40AF;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #E5E7EB;
        letter-spacing: -0.02em;
    }

    .popup-info-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .popup-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        min-width: 80px;
        margin-right: 8px;
    }

    .popup-value {
        font-size: 13px;
        color: #4B5563;
        font-weight: 400;
        flex: 1;
    }

    .popup-button {
        margin-top: 12px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 600;
        width: 100%;
        border-radius: 6px;
        transition: all 0.2s;
        color: #FFFFFF !important;
        text-decoration: none !important;
    }

    .popup-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(30, 64, 175, 0.2);
        color: #FFFFFF !important;
    }

    .popup-button i {
        color: #FFFFFF !important;
    }

    /* Table styling improvements */
    .incident-row {
        transition: background-color 0.2s ease;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 py-6">
    <!-- Page Header with Emergency Response Styling -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <i class="fas fa-map-marked-alt text-primary text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-base-content">Emergency Heat Map</h1>
                <p class="text-base-content/60 text-sm">Visual incident analysis for Maramag, Bukidnon</p>
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
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content/60 text-xs">Total Incidents</div>
                        <div class="text-2xl font-bold text-base-content">{{ $totalIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-info/10 flex items-center justify-center">
                        <i class="fas fa-clock text-info"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content/60 text-xs">This Month</div>
                        <div class="text-2xl font-bold text-base-content">{{ $monthlyIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-warning/10 flex items-center justify-center">
                        <i class="fas fa-chart-area text-warning"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content/60 text-xs">High Density Areas</div>
                        <div class="text-2xl font-bold text-base-content">{{ $hotspots ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center">
                        <i class="fas fa-map-pin text-success"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-base-content/60 text-xs">Mapped Locations</div>
                        <div class="text-2xl font-bold text-base-content">{{ $mappedIncidents ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel (Initially Hidden) -->
    <div class="mb-6 hidden" id="filterPanel">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-10 h-10 bg-primary/10 rounded-lg">
                        <i class="fas fa-filter text-primary"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Filter Controls</h3>
                        <p class="text-sm text-gray-500">Filter incidents by type, severity, and location</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Emergency Type</span>
                        </label>
                        <select class="select select-bordered w-full" id="incidentTypeFilter">
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
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Severity Level</span>
                        </label>
                        <select class="select select-bordered w-full" id="severityFilter">
                            <option value="">All Levels</option>
                            <option value="minor">Minor</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Municipality</span>
                        </label>
                        <select name="municipality" id="municipalityFilter" class="select select-bordered w-full">
                            <option value="">All Municipalities</option>
                            @foreach(config('locations.municipalities') as $municipality => $value)
                                <option value="{{ $municipality }}">{{ $municipality }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Actions</span>
                        </label>
                        <div class="flex gap-2">
                            <button class="btn btn-primary flex-1 gap-2" onclick="applyFilters()">
                                <i class="fas fa-search"></i>
                                Apply
                            </button>
                            <button class="btn btn-outline flex-1 gap-2" onclick="clearFilters()">
                                <i class="fas fa-times"></i>
                                Clear
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
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-0">
                    <div class="flex justify-between items-center p-4 border-b border-base-300">
                        <h6 class="text-base font-semibold"><i class="fas fa-map mr-2"></i>Emergency Incident Heat Map</h6>
                        <div class="flex gap-2">
                            <button class="btn btn-outline btn-sm" onclick="toggleHeatLayer()">
                                <i class="fas fa-layer-group mr-1"></i>Toggle Heat
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="centerMap()">
                                <i class="fas fa-crosshairs mr-1"></i>Center
                            </button>
                        </div>
                    </div>
                    <div id="heatMap"></div>
                </div>
            </div>
        </div>

        <!-- Map Information Panel -->
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow-sm h-full">
                <div class="card-body">
                    <!-- Heat Map Legend -->
                    <div class="mb-6">
                        <h6 class="font-semibold text-base-content mb-3">Incident Density</h6>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-5 h-5 rounded" style="background: linear-gradient(to right, #0dcaf0, #0d6efd);"></div>
                            <span class="text-sm text-base-content/60">Low - Moderate</span>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-5 h-5 rounded" style="background: linear-gradient(to right, #fd7e14, #dc3545);"></div>
                            <span class="text-sm text-base-content/60">High - Critical</span>
                        </div>
                    </div>

                    <!-- Severity Markers -->
                    <div class="mb-6">
                        <h6 class="font-semibold text-base-content mb-3">Severity Levels</h6>
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-circle text-info"></i>
                            <span class="text-sm text-base-content/60">Minor Incidents</span>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-circle text-primary"></i>
                            <span class="text-sm text-base-content/60">Moderate Incidents</span>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-circle text-warning"></i>
                            <span class="text-sm text-base-content/60">Severe Incidents</span>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-circle text-error"></i>
                            <span class="text-sm text-base-content/60">Critical Incidents</span>
                        </div>
                    </div>

                    <!-- Map Controls Info -->
                    <div class="mb-6">
                        <h6 class="font-semibold text-base-content mb-3">Map Controls</h6>
                        <ul class="space-y-1">
                            <li class="text-sm text-base-content/60"><i class="fas fa-mouse-pointer mr-2"></i>Click pins for details</li>
                            <li class="text-sm text-base-content/60"><i class="fas fa-search-plus mr-2"></i>Scroll to zoom</li>
                            <li class="text-sm text-base-content/60"><i class="fas fa-hand-rock mr-2"></i>Drag to pan</li>
                            <li class="text-sm text-base-content/60"><i class="fas fa-layer-group mr-2"></i>Toggle heat overlay</li>
                        </ul>
                    </div>

                    <!-- Current View Status -->
                    <div class="bg-base-200 rounded-lg p-4">
                        <h6 class="font-semibold text-base-content mb-2">Current View</h6>
                        <div class="text-sm text-base-content/60 space-y-1">
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
        <!-- Table Card -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-0">
                <div class="flex justify-between items-center p-4 border-b border-base-300">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-info/10 rounded-lg">
                            <i class="fas fa-table text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-lg font-semibold text-gray-900">Recent Incidents on Map</h6>
                            <p class="text-sm text-gray-500">
                                @if(isset($recentIncidents) && method_exists($recentIncidents, 'total'))
                                    Showing {{ $recentIncidents->firstItem() ?? 0 }} to {{ $recentIncidents->lastItem() ?? 0 }} of {{ $recentIncidents->total() }} incidents
                                @else
                                    {{ isset($recentIncidents) ? $recentIncidents->count() : 0 }} incidents
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary btn-sm gap-2">
                        <i class="fas fa-external-link-alt"></i>
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    @if(isset($recentIncidents) && $recentIncidents->count() > 0)
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-hashtag mr-1 text-gray-600"></i>
                                        Incident #
                                    </th>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-tag mr-1 text-gray-600"></i>
                                        Type
                                    </th>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-map-marker-alt mr-1 text-gray-600"></i>
                                        Location
                                    </th>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-exclamation-triangle mr-1 text-gray-600"></i>
                                        Severity
                                    </th>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-calendar mr-1 text-gray-600"></i>
                                        Date
                                    </th>
                                    <th class="font-semibold text-gray-700">
                                        <i class="fas fa-info-circle mr-1 text-gray-600"></i>
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIncidents as $incident)
                                <tr class="incident-row hover:bg-primary/5 cursor-pointer transition-colors" onclick="centerMapOnIncident({{ $incident->latitude }}, {{ $incident->longitude }})">
                                    <td>
                                        <span class="font-mono font-semibold text-primary">{{ $incident->incident_number }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'vehicle_vs_vehicle' => 'badge-error',
                                                'vehicle_vs_pedestrian' => 'badge-warning',
                                                'vehicle_vs_animals' => 'badge-info',
                                                'vehicle_vs_property' => 'badge-warning',
                                                'vehicle_alone' => 'badge-info',
                                                'maternity' => 'badge-error',
                                                'stabbing_shooting' => 'badge-error',
                                                'transport_to_hospital' => 'badge-warning',
                                            ];
                                            $badgeColor = $typeColors[$incident->incident_type] ?? 'badge-ghost';
                                        @endphp
                                        <span class="badge {{ $badgeColor }} badge-sm gap-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                        </span>
                                    </td>
                                    <td class="text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-location-dot text-gray-400"></i>
                                            <span>{{ $incident->location }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($incident->severity_level)
                                            @case('minor')
                                                <span class="badge badge-info gap-1">
                                                    <i class="fas fa-circle"></i>
                                                    Minor
                                                </span>
                                                @break
                                            @case('moderate')
                                                <span class="badge badge-primary gap-1">
                                                    <i class="fas fa-circle"></i>
                                                    Moderate
                                                </span>
                                                @break
                                            @case('severe')
                                                <span class="badge badge-warning gap-1">
                                                    <i class="fas fa-circle"></i>
                                                    Severe
                                                </span>
                                                @break
                                            @case('critical')
                                                <span class="badge badge-error gap-1">
                                                    <i class="fas fa-circle"></i>
                                                    Critical
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-gray-400"></i>
                                            <span>{{ $incident->incident_date ? $incident->incident_date->timezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($incident->status)
                                            @case('pending')
                                                <span class="badge badge-warning gap-1">
                                                    <i class="fas fa-clock"></i>
                                                    Pending
                                                </span>
                                                @break
                                            @case('responding')
                                                <span class="badge badge-info gap-1">
                                                    <i class="fas fa-truck-medical"></i>
                                                    Responding
                                                </span>
                                                @break
                                            @case('resolved')
                                                <span class="badge badge-success gap-1">
                                                    <i class="fas fa-check-circle"></i>
                                                    Resolved
                                                </span>
                                                @break
                                            @case('closed')
                                                <span class="badge badge-ghost gap-1">
                                                    <i class="fas fa-archive"></i>
                                                    Closed
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-map-marker-alt text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Incidents Found</h3>
                            <p class="text-gray-500">No incidents with location data are available at this time.</p>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if(isset($recentIncidents) && method_exists($recentIncidents, 'hasPages') && $recentIncidents->hasPages())
                <div class="p-4 border-t border-base-300 bg-white">
                    {{ $recentIncidents->links() }}
                </div>
                @endif
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
    // Center on Maramag, Bukidnon
    map = L.map('heatMap').setView([7.7167, 125.0167], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Z-index fix for map after initialization
    setTimeout(() => {
        const mapContainer = document.getElementById('heatMap');
        if (mapContainer) {
            mapContainer.style.zIndex = '1';
            mapContainer.style.position = 'relative';
        }

        // Force all leaflet panes to low z-index
        const leafletPanes = document.querySelectorAll('.leaflet-pane');
        leafletPanes.forEach(pane => {
            pane.style.zIndex = '1';
        });

        // Ensure navbar stays on top
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.style.zIndex = '9999';
            navbar.style.position = 'relative';
        }

        // Fix dropdown z-index
        const dropdowns = document.querySelectorAll('.dropdown-content');
        dropdowns.forEach(dropdown => {
            dropdown.style.zIndex = '10000';
        });

        console.log('Z-index fix applied');
    }, 100);

    // Sample incident data - replace with actual data from controller
    const incidentData = @json($incidents ?? []);

    if (incidentData.length > 0) {
        initHeatLayer(incidentData);
        addIncidentMarkers(incidentData);
    }

    // Update map info
    updateMapInfo();

    // Map event listeners
    map.on('zoomend moveend', updateMapInfo);
}

// Rest of your existing JavaScript functions...
function initHeatLayer(incidents) {
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
    }
}

function addIncidentMarkers(incidents) {
    incidents.forEach(incident => {
        if (incident.latitude && incident.longitude) {
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
        }
    });
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
        'minor': '<span class="badge badge-info badge-sm gap-1"><i class="fas fa-circle"></i> Minor</span>',
        'moderate': '<span class="badge badge-primary badge-sm gap-1"><i class="fas fa-circle"></i> Moderate</span>',
        'severe': '<span class="badge badge-warning badge-sm gap-1"><i class="fas fa-circle"></i> Severe</span>',
        'critical': '<span class="badge badge-error badge-sm gap-1"><i class="fas fa-circle"></i> Critical</span>'
    };

    // Format the date safely
    let dateHtml = '';
    if (incident.incident_datetime) {
        const date = new Date(incident.incident_datetime);
        const formattedDate = date.toLocaleDateString('en-US', {
            timeZone: 'Asia/Manila',
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        dateHtml = `
            <div class="popup-info-row">
                <span class="popup-label"><i class="fas fa-calendar-alt"></i> Date:</span>
                <span class="popup-value">${formattedDate}</span>
            </div>`;
    }

    const incidentType = incident.incident_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

    return `
        <div class="popup-container">
            <h6 class="popup-header">${incident.incident_number}</h6>
            <div class="popup-info-row">
                <span class="popup-label"><i class="fas fa-tag"></i> Type:</span>
                <span class="popup-value">${incidentType}</span>
            </div>
            <div class="popup-info-row">
                <span class="popup-label"><i class="fas fa-map-marker-alt"></i> Location:</span>
                <span class="popup-value">${incident.location}</span>
            </div>
            <div class="popup-info-row">
                <span class="popup-label"><i class="fas fa-exclamation-triangle"></i> Severity:</span>
                <span class="popup-value">${severityBadges[incident.severity_level] || incident.severity_level}</span>
            </div>
            ${dateHtml}
            <a href="/incidents/${incident.id}" class="btn btn-primary popup-button ">
                <i class="fas fa-eye mr-2 white-text"></i>View Full Details
            </a>
        </div>
    `;
}

// New function to create tooltip content with image preview - Enhanced Typography
function createTooltipContent(incident) {
    // Get the first photo if available
    let photoHtml = '';
    if (incident.photos && incident.photos.length > 0) {
        const firstPhoto = incident.photos[0];
        const photoSrc = `/storage/${firstPhoto}`;
        photoHtml = `<div class="tooltip-image">
                        <img src="${photoSrc}" alt="Incident photo" style="width: 100%; height: 80px; object-fit: cover;">
                     </div>`;
    }

    // Get victim count if available
    const victimCount = incident.victims ? incident.victims.length : 0;
    const victimHtml = victimCount > 0 ? `<div class="tooltip-victims"><i class="fas fa-user-injured"></i> ${victimCount} victim${victimCount > 1 ? 's' : ''}</div>` : '';

    // Get severity badge with icons
    const severityBadges = {
        'minor': '<span class="badge badge-info badge-xs gap-1"><i class="fas fa-circle"></i> Minor</span>',
        'moderate': '<span class="badge badge-primary badge-xs gap-1"><i class="fas fa-circle"></i> Moderate</span>',
        'severe': '<span class="badge badge-warning badge-xs gap-1"><i class="fas fa-circle"></i> Severe</span>',
        'critical': '<span class="badge badge-error badge-xs gap-1"><i class="fas fa-circle"></i> Critical</span>'
    };
    const severityHtml = severityBadges[incident.severity_level] || `<span class="badge badge-ghost badge-xs">${incident.severity_level}</span>`;

    // Format the date safely
    let dateHtml = '';
    if (incident.incident_datetime) {
        const date = new Date(incident.incident_datetime);
        const formattedDate = date.toLocaleDateString('en-US', {
            timeZone: 'Asia/Manila',
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
        dateHtml = `<div class="tooltip-date"><i class="fas fa-calendar"></i> ${formattedDate}</div>`;
    }

    const incidentType = incident.incident_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

    return `
        <div class="tooltip-content">
            ${photoHtml}
            <div class="tooltip-title">${incident.incident_number}</div>
            <div class="tooltip-type"><i class="fas fa-tag"></i> ${incidentType}</div>
            <div class="tooltip-location"><i class="fas fa-map-marker-alt"></i> ${incident.location}</div>
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
    const incidentType = document.getElementById('incidentTypeFilter').value;
    const severity = document.getElementById('severityFilter').value;
    const municipality = document.getElementById('municipalityFilter').value;

    // TODO: Implement actual filtering logic here
    // For now, just show a toast notification
    showSuccessToast('Filters applied successfully');

    console.log('Applied filters:', {
        incidentType,
        severity,
        municipality
    });
}

function clearFilters() {
    document.getElementById('incidentTypeFilter').value = '';
    document.getElementById('severityFilter').value = '';
    document.getElementById('municipalityFilter').value = '';
    showInfoToast('Filters cleared');
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});
</script>
@endsection
