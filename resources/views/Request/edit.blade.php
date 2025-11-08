@extends("Layouts.app")

@section('title', 'Edit Request - ' . $request->request_number)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-5xl">

        {{-- Breadcrumbs --}}
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('requests.index') }}">Requests</a></li>
                <li><a href="{{ route('requests.show', $request) }}">{{ $request->request_number }}</a></li>
                <li class="font-semibold">Edit</li>
            </ul>
        </div>

        {{-- Page Header --}}
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Edit Request: {{ $request->request_number }}
            </h1>
            <p class="text-gray-600">Update request status, assign staff, and manage approval workflow</p>
        </header>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-error shadow-lg mb-6" role="alert">
                <div>
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <div>
                        <h3 class="font-bold">Please correct the following errors:</h3>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('requests.update', $request) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column - Editable Fields --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Status & Assignment Section --}}
                    <div class="card bg-white shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                                <i class="fas fa-tasks text-primary"></i>
                                Request Processing
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Status --}}
                                <div class="form-control">
                                    <label for="status" class="label">
                                        <span class="label-text font-semibold text-gray-700">
                                            Status <span class="text-error">*</span>
                                        </span>
                                    </label>
                                    <select name="status" 
                                            id="status"
                                            class="select select-bordered w-full focus:outline-primary"
                                            required
                                            onchange="handleStatusChange()">
                                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                {{-- Urgency Level --}}
                                <div class="form-control">
                                    <label for="urgency_level" class="label">
                                        <span class="label-text font-semibold text-gray-700">
                                            Urgency Level <span class="text-error">*</span>
                                        </span>
                                    </label>
                                    <select name="urgency_level" 
                                            id="urgency_level"
                                            class="select select-bordered w-full focus:outline-primary"
                                            required>
                                        <option value="low" {{ $request->urgency_level == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $request->urgency_level == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ $request->urgency_level == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ $request->urgency_level == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>

                                {{-- Assigned Staff --}}
                                <div class="form-control md:col-span-2">
                                    <label for="assigned_staff_id" class="label">
                                        <span class="label-text font-semibold text-gray-700">
                                            Assign to Staff Member
                                        </span>
                                    </label>
                                    <select name="assigned_staff_id" 
                                            id="assigned_staff_id"
                                            class="select select-bordered w-full focus:outline-primary">
                                        <option value="">-- Unassigned --</option>
                                        @foreach($staff as $staffMember)
                                            <option value="{{ $staffMember->id }}" 
                                                    {{ $request->assigned_staff_id == $staffMember->id ? 'selected' : '' }}>
                                                {{ $staffMember->name }} ({{ $staffMember->municipality }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Conditional: Approval Fields --}}
                    <div id="approvalSection" class="card bg-green-50 border-2 border-green-200 shadow-lg" style="display: none;">
                        <div class="card-body">
                            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                Approval Details
                            </h2>

                            <div class="space-y-4">
                                {{-- Approval Notes --}}
                                <div class="form-control">
                                    <label for="approval_notes" class="label">
                                        <span class="label-text font-semibold text-gray-700">
                                            Approval Notes (Optional)
                                        </span>
                                    </label>
                                    <textarea name="approval_notes" 
                                              id="approval_notes"
                                              rows="3"
                                              class="textarea textarea-bordered w-full focus:outline-primary"
                                              placeholder="Enter any notes about the approval...">{{ old('approval_notes', $request->approval_notes) }}</textarea>
                                </div>

                                {{-- Auto-create Incident --}}
                                <div class="form-control">
                                    <label class="label cursor-pointer justify-start gap-3 bg-white p-4 rounded-lg border border-green-300">
                                        <input type="checkbox" 
                                               name="auto_create_incident" 
                                               value="1"
                                               class="checkbox checkbox-success"
                                               id="autoCreateIncident">
                                        <div>
                                            <span class="label-text font-semibold block">Auto-create Incident from Request</span>
                                            <span class="label-text-alt text-gray-600">Automatically generate an incident record when approving this request</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Conditional: Rejection Fields --}}
                    <div id="rejectionSection" class="card bg-red-50 border-2 border-red-200 shadow-lg" style="display: none;">
                        <div class="card-body">
                            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                                <i class="fas fa-times-circle text-error"></i>
                                Rejection Details
                            </h2>

                            <div class="form-control">
                                <label for="rejection_reason" class="label">
                                    <span class="label-text font-semibold text-gray-700">
                                        Reason for Rejection <span class="text-error">*</span>
                                    </span>
                                </label>
                                <textarea name="rejection_reason" 
                                          id="rejection_reason"
                                          rows="4"
                                          class="textarea textarea-bordered w-full focus:outline-primary"
                                          placeholder="Provide a clear reason for rejecting this request...">{{ old('rejection_reason', $request->rejection_reason) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Internal Notes --}}
                    <div class="card bg-white shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-xl mb-4 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-primary"></i>
                                Internal Notes
                            </h2>

                            <div class="form-control">
                                <label for="internal_notes" class="label">
                                    <span class="label-text font-semibold text-gray-700">
                                        Staff Notes (Internal Only)
                                    </span>
                                </label>
                                <textarea name="internal_notes" 
                                          id="internal_notes"
                                          rows="4"
                                          class="textarea textarea-bordered w-full focus:outline-primary"
                                          placeholder="Add any internal notes for staff reference...">{{ old('internal_notes', $request->internal_notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Incident Verification Tool --}}
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
                                           onkeypress="if(event.key === 'Enter') { event.preventDefault(); searchIncidentsByVictim(); }">
                                    <button type="button" class="btn btn-primary" onclick="searchIncidentsByVictim()">
                                        <i class="fas fa-search mr-2"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                            <div id="searchResults" class="mt-4"></div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Read-only Info --}}
                <div class="space-y-6">
                    {{-- Requester Info --}}
                    <div class="card bg-white shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-lg mb-3 flex items-center gap-2">
                                <i class="fas fa-user text-primary"></i>
                                Requester
                            </h2>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Name</label>
                                    <p class="text-gray-900">{{ $request->requester_name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Email</label>
                                    <p class="text-gray-900">{{ $request->requester_email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Phone</label>
                                    <p class="text-gray-900">{{ $request->requester_phone }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Municipality</label>
                                    <p class="text-gray-900">{{ $request->municipality }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Request Details --}}
                    <div class="card bg-white shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-lg mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-primary"></i>
                                Request Info
                            </h2>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Type</label>
                                    <p class="text-gray-900">{{ str_replace('_', ' ', ucwords($request->request_type)) }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Submitted</label>
                                    <p class="text-gray-900">{{ $request->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                                @if($request->incident_case_number)
                                <div>
                                    <label class="text-xs font-semibold text-gray-600">Case Number</label>
                                    <p class="text-gray-900 font-mono">{{ $request->incident_case_number }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card bg-gray-100 shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-lg mb-3 flex items-center gap-2">
                                <i class="fas fa-bolt text-primary"></i>
                                Actions
                            </h2>
                            <div class="space-y-2">
                                <a href="{{ route('requests.show', $request) }}" class="btn btn-outline btn-sm btn-block gap-2">
                                    <i class="fas fa-eye"></i>
                                    <span>View Full Details</span>
                                </a>
                                <a href="{{ route('requests.index') }}" class="btn btn-ghost btn-sm btn-block gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to List</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t-2 border-gray-200">
                <button type="submit" class="btn btn-primary btn-lg gap-2 flex-1">
                    <i class="fas fa-save"></i>
                    <span>Update Request</span>
                </button>
                <a href="{{ route('requests.show', $request) }}" class="btn btn-outline btn-lg gap-2 sm:w-auto">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle status change to show/hide conditional sections
function handleStatusChange() {
    const status = document.getElementById('status').value;
    const approvalSection = document.getElementById('approvalSection');
    const rejectionSection = document.getElementById('rejectionSection');
    const rejectionReason = document.getElementById('rejection_reason');
    
    // Hide all conditional sections first
    approvalSection.style.display = 'none';
    rejectionSection.style.display = 'none';
    rejectionReason.removeAttribute('required');
    
    // Show relevant section based on status
    if (status === 'approved' || status === 'completed') {
        approvalSection.style.display = 'block';
    } else if (status === 'rejected') {
        rejectionSection.style.display = 'block';
        rejectionReason.setAttribute('required', 'required');
    }
}

// Search incidents by victim name
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    handleStatusChange();
});
</script>
@endpush






