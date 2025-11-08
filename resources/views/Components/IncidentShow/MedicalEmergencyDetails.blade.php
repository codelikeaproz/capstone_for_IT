{{-- Medical Emergency Specific Details --}}
@if($incident->incident_type === 'medical_emergency')
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-ambulance text-red-500"></i>
                Medical Emergency Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($incident->medical_emergency_type)
                    <div>
                        <label class="label font-semibold">Emergency Type</label>
                        <div class="badge badge-lg badge-error">
                            {{ ucfirst(str_replace('_', ' ', $incident->medical_emergency_type)) }}
                        </div>
                    </div>
                @endif

                @if($incident->patient_count)
                    <div>
                        <label class="label font-semibold">Number of Patients</label>
                        <div class="badge badge-lg badge-warning">{{ $incident->patient_count }} Patient(s)</div>
                    </div>
                @endif

                @if($incident->ambulance_requested)
                    <div>
                        <label class="label font-semibold">Ambulance Status</label>
                        <div class="badge badge-lg badge-success">
                            <i class="fas fa-check-circle mr-1"></i>
                            Ambulance Requested
                        </div>
                    </div>
                @endif

                @if($incident->patient_symptoms)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Patient Symptoms</label>
                        <div class="bg-red-50 p-4 rounded border border-red-200">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->patient_symptoms }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Alert for critical medical emergencies --}}
            @if(in_array($incident->medical_emergency_type, ['heart_attack', 'stroke', 'trauma', 'respiratory']))
                <div class="alert alert-error mt-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span class="font-semibold">Critical Medical Emergency - Immediate Response Required</span>
                </div>
            @endif
        </div>
    </div>
@endif

