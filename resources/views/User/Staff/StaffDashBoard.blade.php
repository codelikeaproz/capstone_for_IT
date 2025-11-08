@extends('Layouts.app')

@section('title', 'Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-base-200">
    <!-- Dashboard Header -->
    <div class="bg-white shadow-sm py-4 px-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Staff Dashboard</h1>
                <p class="text-sm text-gray-600">{{ auth()->user()->municipality }} Municipality</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('incidents.create') }}" class="btn btn-error">
                    <i class="fas fa-plus mr-2"></i>Report Incident
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Incidents -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Incidents</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['incidents']['total'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-semibold">{{ $stats['incidents']['active'] ?? 0 }}</span>
                    <span class="text-gray-600 ml-2">Active</span>
                </div>
            </div>

            <!-- My Assigned Incidents -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">My Assigned</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $myIncidents->count() }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('incidents.index', ['assigned_staff_id' => auth()->id()]) }}" class="text-sm text-orange-600 hover:underline">
                        View Details →
                    </a>
                </div>
            </div>

            <!-- Available Vehicles -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Available Vehicles</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['vehicles']['available'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-ambulance text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-600">Total: {{ $stats['vehicles']['total'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending Requests</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['requests']['pending'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-purple-600 font-semibold">{{ $stats['requests']['processing'] ?? 0 }}</span>
                    <span class="text-gray-600 ml-2">Processing</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="px-6 pb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- My Assigned Incidents -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-tasks mr-2 text-orange-600"></i>
                            My Assigned Incidents
                        </h2>
                        <a href="{{ route('incidents.index', ['assigned_staff_id' => auth()->id()]) }}" class="text-sm text-orange-600 hover:underline">
                            View All
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($myIncidents && $myIncidents->count() > 0)
                        <div class="space-y-3">
                            @foreach($myIncidents as $incident)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="font-semibold text-gray-800">{{ $incident->incident_number }}</h3>
                                                <span class="badge badge-sm
                                                    @if($incident->severity_level === 'critical') badge-error
                                                    @elseif($incident->severity_level === 'high') badge-warning
                                                    @elseif($incident->severity_level === 'medium') badge-info
                                                    @else badge-neutral
                                                    @endif">
                                                    {{ ucfirst($incident->severity_level ?? 'N/A') }}
                                                </span>
                                                <span class="badge badge-sm badge-outline">
                                                    {{ ucfirst($incident->status ?? 'N/A') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $incident->incident_type_label }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $incident->barangay }}, {{ $incident->municipality }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $incident->incident_date ? $incident->incident_date->format('M d, Y h:i A') : 'N/A' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No incidents assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Assigned Requests -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-clipboard-check mr-2 text-purple-600"></i>
                            My Assigned Requests
                        </h2>
                        <a href="{{ route('requests.index') }}" class="text-sm text-purple-600 hover:underline">
                            View All
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($myRequests && $myRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($myRequests as $request)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="font-semibold text-gray-800">Request #{{ $request->id }}</h3>
                                                <span class="badge badge-sm
                                                    @if($request->status === 'pending') badge-warning
                                                    @elseif($request->status === 'processing') badge-info
                                                    @elseif($request->status === 'approved') badge-success
                                                    @else badge-neutral
                                                    @endif">
                                                    {{ ucfirst($request->status ?? 'N/A') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $request->request_type ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $request->created_at ? $request->created_at->format('M d, Y h:i A') : 'N/A' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No requests assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Team Activity -->
            <div class="bg-white rounded-lg shadow lg:col-span-2">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-users mr-2 text-blue-600"></i>
                        Recent Team Activity
                    </h2>
                </div>
                <div class="p-6">
                    @if($teamActivity && isset($teamActivity['recent_activity']) && $teamActivity['recent_activity']->count() > 0)
                        <div class="space-y-3">
                            @foreach($teamActivity['recent_activity'] as $activity)
                                <div class="flex items-start space-x-3 pb-3 border-b border-gray-100 last:border-0">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800">
                                            <span class="font-semibold">{{ $activity->first_name }} {{ $activity->last_name }}</span>
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">No recent team activity.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="px-6 pb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                Quick Actions
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('incidents.create') }}" class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all">
                    <i class="fas fa-plus-circle text-3xl text-orange-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">New Incident</span>
                </a>
                <a href="{{ route('incidents.index') }}" class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                    <i class="fas fa-list text-3xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">View Incidents</span>
                </a>
                <a href="{{ route('vehicles.index') }}" class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all">
                    <i class="fas fa-ambulance text-3xl text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Vehicles</span>
                </a>
                <a href="{{ route('victims.index') }}" class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all">
                    <i class="fas fa-user-injured text-3xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700">Victims</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush
