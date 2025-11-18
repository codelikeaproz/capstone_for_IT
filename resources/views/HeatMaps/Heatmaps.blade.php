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
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-6 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-map-marked-alt text-primary" aria-hidden="true"></i>
                        <span>Emergency Heat Map</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Visual incident analysis and monitoring across Bukidnon</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <button type="button" class="btn btn-success gap-2 w-full sm:w-auto min-h-[44px]" onclick="refreshMap()" aria-label="Refresh heat map">
                        <i class="fas fa-redo" aria-hidden="true"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" role="region" aria-label="Heat map statistics">
            {{-- Total Incidents --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-exclamation-triangle text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Incidents</div>
                    <div class="stat-value text-primary">{{ number_format($totalIncidents ?? 0) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Mapped locations</div>
                </div>
            </div>

            {{-- This Month --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-calendar-alt text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">This Month</div>
                    <div class="stat-value text-info">{{ number_format($monthlyIncidents ?? 0) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Recent activity</div>
                </div>
            </div>

            {{-- High Density Areas --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-chart-area text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">High Density Areas</div>
                    <div class="stat-value text-warning">{{ number_format($hotspots ?? 0) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Hotspot zones</div>
                </div>
            </div>

            {{-- Mapped Locations --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-map-pin text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Mapped Locations</div>
                    <div class="stat-value text-success">{{ number_format($mappedIncidents ?? 0) }}</div>
                    <div class="stat-desc text-sm text-gray-500">With coordinates</div>
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
        {{-- Main Table Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="px-4 py-6 border-b border-gray-200">
                    <div class="flex flex-row justify-between gap-6">
                        <div class="flex-shrink-0">
                            <h2 class="text-xl font-semibold text-gray-800">Recent Incidents on Map</h2>
                            <p class="text-sm text-gray-500 mt-2">
                                @if(isset($recentIncidents) && method_exists($recentIncidents, 'total'))
                                    Showing {{ $recentIncidents->firstItem() ?? 0 }} to {{ $recentIncidents->lastItem() ?? 0 }} of {{ number_format($recentIncidents->total()) }} results
                                @else
                                    {{ isset($recentIncidents) ? $recentIncidents->count() : 0 }} incidents
                                @endif
                            </p>
                        </div>
                        <form method="GET" action="{{ route('heatmaps') }}" class="flex-shrink-0 lg:ml-auto">
                            <div class="flex flex-wrap items-end gap-3">
                                {{-- Search Input --}}
                                <div class="form-control">
                                    <label for="search" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Search</span>
                                    </label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                           placeholder="Incident #, location..."
                                           class="input input-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                </div>

                                {{-- Emergency Type (Incident Type) Filter --}}
                                <div class="form-control">
                                    <label for="incident_type" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Emergency Type</span>
                                    </label>
                                    <select name="incident_type" id="incident_type" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('incident_type') === '' ? 'selected' : '' }}>All Types</option>
                                        <option value="vehicle_vs_vehicle" {{ request('incident_type') === 'vehicle_vs_vehicle' ? 'selected' : '' }}>Vehicle Collision</option>
                                        <option value="vehicle_vs_pedestrian" {{ request('incident_type') === 'vehicle_vs_pedestrian' ? 'selected' : '' }}>Vehicle vs Pedestrian</option>
                                        <option value="vehicle_vs_animals" {{ request('incident_type') === 'vehicle_vs_animals' ? 'selected' : '' }}>Vehicle vs Animals</option>
                                        <option value="vehicle_vs_property" {{ request('incident_type') === 'vehicle_vs_property' ? 'selected' : '' }}>Vehicle vs Property</option>
                                        <option value="vehicle_alone" {{ request('incident_type') === 'vehicle_alone' ? 'selected' : '' }}>Single Vehicle</option>
                                        <option value="maternity" {{ request('incident_type') === 'maternity' ? 'selected' : '' }}>Medical Emergency</option>
                                        <option value="stabbing_shooting" {{ request('incident_type') === 'stabbing_shooting' ? 'selected' : '' }}>Violence Emergency</option>
                                        <option value="transport_to_hospital" {{ request('incident_type') === 'transport_to_hospital' ? 'selected' : '' }}>Medical Transport</option>
                                    </select>
                                </div>

                                {{-- Municipality Filter (SuperAdmin Only) --}}
                                @if(Auth::user()->isSuperAdmin())
                                <div class="form-control">
                                    <label for="municipality" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Municipality</span>
                                    </label>
                                    <select name="municipality" id="municipality" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ request('municipality') === '' ? 'selected' : '' }}>All Municipalities</option>
                                        @foreach(config('locations.municipalities') as $municipality => $value)
                                            <option value="{{ $municipality }}" {{ request('municipality') === $municipality ? 'selected' : '' }}>
                                                {{ $municipality }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Filter Actions --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700 opacity-0">Actions</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn btn-primary gap-2 min-h-[44px] px-6">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <span>Apply</span>
                                        </button>
                                        <a href="{{ route('heatmaps') }}" class="btn btn-outline gap-2 min-h-[44px]" aria-label="Clear all filters">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>Clear</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Filters Display --}}
                            @if(request('search') || request('municipality') || request('incident_type'))
                            <div class="flex items-center gap-2 flex-wrap mt-3">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                                @if(request('search'))
                                    <span class="badge badge-primary gap-1">
                                        <span>Search: "{{ request('search') }}"</span>
                                    </span>
                                @endif
                                @if(request('municipality'))
                                    <span class="badge badge-secondary gap-1">
                                        <span>{{ request('municipality') }}</span>
                                    </span>
                                @endif
                                @if(request('incident_type'))
                                    <span class="badge badge-info gap-1">
                                        <span>{{ ucwords(str_replace('_', ' ', request('incident_type'))) }}</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if(isset($recentIncidents) && $recentIncidents->count() > 0)
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="font-semibold text-gray-700">Incident #</th>
                                    <th class="font-semibold text-gray-700">Emergency Type</th>
                                    <th class="font-semibold text-gray-700">Location</th>
                                    <th class="font-semibold text-gray-700">Date & Time</th>
                                    <th class="font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIncidents as $incident)
                                <tr class="hover cursor-pointer" data-incident-id="{{ $incident->id }}" onclick="centerMapOnIncident({{ $incident->latitude }}, {{ $incident->longitude }})">
                                    {{-- Incident Number --}}
                                    <td>
                                        <span class="font-mono font-bold text-primary text-base">{{ $incident->incident_number }}</span>
                                    </td>

                                    {{-- Emergency Type --}}
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
                                        <span class="badge {{ $badgeColor }} gap-1">
                                            {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                        </span>
                                    </td>

                                    {{-- Location --}}
                                    <td>
                                        <div class="text-sm text-gray-700">
                                            <div class="font-medium">{{ Str::limit($incident->location, 40) }}</div>
                                            @if($incident->municipality)
                                                <div class="text-xs text-gray-500">{{ $incident->municipality }}</div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Date & Time --}}
                                    <td>
                                        <div class="text-sm text-gray-700">
                                            @if($incident->incident_date)
                                                <div class="font-medium">{{ $incident->incident_date->timezone('Asia/Manila')->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $incident->incident_date->timezone('Asia/Manila')->format('h:i A') }}</div>
                                            @else
                                                <span class="text-gray-400 italic">N/A</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @switch($incident->status)
                                                @case('pending')
                                                    <i class="fas fa-clock text-warning" aria-hidden="true"></i>
                                                    @break
                                                @case('responding')
                                                    <i class="fas fa-truck-medical text-info" aria-hidden="true"></i>
                                                    @break
                                                @case('resolved')
                                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                                                    @break
                                                @case('closed')
                                                    <i class="fas fa-archive text-gray-500" aria-hidden="true"></i>
                                                    @break
                                            @endswitch
                                            @switch($incident->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    @break
                                                @case('responding')
                                                    <span class="badge badge-info">Responding</span>
                                                    @break
                                                @case('resolved')
                                                    <span class="badge badge-success">Resolved</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge badge-ghost">Closed</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-ghost">{{ ucfirst($incident->status) }}</span>
                                            @endswitch
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Pagination --}}
                        @if($recentIncidents->hasPages())
                            <div class="border-t border-gray-200 px-6 py-4">
                                {{ $recentIncidents->links() }}
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-16 px-4">
                            <i class="fas fa-map-marker-alt text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Incidents Found</h3>
                            <p class="text-gray-500 mb-6">
                                @if(request('search') || request('municipality') || request('incident_type'))
                                    No incidents match your current filters. Try adjusting your search criteria.
                                @else
                                    No incidents with location data are available at this time.
                                @endif
                            </p>
                            @if(request('search') || request('municipality') || request('incident_type'))
                                <a href="{{ route('heatmaps') }}" class="btn btn-outline gap-2">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    <span>Clear Filters</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
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

function refreshMap() {
    location.reload();
}

function updateMapInfo() {
    document.getElementById('mapZoom').textContent = map.getZoom();
    const center = map.getCenter();
    document.getElementById('mapCenter').textContent = `${center.lat.toFixed(4)}, ${center.lng.toFixed(4)}`;
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});
</script>
@endsection
