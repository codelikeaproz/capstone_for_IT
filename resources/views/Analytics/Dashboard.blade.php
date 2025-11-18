@extends('Layouts.app')

@section('title', 'Advanced Analytics Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Advanced Analytics</h1>
        <p class="text-gray-600 mt-1">Deep-dive incident analysis and trend visualization</p>
    </div>

    <!-- Advanced Filter Panel -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filter Analytics
        </h2>

        <form method="GET" action="{{ route('analytics.dashboard') }}" id="analyticsFilterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Date Range -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Date Range</span>
                    </label>
                    <select name="date_range" class="select select-bordered select-primary w-full">
                        <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 days</option>
                        <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>

                <!-- Incident Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Incident Type</span>
                    </label>
                    <select name="incident_type" class="select select-bordered select-primary w-full">
                        <option value="">All Types</option>
                        @foreach($incidentTypes as $type)
                            <option value="{{ $type }}" {{ $incidentType == $type ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucwords($type, '_')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Severity Level -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Severity Level</span>
                    </label>
                    <select name="severity" class="select select-bordered select-primary w-full">
                        <option value="">All Levels</option>
                        @foreach($severityLevels as $level)
                            <option value="{{ $level }}" {{ $severityLevel == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(Auth::user()->role === 'admin')
                <!-- Municipality -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Municipality</span>
                    </label>
                    <select name="municipality" class="select select-bordered select-primary w-full">
                        <option value="">All Municipalities</option>
                        @foreach($municipalities as $muni)
                            <option value="{{ $muni }}" {{ $municipality == $muni ? 'selected' : '' }}>
                                {{ $muni }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Apply Filters Button -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium opacity-0">Action</span>
                    </label>
                    <button type="submit" class="btn btn-primary w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if($incidentType || $severityLevel || $municipality)
            <div class="mt-4 flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700">Active Filters:</span>
                @if($incidentType)
                    <span class="badge badge-primary">Type: {{ str_replace('_', ' ', ucwords($incidentType, '_')) }}</span>
                @endif
                @if($severityLevel)
                    <span class="badge badge-secondary">Severity: {{ ucfirst($severityLevel) }}</span>
                @endif
                @if($municipality)
                    <span class="badge badge-accent">Municipality: {{ $municipality }}</span>
                @endif
                <a href="{{ route('analytics.dashboard') }}" class="badge badge-outline badge-error">Clear All</a>
            </div>
            @endif
        </form>
    </div>

    <!-- Month-over-Month Comparison Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Incidents</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $monthComparison['current']->total }}</p>
                    <p class="text-sm mt-1">
                        <span class="font-semibold {{ $monthComparison['changes']['total'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $monthComparison['changes']['total'] >= 0 ? '↑' : '↓' }}
                            {{ abs($monthComparison['changes']['total']) }}%
                        </span>
                        <span class="text-gray-500">vs last month</span>
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Critical Incidents</p>
                    <p class="text-3xl font-bold text-red-600">{{ $monthComparison['current']->critical }}</p>
                    <p class="text-sm mt-1">
                        <span class="font-semibold {{ $monthComparison['changes']['critical'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $monthComparison['changes']['critical'] >= 0 ? '↑' : '↓' }}
                            {{ abs($monthComparison['changes']['critical']) }}%
                        </span>
                        <span class="text-gray-500">vs last month</span>
                    </p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Resolved Incidents</p>
                    <p class="text-3xl font-bold text-green-600">{{ $monthComparison['current']->resolved }}</p>
                    <p class="text-sm mt-1">
                        <span class="font-semibold {{ $monthComparison['changes']['resolved'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $monthComparison['changes']['resolved'] >= 0 ? '↑' : '↓' }}
                            {{ abs($monthComparison['changes']['resolved']) }}%
                        </span>
                        <span class="text-gray-500">vs last month</span>
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Incident Trends Line Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Incident Trends Over Time</h2>
            <canvas id="incidentTrendChart" height="250"></canvas>
        </div>

        <!-- Severity Distribution Doughnut Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Severity Distribution</h2>
            <canvas id="severityChart" height="250"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Incident Type Breakdown Bar Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Incidents by Type</h2>
            <canvas id="typeChart" height="250"></canvas>
        </div>

        <!-- Response Time Analysis -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Average Response Time Trend</h2>
            <canvas id="responseTimeChart" height="250"></canvas>
        </div>
    </div>

    <!-- Time-Based Heatmap -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Peak Incident Times (Hour x Day of Week)</h2>
        <div class="overflow-x-auto">
            <table class="table table-xs w-full">
                <thead>
                    <tr>
                        <th class="bg-base-200">Hour</th>
                        <th class="bg-base-200">Sun</th>
                        <th class="bg-base-200">Mon</th>
                        <th class="bg-base-200">Tue</th>
                        <th class="bg-base-200">Wed</th>
                        <th class="bg-base-200">Thu</th>
                        <th class="bg-base-200">Fri</th>
                        <th class="bg-base-200">Sat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeHeatmap as $hour => $days)
                    <tr>
                        <td class="font-semibold">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                        @foreach([1,2,3,4,5,6,7] as $day)
                            @php
                                $count = $days[$day] ?? 0;
                                $intensity = $count > 0 ? min(($count / 5) * 100, 100) : 0;
                                $bgColor = $count == 0 ? 'bg-gray-100' :
                                          ($intensity >= 75 ? 'bg-red-500 text-white' :
                                          ($intensity >= 50 ? 'bg-orange-400 text-white' :
                                          ($intensity >= 25 ? 'bg-yellow-300' : 'bg-green-200')));
                            @endphp
                            <td class="text-center {{ $bgColor }}">
                                {{ $count > 0 ? $count : '-' }}
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-sm text-gray-600 mt-2">
            <span class="inline-block w-4 h-4 bg-red-500 mr-1"></span> High (5+ incidents)
            <span class="inline-block w-4 h-4 bg-orange-400 ml-3 mr-1"></span> Medium (3-4)
            <span class="inline-block w-4 h-4 bg-yellow-300 ml-3 mr-1"></span> Low (1-2)
            <span class="inline-block w-4 h-4 bg-gray-100 ml-3 mr-1 border"></span> None
        </p>
    </div>

    <!-- Response Performance Metrics -->
    @if(Auth::user()->role === 'admin')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Average Response Time by Municipality -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Response Time by Municipality</h2>
            <canvas id="responsePerformanceChart" height="300"></canvas>
        </div>

        <!-- Resolution Rate by Municipality -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Resolution Rate by Municipality</h2>
            <canvas id="resolutionRateChart" height="300"></canvas>
        </div>
    </div>
    @endif

    <!-- Municipality Comparison Table (Admin Only) -->
    @if(Auth::user()->role === 'admin' && $municipalityStats)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Detailed Municipality Comparison</h2>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="bg-primary text-white">Municipality</th>
                        <th class="bg-primary text-white text-center">Total Incidents</th>
                        <th class="bg-primary text-white text-center">Critical</th>
                        <th class="bg-primary text-white text-center">Resolved</th>
                        <th class="bg-primary text-white text-center">Avg Response Time</th>
                        <th class="bg-primary text-white text-center">Resolution Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($municipalityStats as $stat)
                    <tr class="hover">
                        <td class="font-semibold">{{ $stat->municipality }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ $stat->total_incidents }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-error">{{ $stat->critical_incidents }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $stat->resolved_incidents }}</span>
                        </td>
                        <td class="text-center">
                            {{ $stat->avg_response_time ? round($stat->avg_response_time, 1) . ' min' : 'N/A' }}
                        </td>
                        <td class="text-center">
                            @php
                                $resolutionRate = $stat->total_incidents > 0 ?
                                    round(($stat->resolved_incidents / $stat->total_incidents) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <progress class="progress progress-success w-20" value="{{ $resolutionRate }}" max="100"></progress>
                                <span class="text-sm font-semibold">{{ $resolutionRate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Chart Data from Laravel
    const chartData = @json($chartData);
    const responseMetrics = @json($responseMetrics);

    // Chart Configuration
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                cornerRadius: 8,
            }
        }
    };

    // 1. Incident Trends Line Chart
    new Chart(document.getElementById('incidentTrendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.trends.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Incidents',
                data: chartData.trends.map(item => item.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            ...chartConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Severity Distribution Doughnut Chart
    new Chart(document.getElementById('severityChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: chartData.severity.map(item => item.severity_level.charAt(0).toUpperCase() + item.severity_level.slice(1)),
            datasets: [{
                data: chartData.severity.map(item => item.count),
                backgroundColor: [
                    'rgba(220, 38, 38, 0.8)',   // Critical - Red
                    'rgba(251, 146, 60, 0.8)',   // High - Orange
                    'rgba(250, 204, 21, 0.8)',   // Medium - Yellow
                    'rgba(34, 197, 94, 0.8)',    // Low - Green
                ],
                borderColor: ['rgb(220, 38, 38)', 'rgb(251, 146, 60)', 'rgb(250, 204, 21)', 'rgb(34, 197, 94)'],
                borderWidth: 2,
            }]
        },
        options: chartConfig
    });

    // 3. Incident Type Bar Chart
    new Chart(document.getElementById('typeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartData.types.map(item => {
                const type = item.incident_type.replace(/_/g, ' ');
                return type.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            }),
            datasets: [{
                label: 'Incidents',
                data: chartData.types.map(item => item.count),
                backgroundColor: 'rgba(168, 85, 247, 0.8)',
                borderColor: 'rgb(168, 85, 247)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            ...chartConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 4. Response Time Chart
    new Chart(document.getElementById('responseTimeChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.response_times.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Avg Response Time (minutes)',
                data: chartData.response_times.map(item => item.avg_response_time || 0),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            ...chartConfig,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Minutes' }
                }
            }
        }
    });

    // 5. Response Performance Chart (Admin Only)
    @if(Auth::user()->role === 'admin')
    new Chart(document.getElementById('responsePerformanceChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: responseMetrics.response_times.map(item => item.municipality),
            datasets: [{
                label: 'Avg Response Time (min)',
                data: responseMetrics.response_times.map(item => item.avg_response_time),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            ...chartConfig,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Minutes' }
                }
            }
        }
    });

    // 6. Resolution Rate Chart (Admin Only)
    new Chart(document.getElementById('resolutionRateChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: responseMetrics.resolution_rates.map(item => item.municipality),
            datasets: [{
                label: 'Resolution Rate (%)',
                data: responseMetrics.resolution_rates.map(item => item.resolution_rate),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            ...chartConfig,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Percentage (%)' }
                }
            }
        }
    });
    @endif
</script>
@endsection
