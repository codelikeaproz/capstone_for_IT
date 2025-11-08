@extends("Layouts.app")

@section('title', 'Request Details - ' . $request->request_number)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-7xl">

        {{-- Breadcrumbs --}}
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('requests.index') }}">Requests</a></li>
                <li class="font-semibold">{{ $request->request_number }}</li>
            </ul>
        </div>

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-mono mb-2">
                        {{ $request->request_number }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="badge {{ $request->status_badge }} badge-lg font-semibold">
                            {{ strtoupper($request->status) }}
                        </span>
                        @php
                            $urgencyBadges = [
                                'low' => 'badge-success',
                                'medium' => 'badge-warning',
                                'high' => 'badge-warning',
                                'critical' => 'badge-error',
                            ];
                        @endphp
                        <span class="badge {{ $urgencyBadges[$request->urgency_level] ?? 'badge-ghost' }} badge-lg">
                            {{ ucfirst($request->urgency_level) }} Priority
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if(in_array($request->status, ['pending', 'processing']))
                        <a href="{{ route('requests.edit', $request) }}" class="btn btn-primary gap-2">
                            <i class="fas fa-edit"></i>
                            <span>Edit Request</span>
                        </a>
                    @endif
                    <a href="{{ route('requests.index') }}" class="btn btn-outline gap-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Requester Information --}}
                <div class="card bg-white shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-primary"></i>
                            Requester Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                <p class="text-base text-gray-900">{{ $request->requester_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Email Address</label>
                                <p class="text-base text-gray-900">
                                    @if($request->email_notifications_enabled)
                                        <i class="fas fa-bell text-success mr-1" title="Email notifications enabled"></i>
                                    @endif
                                    {{ $request->requester_email }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Phone Number</label>
                                <p class="text-base text-gray-900">
                                    @if($request->sms_notifications_enabled)
                                        <i class="fas fa-bell text-success mr-1" title="SMS notifications enabled"></i>
                                    @endif
                                    {{ $request->requester_phone }}
                                </p>
                            </div>
                            @if($request->requester_id_number)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">ID Number</label>
                                <p class="text-base text-gray-900">{{ $request->requester_id_number }}</p>
                            </div>
                            @endif
                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-gray-600">Address</label>
                                <p class="text-base text-gray-900">{{ $request->requester_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Request Details --}}
                <div class="card bg-white shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt text-primary"></i>
                            Request Details
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Type of Report</label>
                                <p class="text-base text-gray-900">
                                    @php
                                        $typeIcons = [
                                            'traffic_accident_report' => 'fa-car-crash',
                                            'medical_emergency_report' => 'fa-heartbeat',
                                            'fire_incident_report' => 'fa-fire',
                                            'general_emergency_report' => 'fa-exclamation-triangle',
                                            'vehicle_accident_report' => 'fa-ambulance',
                                            'incident_report' => 'fa-clipboard-list',
                                        ];
                                        $icon = $typeIcons[$request->request_type] ?? 'fa-file-alt';
                                    @endphp
                                    <i class="fas {{ $icon }} text-primary mr-2"></i>
                                    {{ str_replace('_', ' ', ucwords($request->request_type)) }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Municipality</label>
                                <p class="text-base text-gray-900">{{ $request->municipality }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Description</label>
                                <div class="p-4 bg-gray-50 rounded-lg mt-2">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $request->request_description }}</p>
                                </div>
                            </div>
                            @if($request->purpose_of_request)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Purpose of Request</label>
                                <div class="p-4 bg-gray-50 rounded-lg mt-2">
                                    <p class="text-gray-700">{{ $request->purpose_of_request }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Incident Reference --}}
                @if($request->incident_case_number || $request->incident_date || $request->incident_location)
                <div class="card bg-white shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-link text-primary"></i>
                            Incident Reference
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($request->incident_case_number)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Case Number</label>
                                <p class="text-base text-gray-900 font-mono">{{ $request->incident_case_number }}</p>
                            </div>
                            @endif
                            @if($request->incident_date)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Incident Date</label>
                                <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($request->incident_date)->format('F d, Y') }}</p>
                            </div>
                            @endif
                            @if($request->incident_location)
                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-gray-600">Incident Location</label>
                                <p class="text-base text-gray-900">{{ $request->incident_location }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Incident Verification Tool --}}
                @if(in_array($request->status, ['pending', 'processing']))
                <div class="card bg-blue-50 border-2 border-blue-200 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600"></i>
                            Verify Incident Record
                        </h2>
                        <p class="text-sm text-gray-700 mb-4">Search for incidents by victim name to verify if a record exists</p>
                        <div class="form-control">
                            <div class="input-group">
                                <input type="text"
                                       id="victimSearchInput"
                                       placeholder="Enter victim name..."
                                       class="input input-bordered flex-1"
                                       onkeypress="if(event.key === 'Enter') searchIncidentsByVictim()">
                                <button class="btn btn-primary" onclick="searchIncidentsByVictim()">
                                    <i class="fas fa-search mr-2"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                        <div id="searchResults" class="mt-4"></div>
                    </div>
                </div>
                @endif

                {{-- Internal Notes --}}
                @if($request->internal_notes)
                <div class="card bg-yellow-50 border border-yellow-200 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-yellow-600"></i>
                            Internal Notes
                        </h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $request->internal_notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column - Status & Actions --}}
            <div class="space-y-6">
                {{-- Processing Status --}}
                <div class="card bg-white shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-tasks text-primary"></i>
                            Processing Status
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Submitted</label>
                                <p class="text-base text-gray-900">{{ $request->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                            @if($request->assignedStaff)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Assigned To</label>
                                <p class="text-base text-gray-900">{{ $request->assignedStaff->name }}</p>
                            </div>
                            @endif
                            @if($request->processing_started_at)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Processing Started</label>
                                <p class="text-base text-gray-900">{{ $request->processing_started_at->format('M d, Y g:i A') }}</p>
                            </div>
                            @endif
                            @if($request->approved_at)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Decision Date</label>
                                <p class="text-base text-gray-900">{{ $request->approved_at->format('M d, Y g:i A') }}</p>
                            </div>
                            @endif
                            @if($request->approvedBy)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Decided By</label>
                                <p class="text-base text-gray-900">{{ $request->approvedBy->name }}</p>
                            </div>
                            @endif
                            @if($request->completed_at)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Completed</label>
                                <p class="text-base text-gray-900">{{ $request->completed_at->format('M d, Y g:i A') }}</p>
                            </div>
                            @endif
                            @if($request->processing_days)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Processing Time</label>
                                <p class="text-base text-gray-900">{{ $request->processing_days }} days</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Decision Information --}}
                @if($request->status === 'approved' || $request->status === 'completed')
                <div class="card bg-green-50 border-2 border-green-200 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Approval Details
                        </h2>
                        @if($request->approval_notes)
                        <p class="text-gray-700">{{ $request->approval_notes }}</p>
                        @else
                        <p class="text-gray-600">Request has been approved.</p>
                        @endif
                    </div>
                </div>
                @elseif($request->status === 'rejected')
                <div class="card bg-red-50 border-2 border-red-200 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-times-circle text-error"></i>
                            Rejection Details
                        </h2>
                        <p class="text-gray-700">{{ $request->rejection_reason ?? 'Request has been rejected.' }}</p>
                    </div>
                </div>
                @endif

                {{-- Quick Actions --}}
                @if(in_array($request->status, ['pending', 'processing']))
                <div class="card bg-white shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                            <i class="fas fa-bolt text-primary"></i>
                            Quick Actions
                        </h2>
                        <div class="space-y-2">
                            @if($request->assigned_staff_id !== auth()->id())
                            <form action="{{ route('requests.assign', $request) }}" method="POST">
                                @csrf
                                <input type="hidden" name="staff_id" value="{{ auth()->id() }}">
                                <button type="submit" class="btn btn-outline btn-block gap-2">
                                    <i class="fas fa-hand-paper"></i>
                                    <span>Assign to Me</span>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('requests.edit', $request) }}" class="btn btn-primary btn-block gap-2">
                                <i class="fas fa-edit"></i>
                                <span>Edit & Process</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function searchIncidentsByVictim() {
    const victimName = document.getElementById('victimSearchInput').value.trim();
    const resultsDiv = document.getElementById('searchResults');

    if (!victimName || victimName.length < 2) {
        resultsDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-info-circle mr-2"></i>Please enter at least 2 characters</div>';
        return;
    }

    resultsDiv.innerHTML = '<div class="flex justify-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-primary"></i></div>';

    fetch(`/api/requests/search-incidents?victim_name=${encodeURIComponent(victimName)}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.count > 0) {
            let html = `<div class="alert alert-info mb-4"><i class="fas fa-check-circle mr-2"></i>Found ${data.count} incident(s)</div>`;
            html += '<div class="space-y-3">';

            data.incidents.forEach(incident => {
                html += `
                    <div class="card bg-white border border-gray-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-mono font-bold text-primary">${incident.incident_number}</span>
                                <span class="badge badge-sm ${incident.severity_level === 'critical' ? 'badge-error' : incident.severity_level === 'high' ? 'badge-warning' : 'badge-info'}">${incident.severity_level}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-calendar mr-1"></i> ${incident.incident_date} |
                                <i class="fas fa-map-marker-alt mr-1"></i> ${incident.location}
                            </p>
                            <div class="text-sm mb-2">
                                <strong>Victims:</strong>
                                ${incident.victims.map(v => `${v.name} (${v.age}, ${v.gender})`).join(', ')}
                            </div>
                            <a href="/incidents/${incident.incident_id}" class="btn btn-sm btn-primary gap-1" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                                View Full Incident
                            </a>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-info-circle mr-2"></i>No incidents found with matching victim names</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultsDiv.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-triangle mr-2"></i>Error searching incidents. Please try again.</div>';
    });
}
</script>
@endpush

