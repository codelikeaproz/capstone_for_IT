@extends('Layouts.app')

@section('content')
<div class="flex">
    {{-- @include('Components.SideBar') --}}
    <div class="content flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-800">System Logs</h1>
                <span class="ml-4 bg-purple-100 text-purple-800 text-sm font-medium px-2.5 py-0.5 rounded">Admin Only</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Logs</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_logs']) }}</p>
                            <p class="text-blue-500 text-xs mt-1"><i class="fas fa-database mr-1"></i>All system logs</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-list text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Today's Activity</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['today_logs']) }}</p>
                            <p class="text-green-500 text-xs mt-1"><i class="fas fa-calendar-day mr-1"></i>Recent activity</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Login Success Rate</p>
                            @php
                                $totalLogins = $stats['successful_logins'] + $stats['failed_logins'];
                                $successRate = $totalLogins > 0 ? round(($stats['successful_logins'] / $totalLogins) * 100, 1) : 0;
                            @endphp
                            <p class="text-2xl font-bold mt-1">{{ $successRate }}%</p>
                            <p class="text-green-500 text-xs mt-1"><i class="fas fa-shield-alt mr-1"></i>Security metric</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-brick-orange"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Active Users Today</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['active_users_today']) }}</p>
                            <p class="text-purple-500 text-xs mt-1"><i class="fas fa-users mr-1"></i>Unique users</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-user-friends text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filters</h2>
                <form method="GET" action="{{ route('system.logs') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ $search }}"
                               placeholder="User, action, email..."
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brick-orange">
                    </div>

                    <div>
                        <label for="log_name" class="block text-sm font-medium text-gray-700 mb-1">Log Type</label>
                        <select name="log_type" id="log_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brick-orange">
                            <option value="">All Types</option>
                            <option value="activity" {{ $logType === 'activity' ? 'selected' : '' }}>General Activity</option>
                            <option value="login" {{ $logType === 'login' ? 'selected' : '' }}>Login Logs</option>
                            <option value="created" {{ $logType === 'created' ? 'selected' : '' }}>Created Records</option>
                            <option value="updated" {{ $logType === 'updated' ? 'selected' : '' }}>Updated Records</option>
                            <option value="deleted" {{ $logType === 'deleted' ? 'selected' : '' }}>Deleted Records</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brick-orange">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brick-orange">
                    </div>

                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                        <select name="per_page" id="per_page" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brick-orange">
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="bg-brick-orange hover:bg-orange-700 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('system.logs') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Main Logs Table (All logs in one table) -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">System Activity Logs</h2>
                        <div class="text-sm text-gray-600">
                            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($log->created_at)->format('M d') }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
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
                        <td class="px-4 py-3 whitespace-nowrap">
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
                        <td class="px-4 py-3 text-sm text-gray-900">
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
                        <td class="px-4 py-3 text-sm text-gray-500">
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
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($log->properties)
                                <button onclick="showLogDetails({{ $log->id }}, {{ htmlspecialchars(json_encode($log->properties)) }})" 
                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-150">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">No logs found</p>
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
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Log Details</h3>
                <button onclick="closeLogDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="logDetailsContent" class="mt-2">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(logId, properties) {
    const modal = document.getElementById('logDetailsModal');
    const content = document.getElementById('logDetailsContent');

    let propertiesData;
    try {
        propertiesData = typeof properties === 'string' ? JSON.parse(properties) : properties;
    } catch (e) {
        propertiesData = properties;
    }

    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Log ID</label>
                <p class="mt-1 text-sm text-gray-900">${logId}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Properties</label>
                <pre class="mt-1 text-sm bg-gray-100 p-3 rounded-md overflow-x-auto">${JSON.stringify(propertiesData, null, 2)}</pre>
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
}

function closeLogDetails() {
    document.getElementById('logDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('logDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogDetails();
    }
});

// Auto-refresh functionality
let autoRefresh = false;
let refreshInterval;

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const button = document.getElementById('autoRefreshBtn');

    if (autoRefresh) {
        button.innerHTML = '<i class="fas fa-pause mr-2"></i>Stop Auto-refresh';
        button.classList.remove('bg-green-500', 'hover:bg-green-600');
        button.classList.add('bg-red-500', 'hover:bg-red-600');

        refreshInterval = setInterval(() => {
            window.location.reload();
        }, 30000); // Refresh every 30 seconds
    } else {
        button.innerHTML = '<i class="fas fa-play mr-2"></i>Auto-refresh';
        button.classList.remove('bg-red-500', 'hover:bg-red-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');

        clearInterval(refreshInterval);
    }
}

// Add auto-refresh button
document.addEventListener('DOMContentLoaded', function() {
    const filterSection = document.querySelector('.bg-white.rounded-lg.shadow.p-6.mb-6');
    if (filterSection) {
        const autoRefreshBtn = document.createElement('button');
        autoRefreshBtn.id = 'autoRefreshBtn';
        autoRefreshBtn.className = 'bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors duration-200 ml-2 flex items-center';
        autoRefreshBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Auto-refresh';
        autoRefreshBtn.onclick = toggleAutoRefresh;

        const buttonContainer = filterSection.querySelector('.flex.items-end.space-x-2');
        if (buttonContainer) {
            buttonContainer.appendChild(autoRefreshBtn);
        }
    }
});
</script>

@endsection