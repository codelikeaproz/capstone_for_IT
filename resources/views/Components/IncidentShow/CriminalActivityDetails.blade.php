{{-- Criminal Activity Specific Details --}}
@if($incident->incident_type === 'criminal_activity')
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-shield-alt text-red-500"></i>
                Criminal Activity Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($incident->crime_type)
                    <div>
                        <label class="label font-semibold">Crime Type</label>
                        <div class="badge badge-lg badge-error">{{ ucfirst(str_replace('_', ' ', $incident->crime_type)) }}</div>
                    </div>
                @endif

                @if($incident->police_notified)
                    <div>
                        <label class="label font-semibold">Police Status</label>
                        <div class="badge badge-lg badge-success">
                            <i class="fas fa-check-circle mr-1"></i>
                            Police Notified
                        </div>
                    </div>
                @endif

                @if($incident->case_number)
                    <div>
                        <label class="label font-semibold">Police Case Number</label>
                        <div class="font-mono text-lg text-blue-600 font-semibold">{{ $incident->case_number }}</div>
                    </div>
                @endif

                @if($incident->suspect_description)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Suspect Description</label>
                        <div class="bg-red-50 p-4 rounded border border-red-200">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->suspect_description }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Security Alert --}}
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <span class="font-semibold">Security Notice</span>
                    <div class="text-sm">This information is sensitive. Handle with appropriate confidentiality.</div>
                </div>
            </div>
        </div>
    </div>
@endif

