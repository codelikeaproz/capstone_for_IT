@extends('Layouts.app')

@section('title', 'System Activity Logs')

@section('content')
<div class="min-h-screen bg-base-200 py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="flex items-center justify-center w-12 h-12 bg-purple-500/10 rounded-lg">
                            <i class="fas fa-history text-purple-500 text-xl"></i>
                        </span>
                        System Activity Logs
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Monitor all system activities and user actions.
                        <span class="badge badge-sm badge-primary ml-2">Admin Only</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Logs</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_logs']) }}</p>
                                <p class="text-blue-100 text-xs mt-2">
                                    <i class="fas fa-database mr-1"></i>All system logs
                                </p>
                            </div>
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-lg">
                                <i class="fas fa-list text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Today's Activity</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($stats['today_logs']) }}</p>
                                <p class="text-green-100 text-xs mt-2">
                                    <i class="fas fa-calendar-day mr-1"></i>Recent activity
                                </p>
                            </div>
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-lg">
                                <i class="fas fa-chart-line text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Login Success Rate</p>
                                @php
                                    $totalLogins = $stats['successful_logins'] + $stats['failed_logins'];
                                    $successRate = $totalLogins > 0 ? round(($stats['successful_logins'] / $totalLogins) * 100, 1) : 0;
                                @endphp
                                <p class="text-3xl font-bold mt-2">{{ $successRate }}%</p>
                                <p class="text-orange-100 text-xs mt-2">
                                    <i class="fas fa-shield-alt mr-1"></i>Security metric
                                </p>
                            </div>
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-lg">
                                <i class="fas fa-sign-in-alt text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Active Users Today</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($stats['active_users_today']) }}</p>
                                <p class="text-purple-100 text-xs mt-2">
                                    <i class="fas fa-users mr-1"></i>Unique users
                                </p>
                            </div>
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-lg">
                                <i class="fas fa-user-friends text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-base-300">
                        <div class="flex items-center justify-center w-10 h-10 bg-primary/10 rounded-lg">
                            <i class="fas fa-filter text-primary"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Filter Logs</h2>
                            <p class="text-sm text-gray-500">Refine your search criteria</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('system.logs') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div class="form-control">
                                <label for="search" class="label">
                                    <span class="label-text font-semibold text-gray-700">Search</span>
                                </label>
                                <input type="text" name="search" id="search" value="{{ $search }}"
                                       placeholder="User, action, email..."
                                       class="input input-bordered w-full">
                            </div>

                            <div class="form-control">
                                <label for="log_type" class="label">
                                    <span class="label-text font-semibold text-gray-700">Log Type</span>
                                </label>
                                <select name="log_type" id="log_type" class="select select-bordered w-full">
                                    <option value="">All Types</option>
                                    <option value="activity" {{ $logType === 'activity' ? 'selected' : '' }}>General Activity</option>
                                    <option value="login" {{ $logType === 'login' ? 'selected' : '' }}>Login Logs</option>
                                    <option value="created" {{ $logType === 'created' ? 'selected' : '' }}>Created Records</option>
                                    <option value="updated" {{ $logType === 'updated' ? 'selected' : '' }}>Updated Records</option>
                                    <option value="deleted" {{ $logType === 'deleted' ? 'selected' : '' }}>Deleted Records</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label for="date_from" class="label">
                                    <span class="label-text font-semibold text-gray-700">From Date</span>
                                </label>
                                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                                       class="input input-bordered w-full">
                            </div>

                            <div class="form-control">
                                <label for="date_to" class="label">
                                    <span class="label-text font-semibold text-gray-700">To Date</span>
                                </label>
                                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                                       class="input input-bordered w-full">
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 justify-end">
                            <button type="submit" class="btn btn-primary gap-2">
                                <i class="fas fa-search"></i>
                                <span>Apply Filters</span>
                            </button>
                            <a href="{{ route('system.logs') }}" class="btn btn-outline gap-2">
                                <i class="fas fa-times"></i>
                                <span>Clear</span>
                            </a>
                            <button type="button" id="autoRefreshBtn" onclick="toggleAutoRefresh()" class="btn btn-success gap-2">
                                <i class="fas fa-sync-alt"></i>
                                <span>Auto-refresh</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Logs Table -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-info/10 rounded-lg">
                                <i class="fas fa-table text-info"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Activity Logs</h2>
                                <p class="text-sm text-gray-500">
                                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} results
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>IP Address</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($log->created_at)->format('M d') }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap">
                            @if($log->log_name === 'login')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ str_contains($log->description, 'Successful') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    {{ str_contains($log->description, 'Successful') ? 'Login' : 'Failed' }}
                                </span>
                            @elseif(str_contains($log->description, 'created'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Created
                                </span>
                            @elseif(str_contains($log->description, 'updated'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-edit mr-1"></i>
                                    Updated
                                </span>
                            @elseif(str_contains($log->description, 'deleted'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Deleted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-cogs mr-1"></i>
                                    Activity
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            @if($log->first_name || $log->last_name)
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                        <span class="text-xs font-medium">
                                            {{ substr($log->first_name, 0, 1) }}{{ substr($log->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->first_name }} {{ $log->last_name }}
                                        </div>
                                        @if($log->role)
                                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                                {{ $log->role }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-500 text-sm">System</span>
                            @endif
                        </td>
                        <td>
                            <div class="max-w-xs">
                                <div class="truncate" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                                @if($log->subject_type && $log->subject_id)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    </div>
                                @endif
                                @if($log->municipality)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $log->municipality }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="text-sm text-gray-500">
                            @if($log->properties)
                                @php
                                    $properties = json_decode($log->properties, true);
                                @endphp
                                @if(is_array($properties) && isset($properties['ip_address']))
                                    <div class="text-xs">
                                        <i class="fas fa-globe mr-1"></i>{{ $properties['ip_address'] }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <button onclick='showLogDetails(@json($log->log_details))' class="btn btn-ghost btn-xs btn-circle">
                                <i class="fas fa-eye text-primary"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                                <p class="text-gray-600 text-lg font-semibold mb-2">No logs found</p>
                                <p class="text-gray-400 text-sm">Try adjusting your filters or date range</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="mt-6 border-t border-base-300 pt-6">
            <div class="bg-white dark:bg-gray-100 shadow rounded-lg p-4">
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<dialog id="logDetailsModal" class="modal">
    <div class="modal-box max-w-3xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="font-bold text-lg flex items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i>
            Log Details
        </h3>
        <div id="logDetailsContent" class="mt-4">
            <!-- Content will be loaded here -->

        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

@push('scripts')
<script>
function showLogDetails(logData) {
    const modal = document.getElementById('logDetailsModal');
    const content = document.getElementById('logDetailsContent');

    // Format date
    const date = new Date(logData.created_at);
    const formattedDate = date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    // Determine log type badge
    let logTypeBadge = '';
    if (logData.log_name === 'login') {
        const isSuccess = logData.description.includes('Successful');
        logTypeBadge = `<span class="badge ${isSuccess ? 'badge-success' : 'badge-error'}">${isSuccess ? 'Login Success' : 'Login Failed'}</span>`;
    } else if (logData.description.includes('created')) {
        logTypeBadge = '<span class="badge badge-info">Created</span>';
    } else if (logData.description.includes('updated')) {
        logTypeBadge = '<span class="badge badge-warning">Updated</span>';
    } else if (logData.description.includes('deleted')) {
        logTypeBadge = '<span class="badge badge-error">Deleted</span>';
    } else {
        logTypeBadge = '<span class="badge badge-ghost">Activity</span>';
    }

    content.innerHTML = `
        <div class="space-y-4">
            <!-- Log ID and Type -->
            <div class="grid grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-700">Log ID</span>
                    </label>
                    <div class="badge badge-lg badge-primary">#${logData.id}</div>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-700">Type</span>
                    </label>
                    ${logTypeBadge}
                </div>
            </div>

            <!-- Description -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold text-gray-700">Action Description</span>
                </label>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>${logData.description}</span>
                </div>
            </div>

            <!-- User Information -->
            <div class="card bg-base-200">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-700 mb-2">User Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Performed By</p>
                            <p class="font-medium">${logData.causer || 'System'}</p>
                        </div>
                        ${logData.email ? `
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-medium">${logData.email}</p>
                        </div>` : ''}
                        ${logData.role ? `
                        <div>
                            <p class="text-xs text-gray-500">Role</p>
                            <p class="font-medium capitalize">${logData.role}</p>
                        </div>` : ''}
                        ${logData.municipality ? `
                        <div>
                            <p class="text-xs text-gray-500">Municipality</p>
                            <p class="font-medium">${logData.municipality}</p>
                        </div>` : ''}
                    </div>
                </div>
            </div>

            <!-- Subject Information -->
            ${logData.subject_type || logData.subject_id ? `
            <div class="card bg-base-200">
                <div class="card-body">
                    <h3 class="font-semibold text-gray-700 mb-2">Affected Resource</h3>
                    <div class="grid grid-cols-2 gap-4">
                        ${logData.subject_type ? `
                        <div>
                            <p class="text-xs text-gray-500">Resource Type</p>
                            <p class="font-medium">${logData.subject_type.split('\\\\').pop()}</p>
                        </div>` : ''}
                        ${logData.subject_id ? `
                        <div>
                            <p class="text-xs text-gray-500">Resource ID</p>
                            <p class="font-medium">#${logData.subject_id}</p>
                        </div>` : ''}
                    </div>
                </div>
            </div>` : ''}

            <!-- Timestamp -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold text-gray-700">Timestamp</span>
                </label>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-gray-500"></i>
                    <span class="text-sm">${formattedDate}</span>
                </div>
            </div>
            

            <!-- Properties/Additional Data -->
            ${logData.properties ? `
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold text-gray-700">Additional Details</span>
                </label>
                <div class="mockup-code">
                    <pre data-prefix=""><code class="text-xs">${JSON.stringify(logData.properties, null, 2)}</code></pre>
                </div>
            </div>` : ''}
        </div>
    `;
    

    modal.showModal();
}

// Auto-refresh functionality
let autoRefresh = false;
let refreshInterval;

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const button = document.getElementById('autoRefreshBtn');

    if (autoRefresh) {
        button.innerHTML = '<i class="fas fa-stop-circle"></i><span>Stop Auto-refresh</span>';
        button.classList.remove('btn-success');
        button.classList.add('btn-error');

        refreshInterval = setInterval(() => {
            window.location.reload();
        }, 30000); // Refresh every 30 seconds

        showInfoToast('Auto-refresh enabled (every 30 seconds)');
    } else {
        button.innerHTML = '<i class="fas fa-sync-alt"></i><span>Auto-refresh</span>';
        button.classList.remove('btn-error');
        button.classList.add('btn-success');

        clearInterval(refreshInterval);
        showInfoToast('Auto-refresh disabled');
    }
}
</script>
@endpush

@endsection
