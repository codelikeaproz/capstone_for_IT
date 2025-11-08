<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Status: {{ $citizenRequest->request_number }} - MDRRMO Bukidnon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .timeline-step {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
        }
        .timeline-step:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 0.875rem;
            top: 2rem;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        .timeline-step.active::before {
            background: #3b82f6;
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0.25rem;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e5e7eb;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #e5e7eb;
        }
        .timeline-step.active .timeline-dot {
            background: #3b82f6;
            box-shadow: 0 0 0 3px #bfdbfe;
        }
        .timeline-step.completed .timeline-dot {
            background: #10b981;
            box-shadow: 0 0 0 3px #d1fae5;
        }
        .timeline-step.rejected .timeline-dot {
            background: #ef4444;
            box-shadow: 0 0 0 3px #fee2e2;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Public Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-10 h-10">
                    <div>
                        <h1 class="font-bold text-xl">MDRRMO Bukidnon</h1>
                        <p class="text-xs text-blue-200">Request Status Portal</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('requests.status-check') }}" class="btn btn-sm btn-outline text-white border-white hover:bg-white hover:text-blue-900">
                        <i class="fas fa-search"></i>
                        <span class="hidden sm:inline">Check Another</span>
                    </a>
                    <a href="{{ route('requests.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i>
                        <span class="hidden sm:inline">New Request</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- Request Header -->
        <header class="bg-white rounded-lg shadow-lg p-6 md:p-8 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="badge badge-lg {{ $citizenRequest->status_badge }} font-mono font-bold text-lg px-4 py-3">
                            {{ strtoupper($citizenRequest->status) }}
                        </span>
                        @if($citizenRequest->urgency_level === 'critical')
                            <span class="badge badge-error badge-lg">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Critical
                            </span>
                        @elseif($citizenRequest->urgency_level === 'high')
                            <span class="badge badge-warning badge-lg">
                                <i class="fas fa-exclamation-circle mr-1"></i> High Priority
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-mono">
                        {{ $citizenRequest->request_number }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-calendar mr-2"></i>
                        Submitted on {{ $citizenRequest->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="text-center md:text-right">
                    <div class="text-5xl mb-2">
                        @if($citizenRequest->status === 'completed' || $citizenRequest->status === 'approved')
                            <i class="fas fa-check-circle text-success"></i>
                        @elseif($citizenRequest->status === 'rejected')
                            <i class="fas fa-times-circle text-error"></i>
                        @elseif($citizenRequest->status === 'processing')
                            <i class="fas fa-spinner fa-pulse text-info"></i>
                        @else
                            <i class="fas fa-clock text-warning"></i>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-stream text-primary"></i>
                Request Timeline
            </h2>

            <div class="max-w-3xl">
                <!-- Step 1: Submitted -->
                <div class="timeline-step completed">
                    <div class="timeline-dot">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900">Request Submitted</h3>
                        <p class="text-sm text-gray-600">{{ $citizenRequest->created_at->format('F d, Y \a\t g:i A') }}</p>
                        <p class="text-sm text-gray-700 mt-1">Your request has been received successfully.</p>
                    </div>
                </div>

                <!-- Step 2: Processing -->
                <div class="timeline-step {{ in_array($citizenRequest->status, ['processing', 'approved', 'completed']) ? 'completed' : ($citizenRequest->status === 'pending' ? '' : 'rejected') }}">
                    <div class="timeline-dot">
                        @if(in_array($citizenRequest->status, ['processing', 'approved', 'completed']))
                            <i class="fas fa-check text-white text-sm"></i>
                        @else
                            <i class="fas fa-circle text-white text-xs"></i>
                        @endif
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900">Under Review</h3>
                        @if($citizenRequest->processing_started_at)
                            <p class="text-sm text-gray-600">{{ $citizenRequest->processing_started_at->format('F d, Y \a\t g:i A') }}</p>
                        @else
                            <p class="text-sm text-gray-600">Waiting for staff review...</p>
                        @endif
                        @if($citizenRequest->assignedStaff)
                            <p class="text-sm text-gray-700 mt-1">
                                <i class="fas fa-user mr-1"></i> Assigned to: <strong>{{ $citizenRequest->assignedStaff->name }}</strong>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Step 3: Decision -->
                @if($citizenRequest->status === 'approved' || $citizenRequest->status === 'completed')
                    <div class="timeline-step completed">
                        <div class="timeline-dot">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-900">Request Approved</h3>
                            <p class="text-sm text-green-700">{{ $citizenRequest->approved_at?->format('F d, Y \a\t g:i A') }}</p>
                            @if($citizenRequest->approvedBy)
                                <p class="text-sm text-green-800 mt-1">
                                    <i class="fas fa-user-check mr-1"></i> Approved by: <strong>{{ $citizenRequest->approvedBy->name }}</strong>
                                </p>
                            @endif
                            @if($citizenRequest->approval_notes)
                                <div class="mt-2 p-3 bg-white rounded border border-green-200">
                                    <p class="text-sm text-gray-700"><strong>Notes:</strong> {{ $citizenRequest->approval_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($citizenRequest->status === 'rejected')
                    <div class="timeline-step rejected">
                        <div class="timeline-dot">
                            <i class="fas fa-times text-white text-sm"></i>
                        </div>
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                            <h3 class="font-semibold text-red-900">Request Rejected</h3>
                            <p class="text-sm text-red-700">{{ $citizenRequest->approved_at?->format('F d, Y \a\t g:i A') }}</p>
                            @if($citizenRequest->rejection_reason)
                                <div class="mt-2 p-3 bg-white rounded border border-red-200">
                                    <p class="text-sm text-gray-700"><strong>Reason:</strong> {{ $citizenRequest->rejection_reason }}</p>
                                </div>
                            @endif
                            <p class="text-sm text-red-800 mt-2">
                                <i class="fas fa-info-circle mr-1"></i> Please contact the MDRRMO office for more information.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="timeline-step">
                        <div class="timeline-dot">
                            <i class="fas fa-circle text-white text-xs"></i>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg opacity-60">
                            <h3 class="font-semibold text-gray-900">Review Decision</h3>
                            <p class="text-sm text-gray-600">Pending staff decision...</p>
                        </div>
                    </div>
                @endif

                <!-- Step 4: Completed -->
                @if($citizenRequest->status === 'completed')
                    <div class="timeline-step completed">
                        <div class="timeline-dot">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-900">Request Completed</h3>
                            <p class="text-sm text-green-700">{{ $citizenRequest->completed_at?->format('F d, Y \a\t g:i A') }}</p>
                            <p class="text-sm text-green-800 mt-1">
                                <i class="fas fa-check-circle mr-1"></i> Your report is ready for download or pickup.
                            </p>
                            @if($citizenRequest->processing_days)
                                <p class="text-sm text-gray-600 mt-1">
                                    Processing time: <strong>{{ $citizenRequest->processing_days }} days</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Request Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Request Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-file-alt text-primary"></i>
                    Request Information
                </h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-semibold text-gray-600">Type of Report</dt>
                        <dd class="text-base text-gray-900 mt-1">
                            @php
                                $typeIcons = [
                                    'traffic_accident_report' => 'fa-car-crash',
                                    'medical_emergency_report' => 'fa-heartbeat',
                                    'fire_incident_report' => 'fa-fire',
                                    'general_emergency_report' => 'fa-exclamation-triangle',
                                    'vehicle_accident_report' => 'fa-ambulance',
                                    'incident_report' => 'fa-clipboard-list',
                                ];
                                $icon = $typeIcons[$citizenRequest->request_type] ?? 'fa-file-alt';
                            @endphp
                            <i class="fas {{ $icon }} text-primary mr-2"></i>
                            {{ str_replace('_', ' ', ucwords($citizenRequest->request_type)) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-600">Municipality</dt>
                        <dd class="text-base text-gray-900 mt-1">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            {{ $citizenRequest->municipality }}
                        </dd>
                    </div>
                    @if($citizenRequest->incident_case_number)
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Incident Case Number</dt>
                            <dd class="text-base text-gray-900 mt-1 font-mono">
                                <i class="fas fa-hashtag text-primary mr-2"></i>
                                {{ $citizenRequest->incident_case_number }}
                            </dd>
                        </div>
                    @endif
                    @if($citizenRequest->incident_date)
                        <div>
                            <dt class="text-sm font-semibold text-gray-600">Incident Date</dt>
                            <dd class="text-base text-gray-900 mt-1">
                                <i class="fas fa-calendar text-primary mr-2"></i>
                                {{ \Carbon\Carbon::parse($citizenRequest->incident_date)->format('F d, Y') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-primary"></i>
                    Your Information
                </h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-semibold text-gray-600">Name</dt>
                        <dd class="text-base text-gray-900 mt-1">{{ $citizenRequest->requester_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-600">Email</dt>
                        <dd class="text-base text-gray-900 mt-1">
                            @if($citizenRequest->email_notifications_enabled)
                                <i class="fas fa-bell text-success mr-2" title="Email notifications enabled"></i>
                            @endif
                            {{ $citizenRequest->requester_email }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-600">Phone</dt>
                        <dd class="text-base text-gray-900 mt-1">
                            @if($citizenRequest->sms_notifications_enabled)
                                <i class="fas fa-bell text-success mr-2" title="SMS notifications enabled"></i>
                            @endif
                            {{ $citizenRequest->requester_phone }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Description -->
        @if($citizenRequest->request_description)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-primary"></i>
                    Request Description
                </h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $citizenRequest->request_description }}</p>
            </div>
        @endif

        <!-- Generated Reports / Downloads -->
        @if($citizenRequest->status === 'completed' && $citizenRequest->generated_reports)
            <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-green-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-download text-green-600"></i>
                    Available Reports
                </h2>
                <p class="text-green-800 mb-4">Your report is ready! Download or contact the office for pickup.</p>
                <div class="flex flex-wrap gap-3">
                    @foreach($citizenRequest->generated_reports as $report)
                        <a href="{{ Storage::url($report) }}"
                           class="btn btn-success gap-2"
                           download
                           target="_blank">
                            <i class="fas fa-file-pdf"></i>
                            <span>Download Report</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Help Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                <i class="fas fa-question-circle"></i>
                Need Assistance?
            </h3>
            <div class="text-sm text-blue-800 space-y-2">
                <p><i class="fas fa-phone mr-2"></i> Contact your local MDRRMO office for questions about your request</p>
                <p><i class="fas fa-clock mr-2"></i> Office hours: Monday-Friday, 8:00 AM - 5:00 PM</p>
                <p><i class="fas fa-hashtag mr-2"></i> Have your request number ready: <strong class="font-mono">{{ $citizenRequest->request_number }}</strong></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <a href="{{ route('requests.status-check') }}" class="btn btn-primary btn-lg gap-2 flex-1">
                <i class="fas fa-search"></i>
                <span>Check Another Request</span>
            </a>
            <a href="{{ route('requests.create') }}" class="btn btn-outline btn-lg gap-2 flex-1">
                <i class="fas fa-plus"></i>
                <span>Submit New Request</span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="mb-4">
                <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-12 h-12 mx-auto mb-2">
                <h3 class="font-bold text-lg">Municipal Disaster Risk Reduction Management Office</h3>
                <p class="text-gray-400 text-sm">Province of Bukidnon, Philippines</p>
            </div>
            <div class="flex justify-center gap-6 text-sm">
                <a href="{{ url('/') }}" class="hover:text-blue-400 transition">Home</a>
                <a href="{{ route('requests.create') }}" class="hover:text-blue-400 transition">New Request</a>
                <a href="{{ route('requests.status-check') }}" class="hover:text-blue-400 transition">Check Status</a>
            </div>
            <p class="text-gray-500 text-xs mt-4">© {{ date('Y') }} MDRRMO Bukidnon. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

