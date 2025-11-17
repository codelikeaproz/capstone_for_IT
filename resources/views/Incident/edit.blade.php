@extends("Layouts.app")

@section('title', 'Edit Incident - ' . $incident->incident_number)

@section('content')
<div class="min-h-screen bg-base-200 py-8" role="main">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <header class="mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-edit text-primary" aria-hidden="true"></i>
                        <span>Edit Incident</span>
                    </h1>
                    <p class="mt-2 text-base text-gray-600 leading-relaxed">
                        <span class="font-mono font-semibold text-primary">{{ $incident->incident_number }}</span> •
                        {{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }} •
                        @php
                            $badgeColor = match($incident->severity_level) {
                                'critical' => 'badge-error',
                                'high' => 'badge-warning',
                                'medium' => 'badge-info',
                                default => 'badge-success'
                            };
                        @endphp
                        <span class="badge badge-sm {{ $badgeColor }}">
                            {{ ucfirst($incident->severity_level) }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('incidents.show', $incident) }}"
                   class="btn btn-outline gap-2 w-full sm:w-auto min-h-[44px]"
                   aria-label="Back to incident details">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    <span>Back to Details</span>
                </a>
            </div>
        </header>

        <!-- Validation Errors Display -->
        @include('Components.ValidationErrors')

        <!-- Main Form -->
        <form action="{{ route('incidents.update', $incident) }}"
              method="POST"
              enctype="multipart/form-data"
              id="incident-form"
              class="bg-white rounded-lg shadow-lg p-6 md:p-8 space-y-8"
              aria-label="Edit incident form">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <section aria-labelledby="basic-info-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="basic-info-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-primary" aria-hidden="true"></i>
                        <span>Basic Information</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Core incident details and classification</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Incident Type -->
                        <div class="form-control">
                            <label for="incident_type" class="label">
                                <span class="label-text font-semibold text-gray-700">Incident Type <span class="text-error">*</span></span>
                            </label>
                            <select name="incident_type"
                                    id="incident_type"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('incident_type') select-error @enderror"
                                    required
                                    aria-required="true">
                                <option value="">Select incident type</option>
                                <option value="traffic_accident" {{ old('incident_type', $incident->incident_type) == 'traffic_accident' ? 'selected' : '' }}>Traffic Accident</option>
                                <option value="medical_emergency" {{ old('incident_type', $incident->incident_type) == 'medical_emergency' ? 'selected' : '' }}>Medical Emergency</option>
                                <option value="fire_incident" {{ old('incident_type', $incident->incident_type) == 'fire_incident' ? 'selected' : '' }}>Fire Incident</option>
                                <option value="natural_disaster" {{ old('incident_type', $incident->incident_type) == 'natural_disaster' ? 'selected' : '' }}>Natural Disaster</option>
                                <option value="criminal_activity" {{ old('incident_type', $incident->incident_type) == 'criminal_activity' ? 'selected' : '' }}>Criminal Activity</option>
                                <option value="other" {{ old('incident_type', $incident->incident_type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('incident_type')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Severity Level -->
                        <div class="form-control">
                            <label for="severity_level" class="label">
                                <span class="label-text font-semibold text-gray-700">Severity Level <span class="text-error">*</span></span>
                            </label>
                            <select name="severity_level"
                                    id="severity_level"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('severity_level') select-error @enderror"
                                    required
                                    aria-required="true">
                                <option value="low" {{ old('severity_level', $incident->severity_level) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('severity_level', $incident->severity_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('severity_level', $incident->severity_level) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('severity_level', $incident->severity_level) == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('severity_level')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label for="status" class="label">
                                <span class="label-text font-semibold text-gray-700">Status <span class="text-error">*</span></span>
                            </label>
                            <select name="status"
                                    id="status"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('status') select-error @enderror"
                                    required
                                    aria-required="true">
                                <option value="pending" {{ old('status', $incident->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $incident->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="resolved" {{ old('status', $incident->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ old('status', $incident->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Incident Date & Time -->
                        <div class="form-control">
                            <label for="incident_date" class="label">
                                <span class="label-text font-semibold text-gray-700">Date & Time <span class="text-error">*</span></span>
                            </label>
                            <input type="datetime-local"
                                   name="incident_date"
                                   id="incident_date"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('incident_date') input-error @enderror"
                                   value="{{ old('incident_date', $incident->incident_date->format('Y-m-d\TH:i')) }}"
                                   required
                                   aria-required="true">
                            @error('incident_date')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-control mt-6">
                        <label for="description" class="label">
                            <span class="label-text font-semibold text-gray-700">Description <span class="text-error">*</span></span>
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('description') textarea-error @enderror"
                                  placeholder="Provide a detailed description of the incident..."
                                  required
                                  aria-required="true">{{ old('description', $incident->description) }}</textarea>
                        @error('description')
                            <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Location Section --}}
            <section aria-labelledby="location-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="location-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-error" aria-hidden="true"></i>
                        <span>Location Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Incident location and coordinates</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Municipality -->
                        <div class="form-control">
                            <label for="municipality-select" class="label">
                                <span class="label-text font-semibold text-gray-700">Municipality <span class="text-error">*</span></span>
                            </label>
                            <select name="municipality"
                                    id="municipality-select"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('municipality') select-error @enderror"
                                    required
                                    aria-required="true">
                                <option value="">Select municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" {{ old('municipality', $incident->municipality) == $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                            @error('municipality')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Barangay -->
                        <div class="form-control">
                            <label for="barangay-select" class="label">
                                <span class="label-text font-semibold text-gray-700">Barangay</span>
                            </label>
                            <select name="barangay"
                                    id="barangay-select"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('barangay') select-error @enderror"
                                    {{ empty($barangays) ? 'disabled' : '' }}>
                                <option value="">{{ empty($barangays) ? 'Select municipality first' : 'Select barangay' }}</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ old('barangay', $incident->barangay) == $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barangay')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Latitude -->
                        <div class="form-control">
                            <label for="latitude" class="label">
                                <span class="label-text font-semibold text-gray-700">Latitude</span>
                            </label>
                            <input type="number"
                                   step="any"
                                   name="latitude"
                                   id="latitude"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('latitude') input-error @enderror"
                                   placeholder="8.1234567"
                                   value="{{ old('latitude', $incident->latitude) }}">
                            @error('latitude')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="form-control">
                            <label for="longitude" class="label">
                                <span class="label-text font-semibold text-gray-700">Longitude</span>
                            </label>
                            <input type="number"
                                   step="any"
                                   name="longitude"
                                   id="longitude"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('longitude') input-error @enderror"
                                   placeholder="125.1234567"
                                   value="{{ old('longitude', $incident->longitude) }}">
                            @error('longitude')
                                <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                            @enderror
                        </div>
                    </div>

                    <!-- Specific Location Address -->
                    <div class="form-control mt-6">
                        <label for="location" class="label">
                            <span class="label-text font-semibold text-gray-700">Specific Location <span class="text-error">*</span></span>
                        </label>
                        <textarea name="location"
                                  id="location"
                                  rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('location') textarea-error @enderror"
                                  placeholder="Street address, landmarks, or detailed location description..."
                                  required
                                  aria-required="true">{{ old('location', $incident->location) }}</textarea>
                        @error('location')
                            <label class="label"><span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- GPS Capture Button -->
                    <button type="button"
                            onclick="getLocation()"
                            class="btn btn-outline btn-sm gap-2 mt-4 min-h-[44px]">
                        <i class="fas fa-crosshairs" aria-hidden="true"></i>
                        <span>Capture GPS Coordinates</span>
                    </button>
                </div>
            </section>

            {{-- Incident Type-Specific Fields (Dynamically shown) --}}
            @php
                $selectedType = old('incident_type', $incident->incident_type);
            @endphp

            {{-- Traffic Accident Fields --}}
            <section id="traffic-accident-section" data-incident-type="traffic_accident"
                     style="display: {{ $selectedType === 'traffic_accident' ? 'block' : 'none' }};"
                     aria-labelledby="traffic-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="traffic-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-car-crash text-warning" aria-hidden="true"></i>
                        <span>Traffic Accident Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Vehicle and driver information</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="vehicle_count" class="label">
                                <span class="label-text font-semibold text-gray-700">Vehicles Involved</span>
                            </label>
                            <input type="number"
                                   name="vehicle_count"
                                   id="vehicle_count"
                                   min="1"
                                   max="20"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px]"
                                   placeholder="e.g., 2"
                                   value="{{ old('vehicle_count', $incident->vehicle_count) }}">
                        </div>

                        <div class="form-control">
                            <label for="license_plates_input" class="label">
                                <span class="label-text font-semibold text-gray-700">License Plates</span>
                            </label>
                            <input type="text"
                                   name="license_plates_input"
                                   id="license_plates_input"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px]"
                                   placeholder="ABC-123, XYZ-456"
                                   value="{{ old('license_plates_input', is_array($incident->license_plates) ? implode(', ', $incident->license_plates) : '') }}">
                            <label class="label">
                                <span class="label-text-alt text-gray-500">Separate multiple plates with commas</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 mt-6">
                        <div class="form-control">
                            <label for="driver_information" class="label">
                                <span class="label-text font-semibold text-gray-700">Driver Information</span>
                            </label>
                            <textarea name="driver_information"
                                      id="driver_information"
                                      rows="3"
                                      class="textarea textarea-bordered w-full focus:outline-primary"
                                      placeholder="Names, contact information, license numbers...">{{ old('driver_information', $incident->driver_information) }}</textarea>
                        </div>

                        <div class="form-control">
                            <label for="vehicle_details" class="label">
                                <span class="label-text font-semibold text-gray-700">Vehicle Details</span>
                            </label>
                            <textarea name="vehicle_details"
                                      id="vehicle_details"
                                      rows="3"
                                      class="textarea textarea-bordered w-full focus:outline-primary"
                                      placeholder="Make, model, color, damage extent...">{{ old('vehicle_details', $incident->vehicle_details) }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Medical Emergency Fields --}}
            <section id="medical-emergency-section" data-incident-type="medical_emergency"
                     style="display: {{ $selectedType === 'medical_emergency' ? 'block' : 'none' }};"
                     aria-labelledby="medical-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="medical-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-ambulance text-error" aria-hidden="true"></i>
                        <span>Medical Emergency Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Patient information and medical needs</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="medical_emergency_type" class="label">
                                <span class="label-text font-semibold text-gray-700">Emergency Type</span>
                            </label>
                            <select name="medical_emergency_type"
                                    id="medical_emergency_type"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
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
                            <label for="patient_count" class="label">
                                <span class="label-text font-semibold text-gray-700">Patient Count</span>
                            </label>
                            <input type="number"
                                   name="patient_count"
                                   id="patient_count"
                                   min="1"
                                   max="50"
                                   class="input input-bordered w-full focus:outline-primary min-h-[44px]"
                                   placeholder="1"
                                   value="{{ old('patient_count', $incident->patient_count) }}">
                        </div>

                        <div class="form-control col-span-2">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox"
                                       name="ambulance_requested"
                                       value="1"
                                       class="checkbox checkbox-error"
                                       {{ old('ambulance_requested', $incident->ambulance_requested) ? 'checked' : '' }}>
                                <span class="label-text font-semibold text-gray-700">Ambulance Requested</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control mt-6">
                        <label for="patient_symptoms" class="label">
                            <span class="label-text font-semibold text-gray-700">Patient Symptoms</span>
                        </label>
                        <textarea name="patient_symptoms"
                                  id="patient_symptoms"
                                  rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary"
                                  placeholder="Describe symptoms, vital signs, consciousness level...">{{ old('patient_symptoms', $incident->patient_symptoms) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- Fire Incident Fields --}}
            <section id="fire-incident-section" data-incident-type="fire_incident"
                     style="display: {{ $selectedType === 'fire_incident' ? 'block' : 'none' }};"
                     aria-labelledby="fire-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="fire-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-fire text-orange-500" aria-hidden="true"></i>
                        <span>Fire Incident Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Fire spread, building, and evacuation information</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="building_type" class="label">
                                <span class="label-text font-semibold text-gray-700">Building Type</span>
                            </label>
                            <select name="building_type"
                                    id="building_type"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
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
                            <label for="fire_spread_level" class="label">
                                <span class="label-text font-semibold text-gray-700">Fire Spread Level</span>
                            </label>
                            <select name="fire_spread_level"
                                    id="fire_spread_level"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                <option value="">Select level</option>
                                <option value="contained" {{ old('fire_spread_level', $incident->fire_spread_level) == 'contained' ? 'selected' : '' }}>Contained</option>
                                <option value="spreading" {{ old('fire_spread_level', $incident->fire_spread_level) == 'spreading' ? 'selected' : '' }}>Spreading</option>
                                <option value="widespread" {{ old('fire_spread_level', $incident->fire_spread_level) == 'widespread' ? 'selected' : '' }}>Widespread</option>
                                <option value="controlled" {{ old('fire_spread_level', $incident->fire_spread_level) == 'controlled' ? 'selected' : '' }}>Controlled</option>
                                <option value="extinguished" {{ old('fire_spread_level', $incident->fire_spread_level) == 'extinguished' ? 'selected' : '' }}>Extinguished</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-control mt-6">
                        <label for="fire_cause" class="label">
                            <span class="label-text font-semibold text-gray-700">Suspected Fire Cause</span>
                        </label>
                        <textarea name="fire_cause"
                                  id="fire_cause"
                                  rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary"
                                  placeholder="Describe suspected cause of the fire...">{{ old('fire_cause', $incident->fire_cause) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- Natural Disaster Fields --}}
            <section id="natural-disaster-section" data-incident-type="natural_disaster"
                     style="display: {{ $selectedType === 'natural_disaster' ? 'block' : 'none' }};"
                     aria-labelledby="disaster-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="disaster-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-cloud-showers-heavy text-info" aria-hidden="true"></i>
                        <span>Natural Disaster Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Disaster type, affected area, and damage information</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="disaster_type" class="label">
                                <span class="label-text font-semibold text-gray-700">Disaster Type</span>
                            </label>
                            <select name="disaster_type"
                                    id="disaster_type"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
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

                        <div class="form-control md:col-span-2">
                            <label for="disaster_description" class="label">
                                <span class="label-text font-semibold text-gray-700">Disaster Description</span>
                                <span class="label-text-alt text-base-content/60">Provide detailed information about the disaster</span>
                            </label>
                            <textarea name="disaster_description"
                                      id="disaster_description"
                                      rows="4"
                                      class="textarea textarea-bordered w-full focus:outline-primary"
                                      placeholder="Describe the disaster impact, affected areas, damage to infrastructure, evacuation status, and any other relevant information...">{{ old('disaster_description', $incident->disaster_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Criminal Activity Fields --}}
            <section id="criminal-activity-section" data-incident-type="criminal_activity"
                     style="display: {{ $selectedType === 'criminal_activity' ? 'block' : 'none' }};"
                     aria-labelledby="criminal-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="criminal-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-error" aria-hidden="true"></i>
                        <span>Criminal Activity Details</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Crime type and police information</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="crime_type" class="label">
                                <span class="label-text font-semibold text-gray-700">Crime Type</span>
                            </label>
                            <select name="crime_type"
                                    id="crime_type"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                <option value="">Select type</option>
                                <option value="assault" {{ old('crime_type', $incident->crime_type) == 'assault' ? 'selected' : '' }}>Assault</option>
                                <option value="theft" {{ old('crime_type', $incident->crime_type) == 'theft' ? 'selected' : '' }}>Theft/Robbery</option>
                                <option value="vandalism" {{ old('crime_type', $incident->crime_type) == 'vandalism' ? 'selected' : '' }}>Vandalism</option>
                                <option value="domestic_violence" {{ old('crime_type', $incident->crime_type) == 'domestic_violence' ? 'selected' : '' }}>Domestic Violence</option>
                                <option value="other" {{ old('crime_type', $incident->crime_type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-control mt-6">
                        <label for="suspect_description" class="label">
                            <span class="label-text font-semibold text-gray-700">Suspect Description</span>
                        </label>
                        <textarea name="suspect_description"
                                  id="suspect_description"
                                  rows="3"
                                  class="textarea textarea-bordered w-full focus:outline-primary"
                                  placeholder="Describe suspect's appearance, clothing, direction fled...">{{ old('suspect_description', $incident->suspect_description) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- Media Upload Section --}}
            <section aria-labelledby="media-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="media-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-camera text-info" aria-hidden="true"></i>
                        <span>Incident Media</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Upload additional photos or videos</p>

                    {{-- Display Existing Photos --}}
                    @if($incident->photos && count($incident->photos) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-images text-info"></i>
                                Existing Photos ({{ count($incident->photos) }})
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach($incident->photos as $index => $photo)
                                    <div class="relative group bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 1/1;">
                                        {{-- Photo Image --}}
                                        <img src="{{ asset('storage/' . $photo) }}"
                                             alt="Incident photo {{ $index + 1 }}"
                                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             style="z-index: 1;"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23fee2e2%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2210%22 fill=%22%23dc2626%22%3EImage Failed%3C/text%3E%3C/svg%3E';">

                                        {{-- Hover Overlay --}}
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all"
                                             style="z-index: 2; background-color: rgba(0, 0, 0, 0);"
                                             onmouseenter="this.style.backgroundColor='rgba(0, 0, 0, 0.4)'"
                                             onmouseleave="this.style.backgroundColor='rgba(0, 0, 0, 0)'">
                                            <a href="{{ asset('storage/' . $photo) }}"
                                               target="_blank"
                                               class="opacity-0 group-hover:opacity-100 transition-opacity btn btn-sm btn-circle btn-info pointer-events-auto"
                                               onclick="event.stopPropagation();"
                                               title="View full size">
                                                <i class="fas fa-search-plus"></i>
                                            </a>
                                        </div>

                                        {{-- Photo Number Badge --}}
                                        <div class="absolute top-2 left-2 badge badge-sm badge-neutral" style="z-index: 10;">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Display Existing Videos --}}
                    @if($incident->videos && count($incident->videos) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-video text-secondary"></i>
                                Existing Videos ({{ count($incident->videos) }})
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($incident->videos as $index => $video)
                                    <div class="relative">
                                        <video controls
                                               class="w-full h-48 rounded-lg shadow"
                                               style="background: #000;">
                                            <source src="{{ asset('storage/' . $video) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Upload New Media --}}
                    <div class="space-y-6">
                        <!-- Photos Upload -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Add New Photos</span>
                                <span class="label-text-alt text-base-content/60">Max 5 photos, 2MB each</span>
                            </label>
                            <input type="file"
                                   name="photos[]"
                                   id="photo-input"
                                   class="file-input file-input-bordered w-full focus:outline-primary @error('photos') file-input-error @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/gif"
                                   multiple
                                   onchange="handlePhotoUpload(this)">
                            <div class="label">
                                <span class="label-text-alt text-base-content/60">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Supported: JPG, PNG, GIF
                                </span>
                                <span id="photo-count-display" class="label-text-alt text-primary font-medium"></span>
                            </div>
                            @error('photos')
                                <label class="label">
                                    <span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                </label>
                            @enderror

                            <!-- Photo Preview Section -->
                            <div id="photo-preview-container" class="mt-4 hidden">
                                <div class="bg-base-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-sm font-semibold text-base-content">New Photos to Upload</h3>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-base-content/60">
                                                <span id="photo-count">0</span>/5 photos
                                            </span>
                                            <button type="button"
                                                    onclick="clearAllPhotos()"
                                                    class="btn btn-ghost btn-xs text-error gap-1">
                                                <i class="fas fa-trash"></i>
                                                <span>Clear All</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="photo-preview-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                        <!-- Previews will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Videos Upload -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold text-gray-700">Add New Videos (Optional)</span>
                                <span class="label-text-alt text-base-content/60">Max 2 videos, 10MB each</span>
                            </label>
                            <input type="file"
                                   name="videos[]"
                                   id="video-input"
                                   class="file-input file-input-bordered w-full focus:outline-primary @error('videos') file-input-error @enderror"
                                   accept="video/mp4,video/webm,video/quicktime"
                                   multiple
                                   onchange="handleVideoUpload(this)">
                            <div class="label">
                                <span class="label-text-alt text-base-content/60">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Supported: MP4, WebM, MOV
                                </span>
                                <span id="video-count-display" class="label-text-alt text-secondary font-medium"></span>
                            </div>
                            @error('videos')
                                <label class="label">
                                    <span class="label-text-alt text-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                </label>
                            @enderror

                            <!-- Video Preview Section -->
                            <div id="video-preview-container" class="mt-4 hidden">
                                <div class="bg-base-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-sm font-semibold text-base-content">New Videos to Upload</h3>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-base-content/60">
                                                <span id="video-count">0</span>/2 videos
                                            </span>
                                            <button type="button"
                                                    onclick="clearAllVideos()"
                                                    class="btn btn-ghost btn-xs text-error gap-1">
                                                <i class="fas fa-trash"></i>
                                                <span>Clear All</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="video-preview-grid" class="space-y-3">
                                        <!-- Video previews will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Assignment Fields (Admin/Staff Only) --}}
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
            <section aria-labelledby="assignment-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="assignment-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-users text-primary" aria-hidden="true"></i>
                        <span>Assignments</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Assign staff and vehicles to this incident</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label for="assigned_staff_id" class="label">
                                <span class="label-text font-semibold text-gray-700">Assigned Staff</span>
                            </label>
                            <select name="assigned_staff_id"
                                    id="assigned_staff_id"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
                                <option value="">Unassigned</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $incident->assigned_staff_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label for="assigned_vehicle_id" class="label">
                                <span class="label-text font-semibold text-gray-700">Assigned Vehicle</span>
                            </label>
                            <select name="assigned_vehicle_id"
                                    id="assigned_vehicle_id"
                                    class="select select-bordered w-full focus:outline-primary min-h-[44px]">
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
            </section>
            @endif

            {{-- Resolution Notes (for resolved/closed incidents) --}}
            @if(in_array($incident->status, ['resolved', 'closed']))
            <section aria-labelledby="resolution-heading">
                <div class="border-b border-base-300 pb-6 mb-8">
                    <h2 id="resolution-heading" class="text-xl font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                        <span>Resolution Notes</span>
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">Document how this incident was resolved</p>

                    <div class="form-control">
                        <label for="resolution_notes" class="label">
                            <span class="label-text font-semibold text-gray-700">Resolution Details</span>
                        </label>
                        <textarea name="resolution_notes"
                                  id="resolution_notes"
                                  rows="4"
                                  class="textarea textarea-bordered w-full focus:outline-primary"
                                  placeholder="Describe how this incident was resolved, actions taken, outcome...">{{ old('resolution_notes', $incident->resolution_notes) }}</textarea>
                    </div>
                </div>
            </section>
            @endif

            <!-- Form Actions -->
            <div class="border-t border-base-300 pt-8">
                <div class="flex flex-col sm:flex-row justify-end items-stretch sm:items-center gap-3">
                    <a href="{{ route('incidents.show', $incident) }}"
                       class="btn btn-outline w-full sm:w-auto gap-2 min-h-[44px]"
                       aria-label="Cancel and return to incident details">
                        <i class="fas fa-times" aria-hidden="true"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit"
                            class="btn btn-primary w-full sm:w-auto gap-2 min-h-[44px]"
                            aria-label="Save changes to incident">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Incident Stats Card (Below Form) -->
        <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-warning" aria-hidden="true"></i>
                <span>Current Incident Stats</span>
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat bg-base-100 rounded-lg p-4 shadow-sm">
                    <div class="stat-title text-xs text-gray-600">Victims</div>
                    <div class="stat-value text-2xl text-primary">{{ $incident->victims->count() }}</div>
                </div>

                @if($incident->casualty_count > 0)
                <div class="stat bg-base-100 rounded-lg p-4 shadow-sm">
                    <div class="stat-title text-xs text-gray-600">Casualties</div>
                    <div class="stat-value text-2xl text-error">{{ $incident->casualty_count }}</div>
                </div>
                @endif

                <div class="stat bg-base-100 rounded-lg p-4 shadow-sm">
                    <div class="stat-title text-xs text-gray-600">Created</div>
                    <div class="stat-value text-sm">{{ $incident->created_at->format('M d, Y') }}</div>
                    <div class="stat-desc text-xs">{{ $incident->created_at->format('H:i') }}</div>
                </div>

                <div class="stat bg-base-100 rounded-lg p-4 shadow-sm">
                    <div class="stat-title text-xs text-gray-600">Last Updated</div>
                    <div class="stat-value text-sm">{{ $incident->updated_at->format('M d, Y') }}</div>
                    <div class="stat-desc text-xs">{{ $incident->updated_at->diffForHumans() }}</div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('victims.create', ['incident_id' => $incident->id]) }}"
                   class="btn btn-outline btn-sm gap-2 min-h-[44px]">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span>Add Victim</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// GPS LOCATION CAPTURE
// ============================================
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitude"]').value = position.coords.latitude.toFixed(8);
            document.querySelector('input[name="longitude"]').value = position.coords.longitude.toFixed(8);
            showSuccessToast('GPS coordinates captured successfully!');
        }, function(error) {
            showErrorToast('Failed to get location: ' + error.message);
        });
    } else {
        showErrorToast('Geolocation is not supported by this browser.');
    }
}

// ============================================
// INCIDENT TYPE HANDLER
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
    }
}

// ============================================
// DYNAMIC BARANGAY LOADING
// ============================================
function loadBarangays(municipality, selectedBarangay = '') {
    const barangaySelect = document.getElementById('barangay-select');

    if (!municipality) {
        barangaySelect.innerHTML = '<option value="">Select municipality first</option>';
        barangaySelect.disabled = true;
        return;
    }

    // Show loading state
    barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
    barangaySelect.disabled = true;

    // Fetch barangays from API
    fetch(`{{ route('api.barangays') }}?municipality=${encodeURIComponent(municipality)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.barangays) {
                barangaySelect.innerHTML = '<option value="">Select barangay</option>';

                data.barangays.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;

                    // Pre-select if it matches
                    if (selectedBarangay && barangay === selectedBarangay) {
                        option.selected = true;
                    }

                    barangaySelect.appendChild(option);
                });

                barangaySelect.disabled = false;
            } else {
                barangaySelect.innerHTML = '<option value="">No barangays found</option>';
                barangaySelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading barangays:', error);
            barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
            barangaySelect.disabled = true;
            showErrorToast('Failed to load barangays');
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const incidentTypeSelect = document.getElementById('incident_type');
    const municipalitySelect = document.getElementById('municipality-select');
    const barangaySelect = document.getElementById('barangay-select');

    // Initialize incident type handler
    if (incidentTypeSelect) {
        incidentTypeSelect.addEventListener('change', function() {
            handleIncidentTypeChange(this.value);
        });

        // Initialize on page load if there's a value
        if (incidentTypeSelect.value) {
            handleIncidentTypeChange(incidentTypeSelect.value);
        }
    }

    // Initialize municipality/barangay handler
    if (municipalitySelect && barangaySelect) {
        // Handle municipality change
        municipalitySelect.addEventListener('change', function() {
            const municipality = this.value;
            loadBarangays(municipality);
        });

        // Initialize barangays on page load if municipality is selected
        const initialMunicipality = municipalitySelect.value;
        const initialBarangay = '{{ old('barangay', $incident->barangay) }}';

        if (initialMunicipality) {
            loadBarangays(initialMunicipality, initialBarangay);
        }
    }
});

// ============================================
// MEDIA UPLOAD HANDLERS
// ============================================
const MAX_PHOTOS = 5;
const MAX_VIDEOS = 2;
const MAX_PHOTO_SIZE = 2 * 1024 * 1024; // 2MB
const MAX_VIDEO_SIZE = 10 * 1024 * 1024; // 10MB

let photoFiles = [];
let videoFiles = [];

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

// Render photo preview
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
            console.log(`File ${file.name} loaded`);

            // Create preview card
            const previewCard = document.createElement('div');
            previewCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300 group';

            // Build the HTML structure
            previewCard.innerHTML = `
                <div class="relative bg-base-200" style="padding-top: 100%; position: relative;">
                    <img
                        src="${imageUrl}"
                        alt="${file.name}"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; background: #f3f4f6;"
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
        };

        reader.onerror = function(error) {
            console.error('Error reading file:', file.name, error);
            showErrorToast(`Failed to read ${file.name}`);
        };

        reader.readAsDataURL(file);
    });
}

// Hide photo preview
function hidePhotoPreview() {
    const container = document.getElementById('photo-preview-container');
    const textDisplay = document.getElementById('photo-count-display');
    if (container) container.classList.add('hidden');
    if (textDisplay) textDisplay.textContent = '';
}

// Remove individual photo
function removePhoto(index) {
    photoFiles.splice(index, 1);
    updatePhotoInput();
    renderPhotoPreview();
    showSuccessToast('Photo removed');
}

// Clear all photos
function clearAllPhotos() {
    if (confirm('Remove all selected photos?')) {
        photoFiles = [];
        document.getElementById('photo-input').value = '';
        hidePhotoPreview();
        showSuccessToast('Photos cleared');
    }
}

// Update photo input with current files
function updatePhotoInput() {
    const input = document.getElementById('photo-input');
    const dataTransfer = new DataTransfer();

    photoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

// Handle video upload
function handleVideoUpload(input) {
    const files = Array.from(input.files);

    if (files.length > MAX_VIDEOS) {
        showErrorToast(`Maximum ${MAX_VIDEOS} videos allowed`);
        input.value = '';
        return;
    }

    videoFiles = [];
    let hasErrors = false;

    for (let file of files) {
        if (file.size > MAX_VIDEO_SIZE) {
            showErrorToast(`${file.name} exceeds 10MB limit`);
            hasErrors = true;
            continue;
        }

        const validVideoTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        if (!validVideoTypes.includes(file.type.toLowerCase())) {
            showErrorToast(`${file.name} is not a valid video`);
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

    updateVideoInput();
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

    countDisplay.textContent = videoFiles.length;
    textDisplay.textContent = `${videoFiles.length} video${videoFiles.length > 1 ? 's' : ''} selected`;

    grid.innerHTML = '';
    container.classList.remove('hidden');

    videoFiles.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const videoUrl = e.target.result;
            const previewCard = document.createElement('div');
            previewCard.className = 'relative bg-base-100 rounded-lg overflow-hidden shadow-sm border border-base-300';

            previewCard.innerHTML = `
                <video class="w-full h-32 object-cover bg-black" controls>
                    <source src="${videoUrl}" type="${file.type}">
                </video>
                <div class="p-2 flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content truncate">${file.name}</p>
                        <p class="text-xs text-base-content/60">${formatFileSize(file.size)}</p>
                    </div>
                    <button
                        type="button"
                        onclick="removeVideo(${index})"
                        class="btn btn-circle btn-xs btn-error ml-2"
                        title="Remove video"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            grid.appendChild(previewCard);
        };

        reader.readAsDataURL(file);
    });
}

// Hide video preview
function hideVideoPreview() {
    const container = document.getElementById('video-preview-container');
    const textDisplay = document.getElementById('video-count-display');
    if (container) container.classList.add('hidden');
    if (textDisplay) textDisplay.textContent = '';
}

// Remove individual video
function removeVideo(index) {
    videoFiles.splice(index, 1);
    updateVideoInput();
    renderVideoPreview();
    showSuccessToast('Video removed');
}

// Clear all videos
function clearAllVideos() {
    if (confirm('Remove all selected videos?')) {
        videoFiles = [];
        document.getElementById('video-input').value = '';
        hideVideoPreview();
        showSuccessToast('Videos cleared');
    }
}

// Update video input with current files
function updateVideoInput() {
    const input = document.getElementById('video-input');
    const dataTransfer = new DataTransfer();

    videoFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

// Format file size helper
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// ============================================
// TOAST NOTIFICATIONS
// ============================================
function showSuccessToast(message) {
    console.log('Success:', message);
    alert(message);
}

function showErrorToast(message) {
    console.error('Error:', message);
    alert(message);
}
</script>
@endpush
@endsection
