@extends("Layouts.app")

@section('title', 'Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('styles/global.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
@endpush

@section('content')
<!-- Dashboard Header -->
<div class="bg-white shadow-sm py-6 px-6 flex items-center justify-between">
    <div class="flex items-center">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
    </div>
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('incidents.index') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Report Incident
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="fas fa-bell text-gray-500 text-xl cursor-pointer"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
            <div class="dropdown relative">
                <div class="flex items-center cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-user text-brick-orange"></i>
                    </div>
                    <span class="ml-4 text-gray-700">Admin</span>
                    <i class="fas fa-chevron-down ml-1 text-gray-500 text-xs"></i>
                </div>
                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <div class="border-t border-gray-200"></div>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Content -->
<div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Users</p>
                                <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_users']) }}</p>
                                <p class="text-green-500 text-xs mt-1"><i class="fas fa-users mr-1"></i>Active: {{ number_format($stats['active_users']) }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Incidents</p>
                                <p class="text-2xl font-bold mt-1">{{ number_format($stats['system_incidents']) }}</p>
                                <p class="text-orange-500 text-xs mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>System-wide incidents</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-brick-orange"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Vehicles</p>
                                <p class="text-2xl font-bold mt-1">{{ number_format($stats['system_vehicles']) }}</p>
                                <p class="text-green-500 text-xs mt-1"><i class="fas fa-truck mr-1"></i>Emergency vehicles</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-truck text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Pending Requests</p>
                                <p class="text-2xl font-bold mt-1">{{ number_format($stats['pending_requests']) }}</p>
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-clock mr-1"></i>Requires attention</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-clock text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Municipality Performance Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Municipality Performance</h2>
                            <div class="flex space-x-2">
                                <button
                                    class="px-3 py-1 text-xs bg-orange-100 text-brick-orange rounded-md">Incidents</button>
                                <button class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md">Response Time</button>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>

                    <!-- Incident Severity Distribution -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Incident Severity Distribution</h2>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Municipality Performance Table -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Municipality Performance Overview</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipality</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Incidents</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Critical</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolved</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Response Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($municipalityPerformance as $municipality)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $municipality->municipality ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($municipality->total_incidents ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        {{ number_format($municipality->critical_incidents ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        {{ number_format($municipality->resolved_incidents ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($municipality->avg_response_time)
                                            {{ number_format($municipality->avg_response_time, 1) }} min
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No municipality data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar controls (if needed for specific dashboard behavior)
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const usersDropdown = document.getElementById('users-dropdown');
    
    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        const dropdownToggle = dropdown.querySelector('.dropdown');
        
        if (dropdownToggle && dropdownMenu) {
            dropdownToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('hidden');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
    
    // Initialize charts
    // Municipality Performance Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    const municipalityData = @json($municipalityPerformance);
    
    const municipalityLabels = municipalityData.map(item => item.municipality || 'Unknown');
    const incidentCounts = municipalityData.map(item => item.total_incidents || 0);
    
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: municipalityLabels,
            datasets: [{
                label: 'Total Incidents',
                data: incidentCounts,
                backgroundColor: '#c14a09',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Incident Severity Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const severityData = @json($chartData['severity'] ?? []);
    
    const severityLabels = severityData.map(item => {
        const level = item.severity_level || 'unknown';
        return level.charAt(0).toUpperCase() + level.slice(1);
    });
    const severityCounts = severityData.map(item => item.count || 0);
    
    const pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: severityLabels.length > 0 ? severityLabels : ['No Data'],
            datasets: [{
                data: severityCounts.length > 0 ? severityCounts : [1],
                backgroundColor: severityCounts.length > 0 ? [
                    '#dc2626', // critical - red
                    '#ea580c', // high - orange
                    '#d97706', // medium - amber
                    '#65a30d', // low - lime
                    '#6b7280'  // unknown - gray
                ] : ['#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (severityCounts.length === 0) return 'No data available';
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${percentage}% (${value})`;
                        }
                    }
                },
                datalabels: {
                    formatter: (value, ctx) => {
                        if (severityCounts.length === 0) return '';
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = (value * 100 / total).toFixed(0) + "%";
                        return percentage;
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            },
            cutout: '70%'
        },
        plugins: [ChartDataLabels]
    });
});
</script>
@endpush
