@extends("Layouts.app")

@section('title', 'Incident Details - ' . $incident->incident_number)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl" role="main">
    <!-- Header Section -->
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-error" aria-hidden="true"></i>
                <span>Incident <span class="font-mono text-primary">{{ $incident->incident_number }}</span></span>
            </h1>
            <p class="text-base text-gray-600 mt-2">{{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }}</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full lg:w-auto">
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <a href="{{ route('incidents.edit', $incident) }}"
                   class="btn btn-primary gap-2 w-full sm:w-auto min-h-[44px]"
                   aria-label="Edit incident {{ $incident->incident_number }}">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                    <span>Edit Incident</span>
                </a>
            @endif
            <a href="{{ route('incidents.index') }}"
               class="btn btn-outline gap-2 w-full sm:w-auto min-h-[44px]"
               aria-label="Back to incidents list">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Back to List</span>
            </a>
            <button type="button"
                    onclick="window.print()"
                    class="btn btn-ghost gap-2 w-full sm:w-auto min-h-[44px]"
                    aria-label="Print incident report">
                <i class="fas fa-print" aria-hidden="true"></i>
                <span>Print</span>
            </button>
        </div>
    </header>

    <!-- Status Alert -->
    @if($incident->status === 'critical' || $incident->severity_level === 'critical')
        <div class="alert alert-error shadow-lg mb-6" role="alert">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl" aria-hidden="true"></i>
                <div>
                    <h3 class="font-bold text-lg">Critical Incident</h3>
                    <p class="text-sm">This incident requires immediate attention.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <section class="card bg-white shadow-lg" aria-labelledby="basic-info-heading">
                <div class="card-body">
                    <h2 id="basic-info-heading" class="card-title text-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-primary" aria-hidden="true"></i>
                        <span>Basic Information</span>
                    </h2>
                    <div class="divider my-2"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Incident Number</span>
                            </label>
                            <div class="font-mono text-lg font-bold text-primary">{{ $incident->incident_number }}</div>
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Status</span>
                            </label>
                            <div class="flex items-center gap-2">
                                @if($incident->status === 'active')
                                    <i class="fas fa-spinner fa-pulse text-info" aria-hidden="true"></i>
                                @elseif($incident->status === 'resolved')
                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                                @elseif($incident->status === 'closed')
                                    <i class="fas fa-lock text-neutral" aria-hidden="true"></i>
                                @else
                                    <i class="fas fa-clock text-warning" aria-hidden="true"></i>
                                @endif
                                <span class="badge {{ $incident->status_badge }} badge-lg">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Severity Level</span>
                            </label>
                            <div class="flex items-center gap-2">
                                @if($incident->severity_level === 'critical')
                                    <i class="fas fa-exclamation-triangle text-error" aria-hidden="true"></i>
                                @elseif($incident->severity_level === 'high')
                                    <i class="fas fa-exclamation-circle text-warning" aria-hidden="true"></i>
                                @elseif($incident->severity_level === 'medium')
                                    <i class="fas fa-info-circle text-info" aria-hidden="true"></i>
                                @else
                                    <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                                @endif
                                <span class="badge {{ $incident->severity_level === 'critical' ? 'badge-error' : ($incident->severity_level === 'high' ? 'badge-warning' : ($incident->severity_level === 'medium' ? 'badge-info' : 'badge-success')) }} badge-lg">
                                    {{ ucfirst($incident->severity_level) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Date & Time</span>
                            </label>
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-500" aria-hidden="true"></i>
                                <span>{{ $incident->formatted_incident_date }}</span>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-error" aria-hidden="true"></i>
                                    Location
                                </span>
                            </label>
                            <div class="text-gray-700">
                                <div class="font-medium">{{ $incident->location }}</div>
                                @if($incident->barangay)
                                    <div class="text-sm text-gray-600 mt-1">Barangay: {{ $incident->barangay }}</div>
                                @endif
                                <div class="text-sm text-gray-600 mt-1">Municipality: {{ $incident->municipality }}</div>
                            </div>
                        </div>

                        @if($incident->latitude && $incident->longitude)
                            <div class="md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">GPS Coordinates</span>
                                </label>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <span class="text-sm font-mono text-gray-700">
                                        Lat: {{ number_format($incident->latitude, 6) }}, Lng: {{ number_format($incident->longitude, 6) }}
                                    </span>
                                    {{-- navigate it my heat_map view not on google map  --}}
                                    <a href="https://maps.google.com?q={{ $incident->latitude }},{{ $incident->longitude }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-sm btn-outline gap-2 min-h-[44px]"
                                       aria-label="View location on map (opens in new window)">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        <span>View on Map</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="card bg-white shadow-lg" aria-labelledby="description-heading">
                <div class="card-body">
                    <h2 id="description-heading" class="card-title text-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt text-success" aria-hidden="true"></i>
                        <span>Incident Description</span>
                    </h2>
                    <div class="divider my-2"></div>
                    <div class="prose max-w-none text-base text-gray-700 leading-relaxed">
                        {{ $incident->description }}
                    </div>
                </div>
            </section>

            <!-- Incident Type Specific Details -->
            @include('Components.IncidentShow.TrafficAccidentDetails')
            @include('Components.IncidentShow.MedicalEmergencyDetails')
            @include('Components.IncidentShow.FireIncidentDetails')
            @include('Components.IncidentShow.NaturalDisasterDetails')
            @include('Components.IncidentShow.CriminalActivityDetails')

            <!-- Media Gallery -->
            @include('Components.IncidentShow.MediaGallery')

            <!-- Conditions & Details -->
            <section class="card bg-white shadow-lg" aria-labelledby="conditions-heading">
                <div class="card-body">
                    <h2 id="conditions-heading" class="card-title text-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-secondary" aria-hidden="true"></i>
                        <span>Environmental Conditions & Damage</span>
                    </h2>
                    <div class="divider my-2"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($incident->weather_condition)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Weather Condition</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cloud text-info" aria-hidden="true"></i>
                                    <span class="badge badge-info badge-lg">{{ ucfirst($incident->weather_condition) }}</span>
                                </div>
                            </div>
                        @endif

                        @if($incident->road_condition)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Road Condition</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-road text-warning" aria-hidden="true"></i>
                                    <span class="badge badge-warning badge-lg">{{ ucfirst(str_replace('_', ' ', $incident->road_condition)) }}</span>
                                </div>
                            </div>
                        @endif

                        @if($incident->casualty_count > 0)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Total Casualties</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-injured text-error" aria-hidden="true"></i>
                                    <span class="text-error font-bold text-2xl">{{ $incident->casualty_count }}</span>
                                </div>
                            </div>
                        @endif

                        @if($incident->injury_count > 0)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Injuries</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-ambulance text-warning" aria-hidden="true"></i>
                                    <span class="text-warning font-bold text-2xl">{{ $incident->injury_count }}</span>
                                </div>
                            </div>
                        @endif

                        @if($incident->fatality_count > 0)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Fatalities</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-heart-broken text-error" aria-hidden="true"></i>
                                    <span class="text-error font-bold text-2xl">{{ $incident->fatality_count }}</span>
                                </div>
                            </div>
                        @endif

                        @if($incident->property_damage_estimate)
                            <div>
                                <label class="label">
                                    <span class="label-text font-semibold text-gray-700">Property Damage Estimate</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building text-success" aria-hidden="true"></i>
                                    <span class="text-success font-bold text-xl">₱{{ number_format($incident->property_damage_estimate, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($incident->damage_description)
                        <div class="mt-6">
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Damage Description</span>
                            </label>
                            <div class="bg-base-200 p-4 rounded-lg border border-base-300 text-gray-700 leading-relaxed">
                                {{ $incident->damage_description }}
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Enhanced Victims List -->
            @include('Components.IncidentShow.VictimsList')

            <!-- Resolution Notes -->
            @if($incident->resolution_notes && in_array($incident->status, ['resolved', 'closed']))
                <section class="card bg-white shadow-lg" aria-labelledby="resolution-heading">
                    <div class="card-body">
                        <h2 id="resolution-heading" class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                            <span>Resolution Notes</span>
                        </h2>
                        <div class="divider my-2"></div>
                        <div class="alert alert-success shadow-sm">
                            <div class="flex flex-col gap-2">
                                <p class="text-base leading-relaxed">{{ $incident->resolution_notes }}</p>
                                @if($incident->resolved_at)
                                    <div class="text-sm flex items-center gap-2 opacity-80">
                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                        <span>Resolved on: {{ $incident->resolved_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Activity Timeline -->
            <section class="card bg-white shadow-lg" aria-labelledby="timeline-heading">
                <div class="card-body">
                    <h2 id="timeline-heading" class="card-title text-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-accent" aria-hidden="true"></i>
                        <span>Activity Timeline</span>
                    </h2>
                    <div class="divider my-2"></div>

                    <ul class="timeline timeline-vertical">
                        <li>
                            <div class="timeline-start timeline-box">
                                <div class="font-bold text-gray-900">Incident Reported</div>
                                <div class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    <span>{{ $incident->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                @if($incident->reporter)
                                    <div class="text-sm text-gray-700 mt-1 flex items-center gap-2">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                        <span>{{ $incident->reporter->first_name }} {{ $incident->reporter->last_name }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="timeline-middle">
                                <i class="fas fa-plus-circle text-primary text-xl" aria-hidden="true"></i>
                            </div>
                            <hr class="bg-primary" />
                        </li>

                        @if($incident->assigned_staff_id)
                            <li>
                                <hr class="bg-primary" />
                                <div class="timeline-middle">
                                    <i class="fas fa-user-check text-success text-xl" aria-hidden="true"></i>
                                </div>
                                <div class="timeline-end timeline-box">
                                    <div class="font-bold text-gray-900">Staff Assigned</div>
                                    @if($incident->assignedStaff)
                                        <div class="text-sm text-gray-700 mt-1 flex items-center gap-2">
                                            <i class="fas fa-user" aria-hidden="true"></i>
                                            <span>{{ $incident->assignedStaff->first_name }} {{ $incident->assignedStaff->last_name }}</span>
                                        </div>
                                    @endif
                                </div>
                                <hr class="bg-success" />
                            </li>
                        @endif

                        @if($incident->assigned_vehicle_id)
                            <li>
                                <hr class="bg-success" />
                                <div class="timeline-start timeline-box">
                                    <div class="font-bold text-gray-900">Vehicle Dispatched</div>
                                    @if($incident->assignedVehicle)
                                        <div class="text-sm text-gray-700 mt-1 flex items-center gap-2">
                                            <i class="fas fa-truck" aria-hidden="true"></i>
                                            <span>{{ $incident->assignedVehicle->vehicle_number }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="timeline-middle">
                                    <i class="fas fa-truck text-warning text-xl" aria-hidden="true"></i>
                                </div>
                                <hr class="bg-warning" />
                            </li>
                        @endif

                        @if(in_array($incident->status, ['resolved', 'closed']))
                            <li>
                                <hr class="bg-warning" />
                                <div class="timeline-middle">
                                    <i class="fas fa-check-circle text-success text-xl" aria-hidden="true"></i>
                                </div>
                                <div class="timeline-end timeline-box">
                                    <div class="font-bold text-gray-900">Incident {{ ucfirst($incident->status) }}</div>
                                    @if($incident->resolved_at)
                                        <div class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                            <span>{{ $incident->resolved_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6" aria-label="Incident sidebar information">
            <!-- Quick Stats -->
            <section class="stats stats-vertical shadow-lg w-full bg-white" aria-label="Quick statistics">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-calendar text-2xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Reported</div>
                    <div class="stat-value text-primary text-lg">{{ $incident->created_at->diffForHumans() }}</div>
                    <div class="stat-desc text-gray-500">{{ $incident->created_at->format('M d, Y H:i') }}</div>
                </div>

                @if($incident->victims->count() > 0)
                    <div class="stat">
                        <div class="stat-figure text-error">
                            <i class="fas fa-user-injured text-2xl" aria-hidden="true"></i>
                        </div>
                        <div class="stat-title text-gray-600">Victims</div>
                        <div class="stat-value text-error">{{ $incident->victims->count() }}</div>
                        <div class="stat-desc text-gray-500">
                            @if($incident->injury_count > 0)
                                {{ $incident->injury_count }} injured
                            @endif
                            @if($incident->fatality_count > 0)
                                @if($incident->injury_count > 0), @endif
                                {{ $incident->fatality_count }} deceased
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <!-- Assignment Information -->
            <section class="card bg-white shadow-lg" aria-labelledby="assignment-heading">
                <div class="card-body">
                    <h3 id="assignment-heading" class="card-title text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-primary" aria-hidden="true"></i>
                        <span>Assignment</span>
                    </h3>
                    <div class="divider my-2"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Reported By</span>
                            </label>
                            @if($incident->reporter)
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center">
                                            <span class="text-sm font-bold">{{ substr($incident->reporter->first_name, 0, 1) }}{{ substr($incident->reporter->last_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <span class="text-gray-700">{{ $incident->reporter->first_name }} {{ $incident->reporter->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-500 italic">Unknown</span>
                            @endif
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Assigned Staff</span>
                            </label>
                            @if($incident->assignedStaff)
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-success text-white rounded-full w-10 h-10 flex items-center justify-center">
                                            <span class="text-sm font-bold">{{ substr($incident->assignedStaff->first_name, 0, 1) }}{{ substr($incident->assignedStaff->last_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <span class="text-gray-700">{{ $incident->assignedStaff->first_name }} {{ $incident->assignedStaff->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-500 italic">Unassigned</span>
                            @endif
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Assigned Vehicle</span>
                            </label>
                            @if($incident->assignedVehicle)
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-truck text-warning" aria-hidden="true"></i>
                                        <a href="{{ route('vehicles.show', $incident->assignedVehicle) }}"
                                           class="link link-primary hover:link-hover font-medium"
                                           aria-label="View vehicle {{ $incident->assignedVehicle->vehicle_number }} details">
                                            {{ $incident->assignedVehicle->vehicle_number }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-600 ml-6">{{ $incident->assignedVehicle->vehicle_type }}</div>
                                </div>
                            @else
                                <span class="text-gray-500 italic">No vehicle assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Status Update -->
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <section class="card bg-white shadow-lg" aria-labelledby="status-update-heading">
                    <div class="card-body">
                        <h3 id="status-update-heading" class="card-title text-lg mb-4 flex items-center gap-2">
                            <i class="fas fa-edit text-success" aria-hidden="true"></i>
                            <span>Quick Status Update</span>
                        </h3>
                        <div class="divider my-2"></div>

                        <form action="{{ route('incidents.update', $incident) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="maintain_other_fields" value="1">

                            <div class="form-control">
                                <label for="status-select" class="label">
                                    <span class="label-text font-semibold text-gray-700">Status</span>
                                </label>
                                <select name="status" id="status-select" class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                    <option value="pending" {{ $incident->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $incident->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label for="resolution-notes" class="label">
                                    <span class="label-text font-semibold text-gray-700">Resolution Notes</span>
                                </label>
                                <textarea name="resolution_notes"
                                          id="resolution-notes"
                                          placeholder="Add notes about the resolution..."
                                          class="textarea textarea-bordered w-full focus:outline-primary"
                                          rows="3">{{ $incident->resolution_notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-full gap-2 min-h-[44px]">
                                <i class="fas fa-save" aria-hidden="true"></i>
                                <span>Update Status</span>
                            </button>
                        </form>
                    </div>
                </section>
            @endif

            <!-- Quick Actions -->
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <section class="card bg-white shadow-lg" aria-labelledby="quick-actions-heading">
                    <div class="card-body">
                        <h3 id="quick-actions-heading" class="card-title text-lg mb-4 flex items-center gap-2">
                            <i class="fas fa-bolt text-warning" aria-hidden="true"></i>
                            <span>Quick Actions</span>
                        </h3>
                        <div class="divider my-2"></div>

                        <div class="space-y-3">
                            <a href="{{ route('incidents.edit', $incident) }}"
                               class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                               aria-label="Edit incident details">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                                <span>Edit Details</span>
                            </a>

                            <a href="{{ route('victims.create', ['incident_id' => $incident->id]) }}"
                               class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                               aria-label="Add new victim to this incident">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                <span>Add Victim</span>
                            </a>

                            <button type="button"
                                    onclick="window.print()"
                                    class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                                    aria-label="Print incident report">
                                <i class="fas fa-print" aria-hidden="true"></i>
                                <span>Print Report</span>
                            </button>

                            @if($incident->latitude && $incident->longitude)
                            {{-- redirect to it to my heat-maps view --}}
                                <a href="https://maps.google.com?q={{ $incident->latitude }},{{ $incident->longitude }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                                   aria-label="Navigate to incident location (opens in new window)">
                                    <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                                    <span>Navigate</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            <!-- Export Options -->
            <section class="card bg-white shadow-lg" aria-labelledby="export-heading">
                <div class="card-body">
                    <h3 id="export-heading" class="card-title text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-download text-secondary" aria-hidden="true"></i>
                        <span>Export</span>
                    </h3>
                    <div class="divider my-2"></div>

                    <div class="space-y-3">
                        <button type="button"
                                onclick="window.print()"
                                class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                                aria-label="Export incident as PDF">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                            <span>Export as PDF</span>
                        </button>

                        <button type="button"
                                class="btn btn-outline w-full gap-2 min-h-[44px] justify-start"
                                onclick="alert('CSV export functionality coming soon!')"
                                aria-label="Export incident as CSV (coming soon)">
                            <i class="fas fa-file-csv" aria-hidden="true"></i>
                            <span>Export as CSV</span>
                        </button>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

<style>
    @media print {
        .btn, .alert, nav, footer, [onclick*="print"] {
            display: none !important;
        }

        .card {
            page-break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .timeline {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
