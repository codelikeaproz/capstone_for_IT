@extends("Layouts.app")

@section('title', 'Edit Incident - ' . $incident->incident_number)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-edit text-primary mr-3"></i>
                Edit Incident
            </h1>
            <p class="text-gray-600 mt-1">{{ $incident->incident_number }} • {{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }}</p>
        </div>
        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form (2 columns) -->
        <div class="lg:col-span-2">
            <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data" id="incident-form">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Core Information -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-xl border-b pb-3 mb-4">
                                <i class="fas fa-clipboard-list text-primary"></i>
                                Core Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Incident Type -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Incident Type <span class="text-error">*</span></span>
                                    </label>
                                    <select name="incident_type" id="incident_type" class="select select-bordered @error('incident_type') select-error @enderror" required>
                                        <option value="">Select type</option>
                                <option value="traffic_accident" {{ old('incident_type', $incident->incident_type) == 'traffic_accident' ? 'selected' : '' }}>🚗 Traffic Accident</option>
                                <option value="medical_emergency" {{ old('incident_type', $incident->incident_type) == 'medical_emergency' ? 'selected' : '' }}>🚑 Medical Emergency</option>
                                <option value="fire_incident" {{ old('incident_type', $incident->incident_type) == 'fire_incident' ? 'selected' : '' }}>🔥 Fire Incident</option>
                                <option value="natural_disaster" {{ old('incident_type', $incident->incident_type) == 'natural_disaster' ? 'selected' : '' }}>🌊 Natural Disaster</option>
                                <option value="criminal_activity" {{ old('incident_type', $incident->incident_type) == 'criminal_activity' ? 'selected' : '' }}>🛡️ Criminal Activity</option>
                                <option value="other" {{ old('incident_type', $incident->incident_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('incident_type')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <!-- Severity Level -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Severity <span class="text-error">*</span></span>
                                    </label>
                                    <select name="severity_level" class="select select-bordered @error('severity_level') select-error @enderror" required>
                                        <option value="low" {{ old('severity_level', $incident->severity_level) == 'low' ? 'selected' : '' }}>🟢 Low</option>
                                        <option value="medium" {{ old('severity_level', $incident->severity_level) == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                                        <option value="high" {{ old('severity_level', $incident->severity_level) == 'high' ? 'selected' : '' }}>🟠 High</option>
                                        <option value="critical" {{ old('severity_level', $incident->severity_level) == 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                                    </select>
                                    @error('severity_level')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Status <span class="text-error">*</span></span>
                                    </label>
                                    <section class="block">
                                        <select name="status" class="select select-bordered @error('status') select-error @enderror" required>
                                            <option value="pending" {{ old('status', $incident->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="active" {{ old('status', $incident->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="resolved" {{ old('status', $incident->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ old('status', $incident->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </section>
                                    @error('status')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <!-- Incident Date -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Date & Time <span class="text-error">*</span></span>
                                    </label>
                                    <input type="datetime-local" name="incident_date"
                                           class="input input-bordered @error('incident_date') input-error @enderror"
                                           value="{{ old('incident_date', $incident->incident_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('incident_date')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-control mt-4">
                                <label class="label">
                                    <span class="label-text font-semibold">Description <span class="text-error">*</span></span>
                                </label>
                                <section class="block">
                                    <textarea name="description" rows="3"
                                    class="textarea textarea-bordered @error('description') textarea-error @enderror"
                                    placeholder="Detailed description of the incident..." required>{{ old('description', $incident->description) }}
                          </textarea>
                                </section>
                                @error('description')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-xl border-b pb-3 mb-4">
                                <i class="fas fa-map-marker-alt text-error"></i>
                                Location Details
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Municipality -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Municipality <span class="text-error">*</span></span>
                                    </label>
                                    <select name="municipality" class="select select-bordered @error('municipality') select-error @enderror" required>
                                        <option value="">Select municipality</option>
                                        <option value="Valencia City" {{ old('municipality', $incident->municipality) == 'Valencia City' ? 'selected' : '' }}>Valencia City</option>
                                        <option value="Malaybalay City" {{ old('municipality', $incident->municipality) == 'Malaybalay City' ? 'selected' : '' }}>Malaybalay City</option>
                                        <option value="Don Carlos" {{ old('municipality', $incident->municipality) == 'Don Carlos' ? 'selected' : '' }}>Don Carlos</option>
                                        <option value="Quezon" {{ old('municipality', $incident->municipality) == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                                        <option value="Manolo Fortich" {{ old('municipality', $incident->municipality) == 'Manolo Fortich' ? 'selected' : '' }}>Manolo Fortich</option>
                                    </select>
                                    @error('municipality')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <!-- Barangay -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Barangay</span>
                                    </label>
                                    <input type="text" name="barangay"
                                           class="input input-bordered @error('barangay') input-error @enderror"
                                           placeholder="Enter barangay"
                                           value="{{ old('barangay', $incident->barangay) }}">
                                    @error('barangay')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                                <!-- GPS Coordinates -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Latitude</span>
                                    </label>
                                    <section class="block">
                                        <input type="number" step="any" name="latitude"
                                        class="input input-bordered @error('latitude') input-error @enderror"
                                        placeholder="8.1234567" value="{{ old('latitude', $incident->latitude) }}">
                                    </section>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Longitude</span>
                                    </label>
                                    <input type="number" step="any" name="longitude"
                                           class="input input-bordered @error('longitude') input-error @enderror"
                                           placeholder="125.1234567" value="{{ old('longitude', $incident->longitude) }}">
                                </div>
                            </div>

                                <!-- Location Address -->
                                <div class="form-control md:col-span-2">
                                    <label class="label">
                                        <span class="label-text font-semibold">Specific Location <span class="text-error">*</span></span>
                                    </label>
                                   <section class="block">
                                     <textarea name="location" rows="2"
                                              class="textarea textarea-bordered @error('location') textarea-error @enderror"
                                              placeholder="Street, landmark, or detailed location..." required>{{ old('location', $incident->location) }}
                                    </textarea>
                                   </section>
                                    @error('location')
                                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>

                            <button type="button" class="btn btn-sm btn-outline mt-3" onclick="getLocation()">
                                <i class="fas fa-crosshairs mr-2"></i>Capture GPS
                            </button>
                        </div>
                    </div>

                    <!-- Incident Type Specific Fields -->
                    @php
                        $selectedType = old('incident_type', $incident->incident_type);
                    @endphp

                    @if($selectedType === 'traffic_accident')
                        <div class="card bg-base-100 shadow-xl" id="type-specific-card">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-car-crash text-error"></i>
                                    Traffic Accident Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Vehicles Involved</span>
                                        </label>
                                        <input type="number" name="vehicle_count" min="1" max="20"
                                               class="input input-bordered"
                                               placeholder="e.g., 2"
                                               value="{{ old('vehicle_count', $incident->vehicle_count) }}">
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">License Plates</span>
                                        </label>
                                        <input type="text" name="license_plates_input"
                                               class="input input-bordered"
                                               placeholder="ABC-123, XYZ-456"
                                               value="{{ old('license_plates_input', is_array($incident->license_plates) ? implode(', ', $incident->license_plates) : '') }}">
                                        <label class="label">
                                            <span class="label-text-alt">Separate with commas</span>
                                        </label>
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Driver Information</span>
                                        </label>
                                        <textarea name="driver_information" rows="2"
                                                  class="textarea textarea-bordered"
                                                  placeholder="Names, contact info, license numbers...">{{ old('driver_information', $incident->driver_information) }}</textarea>
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Vehicle Details</span>
                                        </label>
                                        <textarea name="vehicle_details" rows="2"
                                                  class="textarea textarea-bordered"
                                                  placeholder="Make, model, color, damage extent...">{{ old('vehicle_details', $incident->vehicle_details) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedType === 'medical_emergency')
                        <div class="card bg-base-100 shadow-xl" id="type-specific-card">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-ambulance text-error"></i>
                                    Medical Emergency Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Emergency Type</span>
                                        </label>
                                        <select name="medical_emergency_type" class="select select-bordered">
                                            <option value="">Select type</option>
                                            <option value="heart_attack" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'heart_attack' ? 'selected' : '' }}>Heart Attack</option>
                                            <option value="stroke" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'stroke' ? 'selected' : '' }}>Stroke</option>
                                            <option value="trauma" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'trauma' ? 'selected' : '' }}>Trauma</option>
                                            <option value="respiratory" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'respiratory' ? 'selected' : '' }}>Respiratory</option>
                                            <option value="allergic_reaction" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'allergic_reaction' ? 'selected' : '' }}>Allergic Reaction</option>
                                            <option value="seizure" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'seizure' ? 'selected' : '' }}>Seizure</option>
                                            <option value="poisoning" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'poisoning' ? 'selected' : '' }}>Poisoning</option>
                                            <option value="other" {{ old('medical_emergency_type', $incident->medical_emergency_type) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Patient Count</span>
                                        </label>
                                        <input type="number" name="patient_count" min="1" max="50"
                                               class="input input-bordered"
                                               placeholder="1"
                                               value="{{ old('patient_count', $incident->patient_count) }}">
                                    </div>

                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start gap-3">
                                            <input type="checkbox" name="ambulance_requested" value="1"
                                                   class="checkbox checkbox-error"
                                                   {{ old('ambulance_requested', $incident->ambulance_requested) ? 'checked' : '' }}>
                                            <span class="label-text font-semibold">Ambulance Requested</span>
                                        </label>
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Patient Symptoms</span>
                                        </label>
                                        <textarea name="patient_symptoms" rows="2"
                                                  class="textarea textarea-bordered"
                                                  placeholder="Describe symptoms, vital signs, consciousness level...">{{ old('patient_symptoms', $incident->patient_symptoms) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedType === 'fire_incident')
                        <div class="card bg-base-100 shadow-xl" id="type-specific-card">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-fire text-orange-500"></i>
                                    Fire Incident Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Building Type</span>
                                        </label>
                                        <select name="building_type" class="select select-bordered">
                                            <option value="">Select type</option>
                                            <option value="residential" {{ old('building_type', $incident->building_type) == 'residential' ? 'selected' : '' }}>Residential</option>
                                            <option value="commercial" {{ old('building_type', $incident->building_type) == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                            <option value="industrial" {{ old('building_type', $incident->building_type) == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                            <option value="government" {{ old('building_type', $incident->building_type) == 'government' ? 'selected' : '' }}>Government</option>
                                            <option value="agricultural" {{ old('building_type', $incident->building_type) == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                                            <option value="other" {{ old('building_type', $incident->building_type) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Fire Spread Level</span>
                                        </label>
                                        <select name="fire_spread_level" class="select select-bordered">
                                            <option value="">Select level</option>
                                            <option value="contained" {{ old('fire_spread_level', $incident->fire_spread_level) == 'contained' ? 'selected' : '' }}>Contained</option>
                                            <option value="spreading" {{ old('fire_spread_level', $incident->fire_spread_level) == 'spreading' ? 'selected' : '' }}>Spreading</option>
                                            <option value="widespread" {{ old('fire_spread_level', $incident->fire_spread_level) == 'widespread' ? 'selected' : '' }}>Widespread</option>
                                            <option value="controlled" {{ old('fire_spread_level', $incident->fire_spread_level) == 'controlled' ? 'selected' : '' }}>Controlled</option>
                                            <option value="extinguished" {{ old('fire_spread_level', $incident->fire_spread_level) == 'extinguished' ? 'selected' : '' }}>Extinguished</option>
                                        </select>
                                    </div>

                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start gap-3">
                                            <input type="checkbox" name="evacuation_required" value="1"
                                                   class="checkbox checkbox-warning"
                                                   {{ old('evacuation_required', $incident->evacuation_required) ? 'checked' : '' }}>
                                            <span class="label-text font-semibold">Evacuation Required</span>
                                        </label>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">People Evacuated</span>
                                        </label>
                                        <input type="number" name="evacuated_count" min="0"
                                               class="input input-bordered"
                                               placeholder="0"
                                               value="{{ old('evacuated_count', $incident->evacuated_count) }}">
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Buildings Affected</span>
                                        </label>
                                        <input type="number" name="buildings_affected" min="1"
                                               class="input input-bordered"
                                               placeholder="1"
                                               value="{{ old('buildings_affected', $incident->buildings_affected) }}">
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Suspected Fire Cause</span>
                                        </label>
                                        <textarea name="fire_cause" rows="2"
                                                  class="textarea textarea-bordered"
                                                  placeholder="Describe suspected cause...">{{ old('fire_cause', $incident->fire_cause) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedType === 'natural_disaster')
                        <div class="card bg-base-100 shadow-xl" id="type-specific-card">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-cloud-showers-heavy text-info"></i>
                                    Natural Disaster Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Disaster Type</span>
                                        </label>
                                        <select name="disaster_type" class="select select-bordered">
                                            <option value="">Select type</option>
                                            <option value="flood" {{ old('disaster_type', $incident->disaster_type) == 'flood' ? 'selected' : '' }}>Flood</option>
                                            <option value="earthquake" {{ old('disaster_type', $incident->disaster_type) == 'earthquake' ? 'selected' : '' }}>Earthquake</option>
                                            <option value="landslide" {{ old('disaster_type', $incident->disaster_type) == 'landslide' ? 'selected' : '' }}>Landslide</option>
                                            <option value="typhoon" {{ old('disaster_type', $incident->disaster_type) == 'typhoon' ? 'selected' : '' }}>Typhoon</option>
                                            <option value="drought" {{ old('disaster_type', $incident->disaster_type) == 'drought' ? 'selected' : '' }}>Drought</option>
                                            <option value="volcanic" {{ old('disaster_type', $incident->disaster_type) == 'volcanic' ? 'selected' : '' }}>Volcanic</option>
                                            <option value="tsunami" {{ old('disaster_type', $incident->disaster_type) == 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                                            <option value="other" {{ old('disaster_type', $incident->disaster_type) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Affected Area (km²)</span>
                                        </label>
                                        <input type="number" step="0.01" name="affected_area_size" min="0"
                                               class="input input-bordered"
                                               placeholder="5.5"
                                               value="{{ old('affected_area_size', $incident->affected_area_size) }}">
                                    </div>

                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start gap-3">
                                            <input type="checkbox" name="shelter_needed" value="1"
                                                   class="checkbox checkbox-info"
                                                   {{ old('shelter_needed', $incident->shelter_needed) ? 'checked' : '' }}>
                                            <span class="label-text font-semibold">Shelter Required</span>
                                        </label>
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Families Affected</span>
                                        </label>
                                        <input type="number" name="families_affected" min="0"
                                               class="input input-bordered"
                                               placeholder="0"
                                               value="{{ old('families_affected', $incident->families_affected) }}">
                                    </div>

                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Structures Damaged</span>
                                        </label>
                                        <input type="number" name="structures_damaged" min="0"
                                               class="input input-bordered"
                                               placeholder="0"
                                               value="{{ old('structures_damaged', $incident->structures_damaged) }}">
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Infrastructure Damage</span>
                                        </label>
                                       <section class="block">
                                        <textarea name="infrastructure_damage" rows="2"
                                            class="textarea textarea-bordered"
                                            placeholder="Roads, bridges, utilities affected...">{{ old('infrastructure_damage', $incident->infrastructure_damage) }}
                                        </textarea>
                                       </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($selectedType === 'criminal_activity')
                        <div class="card bg-base-100 shadow-xl" id="type-specific-card">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-shield-alt text-error"></i>
                                    Criminal Activity Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold">Crime Type</span>
                                        </label>
                                        <select name="crime_type" class="select select-bordered">
                                            <option value="">Select type</option>
                                            <option value="assault" {{ old('crime_type', $incident->crime_type) == 'assault' ? 'selected' : '' }}>Assault</option>
                                            <option value="theft" {{ old('crime_type', $incident->crime_type) == 'theft' ? 'selected' : '' }}>Theft/Robbery</option>
                                            <option value="vandalism" {{ old('crime_type', $incident->crime_type) == 'vandalism' ? 'selected' : '' }}>Vandalism</option>
                                            <option value="domestic_violence" {{ old('crime_type', $incident->crime_type) == 'domestic_violence' ? 'selected' : '' }}>Domestic Violence</option>
                                            <option value="other" {{ old('crime_type', $incident->crime_type) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start gap-3">
                                            <input type="checkbox" name="police_notified" value="1"
                                                   class="checkbox checkbox-error"
                                                   {{ old('police_notified', $incident->police_notified) ? 'checked' : '' }}>
                                            <span class="label-text font-semibold">Police Notified</span>
                                        </label>
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Police Case Number</span>
                                        </label>
                                        <input type="text" name="case_number"
                                               class="input input-bordered"
                                               placeholder="e.g., 2025-001234"
                                               value="{{ old('case_number', $incident->case_number) }}">
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label">
                                            <span class="label-text font-semibold">Suspect Description</span>
                                        </label>
                                        <textarea name="suspect_description" rows="2"
                                                  class="textarea textarea-bordered"
                                                  placeholder="Appearance, clothing, direction fled...">{{ old('suspect_description', $incident->suspect_description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Additional Details (Collapsible) -->
                    <div class="collapse collapse-arrow bg-base-100 shadow-xl">
                        <input type="checkbox" />
                        <div class="collapse-title text-xl font-semibold">
                            <i class="fas fa-plus-circle text-info mr-2"></i>
                            Additional Details (Optional)
                        </div>
                        <div class="collapse-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                                <!-- Weather & Road Conditions -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Weather Condition</span>
                                    </label>
                                    <select name="weather_condition" class="select select-bordered select-sm">
                                        <option value="">Not specified</option>
                                        <option value="clear" {{ old('weather_condition', $incident->weather_condition) == 'clear' ? 'selected' : '' }}>☀️ Clear</option>
                                        <option value="cloudy" {{ old('weather_condition', $incident->weather_condition) == 'cloudy' ? 'selected' : '' }}>☁️ Cloudy</option>
                                        <option value="rainy" {{ old('weather_condition', $incident->weather_condition) == 'rainy' ? 'selected' : '' }}>🌧️ Rainy</option>
                                        <option value="stormy" {{ old('weather_condition', $incident->weather_condition) == 'stormy' ? 'selected' : '' }}>⛈️ Stormy</option>
                                        <option value="foggy" {{ old('weather_condition', $incident->weather_condition) == 'foggy' ? 'selected' : '' }}>🌫️ Foggy</option>
                                    </select>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Road Condition</span>
                                    </label>
                                    <select name="road_condition" class="select select-bordered select-sm">
                                        <option value="">Not specified</option>
                                        <option value="dry" {{ old('road_condition', $incident->road_condition) == 'dry' ? 'selected' : '' }}>Dry</option>
                                        <option value="wet" {{ old('road_condition', $incident->road_condition) == 'wet' ? 'selected' : '' }}>Wet</option>
                                        <option value="slippery" {{ old('road_condition', $incident->road_condition) == 'slippery' ? 'selected' : '' }}>Slippery</option>
                                        <option value="damaged" {{ old('road_condition', $incident->road_condition) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                        <option value="under_construction" {{ old('road_condition', $incident->road_condition) == 'under_construction' ? 'selected' : '' }}>Under Construction</option>
                                    </select>
                                </div>

                                <!-- Property Damage -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Property Damage (₱)</span>
                                    </label>
                                    <input type="number" step="0.01" min="0" name="property_damage_estimate"
                                           class="input input-bordered input-sm"
                                           placeholder="0.00"
                                           value="{{ old('property_damage_estimate', $incident->property_damage_estimate) }}">
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Damage Description</span>
                                    </label>
                                    <input type="text" name="damage_description"
                                           class="input input-bordered input-sm"
                                           placeholder="Brief description"
                                           value="{{ old('damage_description', $incident->damage_description) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload (Collapsible) -->
                    <div class="collapse collapse-arrow bg-base-100 shadow-xl">
                        <input type="checkbox" />
                        <div class="collapse-title text-xl font-semibold">
                            <i class="fas fa-camera text-success mr-2"></i>
                            Add Photos/Videos (Optional)
                        </div>
                        <div class="collapse-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Photos</span>
                                        <span class="label-text-alt">Max 5 photos, 2MB each</span>
                                    </label>
                                    <input type="file" name="photos[]" multiple accept="image/*"
                                           class="file-input file-input-bordered file-input-sm">
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Videos</span>
                                        <span class="label-text-alt">Max 2 videos, 10MB each</span>
                                    </label>
                                    <input type="file" name="videos[]" multiple accept="video/*"
                                           class="file-input file-input-bordered file-input-sm">
                                </div>
                            </div>

                            @if(($incident->photos && count($incident->photos) > 0) || ($incident->videos && count($incident->videos) > 0))
                                <div class="divider">Existing Media</div>
                                <div class="flex flex-wrap gap-2">
                                    @if($incident->photos && count($incident->photos) > 0)
                                        <div class="badge badge-success gap-2">
                                            <i class="fas fa-images"></i>
                                            {{ count($incident->photos) }} Photo(s)
                                        </div>
                                    @endif
                                    @if($incident->videos && count($incident->videos) > 0)
                                        <div class="badge badge-info gap-2">
                                            <i class="fas fa-video"></i>
                                            {{ count($incident->videos) }} Video(s)
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Resolution Notes (if resolved/closed) -->
                    @if(in_array($incident->status, ['resolved', 'closed']))
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title text-xl border-b pb-3 mb-4">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Resolution Notes
                                </h2>

                                <div class="form-control">
                                    <textarea name="resolution_notes" rows="3"
                                              class="textarea textarea-bordered"
                                              placeholder="How was this incident resolved?">{{ old('resolution_notes', $incident->resolution_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Actions (Sticky Bottom) -->
                <div class="sticky bottom-4 mt-6 bg-base-100 p-4 rounded-lg shadow-2xl border-2 border-base-300">
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-ghost btn-sm sm:btn-md">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm sm:btn-md">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar (1 column) -->
        <div class="lg:col-span-1">
            <div class="sticky top-4 space-y-4">
                <!-- Info Card -->
                <div class="card bg-info text-info-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">
                            <i class="fas fa-info-circle"></i>
                            Editing Tips
                        </h3>
                        <ul class="text-sm space-y-2">
                            <li>• Only modify fields that need updating</li>
                            <li>• Changing incident type will require type-specific details</li>
                            <li>• GPS coordinates can be auto-captured</li>
                            <li>• Add photos/videos as supporting evidence</li>
                        </ul>
                    </div>
                </div>

                <!-- Assignment Section (Admin/Staff Only) -->
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-lg">
                                <i class="fas fa-users text-primary"></i>
                                Assignments
                            </h3>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Assign Staff</span>
                                </label>
                                <select name="assigned_staff_id" class="select select-bordered select-sm" form="incident-form">
                                    <option value="">Unassigned</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->id }}" {{ $incident->assigned_staff_id == $member->id ? 'selected' : '' }}>
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Assign Vehicle</span>
                                </label>
                                <select name="assigned_vehicle_id" class="select select-bordered select-sm" form="incident-form">
                                    <option value="">No vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ $incident->assigned_vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->vehicle_number }} - {{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">
                            <i class="fas fa-chart-line text-warning"></i>
                            Current Stats
                        </h3>

                        <div class="stats stats-vertical shadow">
                            <div class="stat py-3">
                                <div class="stat-title text-xs">Victims</div>
                                <div class="stat-value text-2xl">{{ $incident->victims->count() }}</div>
                            </div>

                            @if($incident->casualty_count > 0)
                                <div class="stat py-3">
                                    <div class="stat-title text-xs">Casualties</div>
                                    <div class="stat-value text-2xl text-error">{{ $incident->casualty_count }}</div>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('victims.create', ['incident_id' => $incident->id]) }}" class="btn btn-sm btn-outline mt-3">
                            <i class="fas fa-plus mr-2"></i>Add Victim
                        </a>
                    </div>
                </div>

                <!-- Audit Info -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-4">
                        <h3 class="font-semibold text-sm mb-2">
                            <i class="fas fa-clock text-neutral"></i>
                            Timeline
                        </h3>
                        <div class="text-xs space-y-1">
                            <p><strong>Created:</strong> {{ $incident->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Updated:</strong> {{ $incident->updated_at->format('M d, Y H:i') }}</p>
                            @if($incident->resolved_at)
                                <p><strong>Resolved:</strong> {{ $incident->resolved_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude.toFixed(7);
            document.querySelector('input[name="longitude"]').value = position.coords.longitude.toFixed(7);

            // Show success toast
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-end z-50';
            toast.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>GPS coordinates captured!</span>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => document.body.removeChild(toast), 3000);
        }, function(error) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-end z-50';
            toast.innerHTML = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Failed to get location</span>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => document.body.removeChild(toast), 3000);
        });
    } else {
        // Show error message
        const toast = document.createElement('div');
        toast.className = 'toast toast-top toast-end';
        toast.innerHTML = `
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Geolocation is not supported by this browser.</span>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }
}
</script>
@endsection
