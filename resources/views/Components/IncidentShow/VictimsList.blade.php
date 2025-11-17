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
                                    @if($victim->birth_date)
                                        <div>
                                            <span class="text-gray-600">Birth Date:</span>
                                            <span class="font-semibold">{{ $victim->formatted_birth_date }}</span>
                                        </div>

                                        <div>
                                            <span class="text-gray-600">Age:</span>
                                            <span class="font-semibold">{{ $victim->age }} years</span>
                                        </div>
                                    @endif

                                    <div>
                                        <span class="text-gray-600">Gender:</span>
                                        <span class="font-semibold">{{ ucfirst($victim->gender) }}</span>
                                    </div>

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

                        {{-- Maternity Information --}}
                        @if($victim->is_pregnant)
                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded">
                                <h4 class="font-semibold text-purple-800 mb-2">
                                    <i class="fas fa-baby mr-1"></i>Maternity Information
                                </h4>
                                @if($victim->labor_stage)
                                    <div class="text-sm">
                                        <span class="text-gray-600">Labor Stage:</span>
                                        <span class="badge badge-secondary ml-2">
                                            {{ ucwords(str_replace('_', ' ', $victim->labor_stage)) }}
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

