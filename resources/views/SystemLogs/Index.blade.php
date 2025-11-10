@extends('Layouts.app')

@section('title', 'System Activity Logs - MDRRMC')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-6 py-6">

        {{-- Page Header --}}
        <header class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-history text-accent" aria-hidden="true"></i>
                        <span>System Activity Logs</span>
                    </h1>
                    <p class="text-base text-gray-600 mt-1">Monitor all system activities and user actions across the platform</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <button type="button" class="btn btn-success gap-2 w-full sm:w-auto min-h-[44px]" onclick="window.location.reload()" aria-label="Refresh logs">
                        <i class="fas fa-redo" aria-hidden="true"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" role="region" aria-label="System log statistics">
            {{-- Total Logs --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-info">
                        <i class="fas fa-database text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Total Logs</div>
                    <div class="stat-value text-info">{{ number_format($stats['total_logs']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">All system logs</div>
                </div>
            </div>

            {{-- Today's Activity --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-success">
                        <i class="fas fa-chart-line text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Today's Activity</div>
                    <div class="stat-value text-success">{{ number_format($stats['today_logs']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Recent activity</div>
                </div>
            </div>

            {{-- Login Success Rate --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <i class="fas fa-shield-alt text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Login Success Rate</div>
                    @php
                        $totalLogins = $stats['successful_logins'] + $stats['failed_logins'];
                        $successRate = $totalLogins > 0 ? round(($stats['successful_logins'] / $totalLogins) * 100, 1) : 0;
                    @endphp
                    <div class="stat-value text-warning">{{ $successRate }}%</div>
                    <div class="stat-desc text-sm text-gray-500">Security metric</div>
                </div>
            </div>

            {{-- Active Users Today --}}
            <div class="stats shadow bg-white hover:shadow-lg transition-shadow">
                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fas fa-user-friends text-4xl" aria-hidden="true"></i>
                    </div>
                    <div class="stat-title text-gray-600">Active Users Today</div>
                    <div class="stat-value text-accent">{{ number_format($stats['active_users_today']) }}</div>
                    <div class="stat-desc text-sm text-gray-500">Unique users</div>
                </div>
            </div>
        </div>
        {{-- Main Logs Table Card --}}
        <div class="card bg-white shadow-lg">
            <div class="card-body p-0">
                <div class="px-4 py-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Activity Logs</h2>
                            <p class="text-sm text-gray-500 mt-1 md:mt-2">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} results
                            </p>
                        </div>
                        <form method="GET" action="{{ route('system.logs') }}" class="w-full md:w-auto">
                            <div class="flex flex-col md:flex-row md:justify-end md:items-end gap-3">
                                {{-- Search Input --}}
                                <div class="form-control">
                                    <label for="search" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Search</span>
                                    </label>
                                    <input type="text" name="search" id="search" value="{{ $search }}"
                                           placeholder="User, action, email..."
                                           class="input input-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                </div>

                                {{-- Log Type Filter --}}
                                <div class="form-control">
                                    <label for="log_type" class="label">
                                        <span class="label-text font-medium text-gray-700 my-1">Log Type</span>
                                    </label>
                                    <select name="log_type" id="log_type" class="select select-bordered w-full focus:outline-primary min-h-[44px] focus:border-primary">
                                        <option value="" {{ $logType === '' ? 'selected' : '' }}>All Log Types</option>
                                        <option value="activity" {{ $logType === 'activity' ? 'selected' : '' }}>General Activity</option>
                                        <option value="login" {{ $logType === 'login' ? 'selected' : '' }}>Login Logs</option>
                                        <option value="created" {{ $logType === 'created' ? 'selected' : '' }}>Created Records</option>
                                        <option value="updated" {{ $logType === 'updated' ? 'selected' : '' }}>Updated Records</option>
                                        <option value="deleted" {{ $logType === 'deleted' ? 'selected' : '' }}>Deleted Records</option>
                                    </select>
                                </div>

                                {{-- Filter Actions --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-gray-700 opacity-0">Actions</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn btn-primary gap-2 min-h-[44px] px-6">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <span>Apply</span>
                                        </button>
                                        <a href="{{ route('system.logs') }}" class="btn btn-outline gap-2 min-h-[44px]" aria-label="Clear all filters">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span>Clear</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Filters Display --}}
                            @if($search || $logType)
                            <div class="flex items-center justify-end gap-2 flex-wrap pt-2">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>
                                @if($search)
                                    <span class="badge badge-primary gap-1">
                                        <span>Search: "{{ $search }}"</span>
                                    </span>
                                @endif
                                @if($logType)
                                    <span class="badge badge-info gap-1">
                                        <span>{{ ucfirst($logType) }} Logs</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
                </div>

                <div class="overflow-x-auto">
                    @if($logs->count() > 0)
                        <table class="table table-zebra w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="font-semibold text-gray-700">Time</th>
                                    <th class="font-semibold text-gray-700">Type</th>
                                    <th class="font-semibold text-gray-700">User</th>
                                    <th class="font-semibold text-gray-700">Email</th>
                                    <th class="font-semibold text-gray-700">Activity</th>
                                    <th class="font-semibold text-gray-700 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr class="hover" data-log-id="{{ $log->id }}">
                                        {{-- Time --}}
                                        <td>
                                            <div class="text-sm text-gray-700">
                                                <div class="font-medium">{{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('h:i A') }}</div>
                                            </div>
                                        </td>
                                        {{-- Type Badge --}}
                                        <td>
                                            @if($log->log_name === 'login')
                                                @if(str_contains($log->description, 'completed login') || str_contains($log->description, 'logged in'))
                                                    <span class="badge badge-success">Login</span>
                                                @elseif(str_contains($log->description, 'logged out'))
                                                    <span class="badge badge-error">Logout</span>
                                                @else
                                                    <span class="badge badge-error">Failed</span>
                                                @endif
                                            @elseif(str_contains($log->description, 'created'))
                                                <span class="badge badge-success">Created</span>
                                            @elseif(str_contains($log->description, 'updated'))
                                                <span class="badge badge-info">Updated</span>
                                            @elseif(str_contains($log->description, 'deleted'))
                                                <span class="badge badge-error">Deleted</span>
                                            @else
                                                <span class="badge badge-ghost">Activity</span>
                                            @endif
                                        </td>
                                        {{-- User Information --}}
                                        <td>
                                            @if($log->first_name || $log->last_name)
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $log->first_name }} {{ $log->last_name }}
                                                    </div>
                                                    @if($log->role)
                                                        @php
                                                            $roleBadgeClass = match($log->role) {
                                                                'admin' => 'badge-error',
                                                                'staff' => 'badge-info',
                                                                'responder' => 'badge-warning',
                                                                'citizen' => 'badge-success',
                                                                default => 'badge-ghost'
                                                            };
                                                            $roleIcon = match($log->role) {
                                                                'admin' => 'fa-user-shield',
                                                                'staff' => 'fa-user-tie',
                                                                'responder' => 'fa-user-nurse',
                                                                'citizen' => 'fa-user',
                                                                default => 'fa-user'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $roleBadgeClass }} badge-sm gap-1 mt-1">
                                                            <i class="fas {{ $roleIcon }}"></i>
                                                            {{ ucfirst($log->role) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500 italic">System</span>
                                            @endif
                                        </td>
                                        {{-- Email --}}
                                        <td>
                                            @if($log->email)
                                                <span class="text-sm text-gray-700">{{ $log->email }}</span>
                                            @else
                                                <span class="text-sm text-gray-400 italic">N/A</span>
                                            @endif
                                        </td>
                                        {{-- Activity Description --}}
                                        <td class="text-sm text-gray-700">
                                            <div class="font-medium">{{ Str::title(Str::limit($log->description, 60)) }}</div>
                                        </td>
                                        {{-- Actions Dropdown --}}
                                        <td class="text-center">
                                            <div class="dropdown dropdown-end">
                                                <button type="button"
                                                        tabindex="0"
                                                        class="btn btn-ghost btn-sm min-h-[44px] min-w-[44px]"
                                                        aria-label="Actions for log {{ $log->id }}"
                                                        aria-haspopup="true">
                                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul tabindex="0"
                                                    class="dropdown-content z-10 menu p-2 shadow-lg bg-white rounded-box w-52 border border-gray-200"
                                                    role="menu">
                                                    <li role="none">
                                                        <a onclick='showLogDetails(@json($log->log_details))'
                                                           class="flex items-center gap-3 text-gray-700 hover:bg-primary hover:text-white min-h-[44px]"
                                                           role="menuitem">
                                                            <i class="fas fa-eye w-4" aria-hidden="true"></i>
                                                            <span>View Details</span>
                                                        </a>
                                                    </li>
                                                    @if(str_contains($log->description, 'deleted'))
                                                        <div class="divider my-0"></div>
                                                        <li role="none">
                                                            <a onclick="recoverRecord({{ $log->subject_id }}, '{{ $log->subject_type }}')"
                                                               class="flex items-center gap-3 text-gray-700 hover:bg-success hover:text-white min-h-[44px]"
                                                               role="menuitem">
                                                                <i class="fas fa-undo w-4" aria-hidden="true"></i>
                                                                <span>Recover Record</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        @if($logs->hasPages())
                            <div class="border-t border-gray-200 px-6 py-4">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-16 px-4">
                            <i class="fas fa-history text-6xl text-gray-300 mb-4" aria-hidden="true"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Logs Found</h3>
                            <p class="text-gray-500 mb-6">
                                @if($search || $logType)
                                    No logs match your current filters. Try adjusting your search criteria.
                                @else
                                    There are no activity logs to display yet.
                                @endif
                            </p>
                            @if($search || $logType)
                                <a href="{{ route('system.logs') }}" class="btn btn-outline gap-2">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    <span>Clear Filters</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Log Details Modal - Enhanced with DaisyUI Best Practices --}}
<dialog id="logDetailsModal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-11/12 max-w-5xl" style="max-width: 80rem ;">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-base-300">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 bg-info/10 rounded-lg">
                    <i class="fas fa-info-circle text-info text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xl text-base-content">Log Details</h3>
                    <p class="text-sm text-base-content/60">Complete activity information</p>
                </div>
            </div>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost hover:bg-base-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </form>
        </div>

        <!-- Modal Content -->
        <div id="logDetailsContent" class="space-y-6">
            <!-- Content will be dynamically loaded here -->
        </div>

        <!-- Modal Actions -->
        <div class="modal-action mt-6">
            <form method="dialog">
                <button class="btn btn-primary w-full sm:w-auto">
                    Close
                </button>
            </form>
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

    // Format date to Philippine Standard Time (12-hour format with AM/PM)
    const date = new Date(logData.created_at);
    const formattedDate = date.toLocaleString('en-US', {
        timeZone: 'Asia/Manila',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
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
        <!-- Log Overview -->
        <div class="bg-gradient-to-br from-primary/10 to-accent/10 rounded-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="badge badge-lg badge-primary gap-2">
                    <i class="fas fa-hashtag"></i>
                    Log #${logData.id}
                </span>
                ${logTypeBadge}
            </div>
            <p class="text-lg font-medium text-gray-800">${logData.description}</p>
        </div>

        <!-- Activity Details -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <tbody>
                        <tr class="hover">
                            <td class="font-semibold text-gray-600 w-1/3">
                                <i class="fas fa-user text-primary mr-2"></i>Performed By
                            </td>
                            <td class="font-medium text-gray-800">${logData.causer || 'System'}</td>
                        </tr>
                        ${logData.email ? `
                        <tr class="hover">
                            <td class="font-semibold text-gray-600">
                                <i class="fas fa-envelope text-info mr-2"></i>Email
                            </td>
                            <td class="font-medium text-gray-800">${logData.email}</td>
                        </tr>` : ''}
                        ${logData.role ? `
                        <tr class="hover">
                            <td class="font-semibold text-gray-600">
                                <i class="fas fa-shield-alt text-warning mr-2"></i>Role
                            </td>
                            <td>
                                <span class="badge badge-outline capitalize">${logData.role}</span>
                            </td>
                        </tr>` : ''}
                        ${logData.municipality ? `
                        <tr class="hover">
                            <td class="font-semibold text-gray-600">
                                <i class="fas fa-map-marker-alt text-error mr-2"></i>Municipality
                            </td>
                            <td class="font-medium text-gray-800">${logData.municipality}</td>
                        </tr>` : ''}
                        <tr class="hover">
                            <td class="font-semibold text-gray-600">
                                <i class="fas fa-clock text-accent mr-2"></i>Timestamp
                            </td>
                            <td class="font-medium text-gray-800">${formattedDate}</td>
                        </tr>
                        ${logData.properties && JSON.parse(JSON.stringify(logData.properties)).ip_address ? `
                        <tr class="hover">
                            <td class="font-semibold text-gray-600">
                                <i class="fas fa-network-wired text-success mr-2"></i>IP Address
                            </td>
                            <td>
                                <span class="badge badge-ghost font-mono">${JSON.parse(JSON.stringify(logData.properties)).ip_address}</span>
                            </td>
                        </tr>` : ''}
                    </tbody>
                </table>
            </div>
        </div>
    `;


    modal.showModal();
}

// Recover Record Function (Static for now)
function recoverRecord(subjectId, subjectType) {
    const resourceType = subjectType ? subjectType.split('\\').pop() : 'Record';

    showInfoToast(`Recovery feature for ${resourceType} #${subjectId} - Coming Soon!`);

    // Future implementation will go here:
    // - Confirm recovery with modal
    // - Send AJAX request to recovery endpoint
    // - Show success/error toast
    // - Reload table

    console.log('Recover record:', {
        id: subjectId,
        type: subjectType
    });
}
</script>
@endpush
@endsection
