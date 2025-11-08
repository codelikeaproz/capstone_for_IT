@extends("Layouts.app")

@section('title', 'Incident Details - ' . $incident->incident_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                Incident {{ $incident->incident_number }}
            </h1>
            <p class="text-gray-600 mt-1">{{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }}</p>
        </div>
        
        <div class="flex space-x-3">
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Incident
                </a>
            @endif
            <a href="{{ route('incidents.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if($incident->status === 'critical' || $incident->severity_level === 'critical')
        <div class="alert alert-error mb-6">
            <i class="fas fa-exclamation-circle"></i>
            <span class="font-semibold">Critical Incident</span>
            <span>This incident requires immediate attention.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label font-semibold">Incident Number</label>
                            <div class="font-mono text-lg text-blue-600">{{ $incident->incident_number }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Status</label>
                            <div class="badge {{ $incident->status_badge }} badge-lg">
                                {{ ucfirst($incident->status) }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Severity Level</label>
                            <div class="badge {{ $incident->severity_level === 'critical' ? 'badge-error' : ($incident->severity_level === 'high' ? 'badge-warning' : ($incident->severity_level === 'medium' ? 'badge-info' : 'badge-success')) }} badge-lg">
                                {{ ucfirst($incident->severity_level) }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Date & Time</label>
                            <div>{{ $incident->formatted_incident_date }}</div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="label font-semibold">Location</label>
                            <div>{{ $incident->location }}</div>
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Municipality</label>
                            <div>{{ $incident->municipality }}</div>
                        </div>
                        
                        @if($incident->latitude && $incident->longitude)
                            <div>
                                <label class="label font-semibold">GPS Coordinates</label>
                                <div class="text-sm">
                                    Lat: {{ $incident->latitude }}, Lng: {{ $incident->longitude }}
                                    <a href="https://maps.google.com?q={{ $incident->latitude }},{{ $incident->longitude }}" 
                                       target="_blank" class="btn btn-sm btn-outline ml-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>View on Map
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Photos Gallery -->
            @if($incident->photos && count($incident->photos) > 0)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-images text-green-500"></i>
                            Incident Photos
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($incident->photos as $photo)
                                <div class="aspect-square overflow-hidden rounded-lg border">
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         alt="Incident photo" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-file-alt text-green-500"></i>
                        Description
                    </h2>
                    <div class="prose max-w-none">
                        {{ $incident->description }}
                    </div>
                </div>
            </div>

            <!-- Conditions & Details -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">
                        <i class="fas fa-clipboard-list text-purple-500"></i>
                        Conditions & Details
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($incident->weather_condition)
                            <div>
                                <label class="label font-semibold">Weather Condition</label>
                                <div class="badge badge-info">{{ ucfirst($incident->weather_condition) }}</div>
                            </div>
                        @endif
                        
                        @if($incident->road_condition)
                            <div>
                                <label class="label font-semibold">Road Condition</label>
                                <div class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $incident->road_condition)) }}</div>
                            </div>
                        @endif
                        
                        @if($incident->casualty_count > 0)
                            <div>
                                <label class="label font-semibold">Casualties</label>
                                <div class="text-red-600 font-bold">{{ $incident->casualty_count }}</div>
                            </div>
                        @endif
                        
                        @if($incident->injury_count > 0)
                            <div>
                                <label class="label font-semibold">Injuries</label>
                                <div class="text-orange-600 font-bold">{{ $incident->injury_count }}</div>
                            </div>
                        @endif
                        
                        @if($incident->fatality_count > 0)
                            <div>
                                <label class="label font-semibold">Fatalities</label>
                                <div class="text-red-800 font-bold">{{ $incident->fatality_count }}</div>
                            </div>
                        @endif
                        
                        @if($incident->property_damage_estimate)
                            <div>
                                <label class="label font-semibold">Property Damage Estimate</label>
                                <div class="text-green-600 font-bold">₱{{ number_format($incident->property_damage_estimate, 2) }}</div>
                            </div>
                        @endif
                    </div>
                    
                    @if($incident->vehicle_involved)
                        <div class="mt-4">
                            <label class="label font-semibold">Vehicle Details</label>
                            <div class="bg-gray-100 p-3 rounded">
                                {{ $incident->vehicle_details ?: 'Vehicle involved but no details provided' }}
                            </div>
                        </div>
                    @endif
                    
                    @if($incident->damage_description)
                        <div class="mt-4">
                            <label class="label font-semibold">Damage Description</label>
                            <div class="bg-gray-100 p-3 rounded">
                                {{ $incident->damage_description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resolution Notes -->
            @if($incident->resolution_notes && in_array($incident->status, ['resolved', 'closed']))
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Resolution Notes
                        </h2>
                        <div class="bg-green-50 p-4 rounded border border-green-200">
                            {{ $incident->resolution_notes }}
                        </div>
                        @if($incident->resolved_at)
                            <div class="text-sm text-gray-600 mt-2">
                                Resolved on: {{ $incident->resolved_at->format('M d, Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Assignment Information -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <i class="fas fa-users text-blue-500"></i>
                        Assignment
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="label font-semibold">Reported By</label>
                            @if($incident->reporter)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-blue-500"></i>
                                    <span>{{ $incident->reporter->first_name }} {{ $incident->reporter->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-500">Unknown</span>
                            @endif
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Assigned Staff</label>
                            @if($incident->assignedStaff)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user-tie text-green-500"></i>
                                    <span>{{ $incident->assignedStaff->first_name }} {{ $incident->assignedStaff->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-500">Unassigned</span>
                            @endif
                        </div>
                        
                        <div>
                            <label class="label font-semibold">Assigned Vehicle</label>
                            @if($incident->assignedVehicle)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-truck text-orange-500"></i>
                                    <a href="{{ route('vehicles.show', $incident->assignedVehicle) }}" class="text-blue-600 hover:underline">
                                        {{ $incident->assignedVehicle->vehicle_number }}
                                    </a>
                                </div>
                            @else
                                <span class="text-gray-500">No vehicle assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Update -->
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-edit text-green-500"></i>
                            Quick Status Update
                        </h3>
                        
                        <form action="{{ route('incidents.update', $incident) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="maintain_other_fields" value="1">
                            
                            <div class="form-control mb-3">
                                <select name="status" class="select select-bordered">
                                    <option value="pending" {{ $incident->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $incident->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            
                            <div class="form-control mb-3">
                                <textarea name="resolution_notes" placeholder="Add notes..." class="textarea textarea-bordered" rows="3">{{ $incident->resolution_notes }}</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save mr-2"></i>Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Victims -->
            @if($incident->victims->count() > 0)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-lg mb-4">
                            <i class="fas fa-heartbeat text-red-500"></i>
                            Victims ({{ $incident->victims->count() }})
                        </h3>
                        
                        <div class="space-y-2">
                            @foreach($incident->victims as $victim)
                                <div class="p-3 bg-gray-50 rounded border">
                                    <div class="font-semibold">{{ $victim->full_name }}</div>
                                    <div class="text-sm text-gray-600">
                                        Age: {{ $victim->age }} | 
                                        Status: <span class="badge badge-sm {{ $victim->medical_status === 'critical' ? 'badge-error' : ($victim->medical_status === 'stable' ? 'badge-success' : 'badge-warning') }}">
                                            {{ ucfirst($victim->medical_status) }}
                                        </span>
                                    </div>
                                    @if($victim->injury_type)
                                        <div class="text-sm text-gray-600">{{ $victim->injury_type }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ route('victims.create', ['incident_id' => $incident->id]) }}" class="btn btn-outline btn-sm mt-3">
                            <i class="fas fa-plus mr-2"></i>Add Victim
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection