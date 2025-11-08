{{-- Enhanced Victims List with Medical Details --}}
@if($incident->victims->count() > 0)
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-user-injured text-red-500"></i>
                Victims & Patients ({{ $incident->victims->count() }})
            </h2>

            {{-- Summary Stats --}}
            <div class="stats stats-vertical sm:stats-horizontal shadow mb-4 w-full">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="stat-title">Total Victims</div>
                    <div class="stat-value text-primary">{{ $incident->victims->count() }}</div>
                </div>

                @if($incident->injury_count > 0)
                    <div class="stat">
                        <div class="stat-figure text-warning">
                            <i class="fas fa-band-aid text-2xl"></i>
                        </div>
                        <div class="stat-title">Injuries</div>
                        <div class="stat-value text-warning">{{ $incident->injury_count }}</div>
                    </div>
                @endif

                @if($incident->fatality_count > 0)
                    <div class="stat">
                        <div class="stat-figure text-error">
                            <i class="fas fa-cross text-2xl"></i>
                        </div>
                        <div class="stat-title">Fatalities</div>
                        <div class="stat-value text-error">{{ $incident->fatality_count }}</div>
                    </div>
                @endif
            </div>

            {{-- Victims List --}}
            <div class="space-y-4">
                @foreach($incident->victims as $index => $victim)
                    <div class="border rounded-lg p-4 {{
                        $victim->medical_status === 'critical' ? 'border-red-300 bg-red-50' :
                        ($victim->medical_status === 'deceased' ? 'border-gray-400 bg-gray-100' : 'bg-white')
                    }}">
                        <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                            {{-- Basic Info --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-bold text-lg">{{ $victim->full_name }}</h3>
                                    <span class="badge {{
                                        $victim->medical_status === 'critical' ? 'badge-error' :
                                        ($victim->medical_status === 'stable' ? 'badge-success' :
                                        ($victim->medical_status === 'deceased' ? 'badge-neutral' : 'badge-warning'))
                                    }}">
                                        {{ ucfirst($victim->medical_status) }}
                                    </span>

                                    @if($victim->is_pregnant)
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-baby mr-1"></i>Pregnant
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Age:</span>
                                        <span class="font-semibold">{{ $victim->age }} years</span>
                                        @if($victim->age_category)
                                            <span class="badge badge-xs badge-ghost ml-1">{{ ucfirst($victim->age_category) }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <span class="text-gray-600">Gender:</span>
                                        <span class="font-semibold">{{ ucfirst($victim->gender) }}</span>
                                    </div>

                                    @if($victim->blood_type)
                                        <div>
                                            <span class="text-gray-600">Blood Type:</span>
                                            <span class="font-semibold text-red-600">{{ $victim->blood_type }}</span>
                                        </div>
                                    @endif

                                    @if($victim->contact_number)
                                        <div>
                                            <span class="text-gray-600">Contact:</span>
                                            <span class="font-semibold">{{ $victim->contact_number }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Medical Status Indicator --}}
                            <div class="text-center lg:text-right">
                                @if($victim->medical_status === 'critical')
                                    <div class="text-error font-bold text-3xl">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="text-xs text-error font-semibold">CRITICAL</div>
                                @elseif($victim->medical_status === 'deceased')
                                    <div class="text-gray-600 font-bold text-3xl">
                                        <i class="fas fa-cross"></i>
                                    </div>
                                    <div class="text-xs text-gray-600 font-semibold">DECEASED</div>
                                @endif
                            </div>
                        </div>

                        {{-- Pregnancy Information --}}
                        @if($victim->is_pregnant)
                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded">
                                <h4 class="font-semibold text-purple-800 mb-2">
                                    <i class="fas fa-baby mr-1"></i>Pregnancy Information
                                </h4>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                                    @if($victim->pregnancy_trimester)
                                        <div>
                                            <span class="text-gray-600">Trimester:</span>
                                            <span class="font-semibold">{{ ucfirst($victim->pregnancy_trimester) }}</span>
                                        </div>
                                    @endif

                                    @if($victim->expected_delivery_date)
                                        <div>
                                            <span class="text-gray-600">Expected Delivery:</span>
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($victim->expected_delivery_date)->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($victim->pregnancy_complications)
                                    <div class="mt-2">
                                        <span class="text-gray-600 text-sm">Complications:</span>
                                        <div class="text-sm text-red-600 font-semibold">{{ $victim->pregnancy_complications }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Vital Signs (for medical emergencies) --}}
                        @if($incident->incident_type === 'medical_emergency' &&
                            ($victim->blood_pressure || $victim->heart_rate || $victim->temperature || $victim->respiratory_rate))
                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                <h4 class="font-semibold text-blue-800 mb-2">
                                    <i class="fas fa-heartbeat mr-1"></i>Vital Signs
                                </h4>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                                    @if($victim->blood_pressure)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-tint text-red-500"></i>
                                            <div>
                                                <div class="text-xs text-gray-600">Blood Pressure</div>
                                                <div class="font-bold">{{ $victim->blood_pressure }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($victim->heart_rate)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-heartbeat text-pink-500"></i>
                                            <div>
                                                <div class="text-xs text-gray-600">Heart Rate</div>
                                                <div class="font-bold">{{ $victim->heart_rate }} bpm</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($victim->temperature)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-thermometer-half text-orange-500"></i>
                                            <div>
                                                <div class="text-xs text-gray-600">Temperature</div>
                                                <div class="font-bold">{{ $victim->temperature }}°C</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($victim->respiratory_rate)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-lungs text-blue-500"></i>
                                            <div>
                                                <div class="text-xs text-gray-600">Respiratory Rate</div>
                                                <div class="font-bold">{{ $victim->respiratory_rate }}/min</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($victim->consciousness_level)
                                    <div class="mt-2">
                                        <span class="text-gray-600 text-sm">Consciousness Level:</span>
                                        <span class="badge {{
                                            $victim->consciousness_level === 'alert' ? 'badge-success' :
                                            ($victim->consciousness_level === 'unresponsive' ? 'badge-error' : 'badge-warning')
                                        }} ml-2">
                                            {{ ucfirst($victim->consciousness_level) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Injury Information --}}
                        @if($victim->injury_type || $victim->injury_description)
                            <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded">
                                <h4 class="font-semibold text-orange-800 mb-2">
                                    <i class="fas fa-band-aid mr-1"></i>Injury Details
                                </h4>
                                @if($victim->injury_type)
                                    <div class="mb-2">
                                        <span class="badge badge-warning">{{ $victim->injury_type }}</span>
                                    </div>
                                @endif
                                @if($victim->injury_description)
                                    <div class="text-sm">{{ $victim->injury_description }}</div>
                                @endif
                            </div>
                        @endif

                        {{-- Medical History --}}
                        @if($victim->known_allergies || $victim->existing_medical_conditions || $victim->current_medications)
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <h4 class="font-semibold text-yellow-800 mb-2">
                                    <i class="fas fa-file-medical mr-1"></i>Medical History
                                </h4>
                                <div class="space-y-2 text-sm">
                                    @if($victim->known_allergies)
                                        <div>
                                            <span class="font-semibold text-red-600">Allergies:</span>
                                            <span>{{ $victim->known_allergies }}</span>
                                        </div>
                                    @endif

                                    @if($victim->existing_medical_conditions)
                                        <div>
                                            <span class="font-semibold text-orange-600">Conditions:</span>
                                            <span>{{ $victim->existing_medical_conditions }}</span>
                                        </div>
                                    @endif

                                    @if($victim->current_medications)
                                        <div>
                                            <span class="font-semibold text-blue-600">Medications:</span>
                                            <span>{{ $victim->current_medications }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Special Care Requirements --}}
                        @if($victim->requires_special_care && $victim->special_care_notes)
                            <div class="mt-3 p-3 bg-indigo-50 border border-indigo-200 rounded">
                                <h4 class="font-semibold text-indigo-800 mb-2">
                                    <i class="fas fa-hospital-user mr-1"></i>Special Care Requirements
                                </h4>
                                <div class="text-sm">{{ $victim->special_care_notes }}</div>
                            </div>
                        @endif

                        {{-- Hospital Transfer Info --}}
                        @if($victim->hospital_transferred_to || $victim->ambulance_arrival_time)
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded">
                                <h4 class="font-semibold text-green-800 mb-2">
                                    <i class="fas fa-ambulance mr-1"></i>Transfer Information
                                </h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    @if($victim->hospital_transferred_to)
                                        <div>
                                            <span class="text-gray-600">Hospital:</span>
                                            <span class="font-semibold">{{ $victim->hospital_transferred_to }}</span>
                                        </div>
                                    @endif

                                    @if($victim->ambulance_arrival_time)
                                        <div>
                                            <span class="text-gray-600">Ambulance Arrival:</span>
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($victim->ambulance_arrival_time)->format('M d, Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Add Victim Button --}}
            @if(Auth::user()->role === 'admin' || Auth::user()->municipality === $incident->municipality)
                <div class="mt-4">
                    <a href="{{ route('victims.create', ['incident_id' => $incident->id]) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-plus mr-2"></i>Add Victim/Patient
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

