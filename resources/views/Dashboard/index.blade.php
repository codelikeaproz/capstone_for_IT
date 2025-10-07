<!DOCTYPE html>
@extends('Layouts.app')

@section('title', 'BukidnonAlert Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .bg-brick-orange { background-color: #c14a09; }
    .text-brick-orange { color: #c14a09; }
    .border-brick-orange { border-color: #c14a09; }
    .stat-card { transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .alert-pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>
@endpush

@section('content')
<!-- Dashboard Content -->
<div class="px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600">Emergency Management Overview</p>
            </div>
            <div class="flex space-x-4">
                <select id="dateRange" class="select select-bordered select-sm">
                    <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 days</option>
                </select>
                <button onclick="refreshDashboard()" class="btn btn-sm btn-outline">
                    <i class="fas fa-refresh"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Emergency Alerts -->
        @if(count($alerts) > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3 text-red-600">🚨 Emergency Alerts</h2>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($alerts as $alert)
                <div class="alert {{ $alert['type'] === 'critical' ? 'alert-error' : ($alert['type'] === 'warning' ? 'alert-warning' : 'alert-info') }} alert-pulse">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $alert['message'] }}</span>
                    <span class="badge badge-neutral">{{ $alert['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Core Statistics -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Incidents Stats -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Incidents</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['incidents']['total'] }}</p>
                        <p class="text-sm">
                            <span class="text-red-600">{{ $stats['incidents']['active'] }} Active</span> • 
                            <span class="text-orange-600">{{ $stats['incidents']['critical'] }} Critical</span>
                        </p>
                    </div>
                    <div class="text-blue-600">
                        <i class="fas fa-exclamation-triangle text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Vehicles Stats -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Vehicles</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['vehicles']['available'] }}/{{ $stats['vehicles']['total'] }}</p>
                        <p class="text-sm">
                            <span class="text-green-600">{{ $stats['vehicles']['available'] }} Available</span> • 
                            <span class="text-red-600">{{ $stats['vehicles']['in_use'] }} In Use</span>
                        </p>
                    </div>
                    <div class="text-green-600">
                        <i class="fas fa-truck text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Requests Stats -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Requests</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['requests']['total'] }}</p>
                        <p class="text-sm">
                            <span class="text-orange-600">{{ $stats['requests']['pending'] }} Pending</span> • 
                            <span class="text-green-600">{{ $stats['requests']['completed'] }} Completed</span>
                        </p>
                    </div>
                    <div class="text-purple-600">
                        <i class="fas fa-clipboard-list text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Victims Stats -->
            <div class="stat-card bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Victims</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['victims']['total'] }}</p>
                        <p class="text-sm">
                            <span class="text-yellow-600">{{ $stats['victims']['injured'] }} Injured</span> • 
                            <span class="text-red-600">{{ $stats['victims']['critical'] }} Critical</span>
                        </p>
                    </div>
                    <div class="text-red-600">
                        <i class="fas fa-user-injured text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-6 lg:grid-cols-2 mb-8">
            <!-- Incident Trends Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Incident Trends</h3>
                <canvas id="incidentTrendsChart" width="400" height="200"></canvas>
            </div>

            <!-- Severity Distribution -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Severity Distribution</h3>
                <canvas id="severityChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Recent Incidents -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Incidents</h3>
                <div class="space-y-3">
                    @foreach($recentIncidents->take(5) as $incident)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $incident->incident_number }}</p>
                            <p class="text-sm text-gray-600">{{ $incident->incident_type }} - {{ $incident->location }}</p>
                            <p class="text-xs text-gray-500">{{ $incident->incident_date->diffForHumans() }}</p>
                        </div>
                        <span class="badge 
                            {{ $incident->severity_level === 'critical' ? 'badge-error' : 
                               ($incident->severity_level === 'high' ? 'badge-warning' : 'badge-info') }}">
                            {{ ucfirst($incident->severity_level) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline">View All Incidents</a>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Requests</h3>
                <div class="space-y-3">
                    @foreach($recentRequests->take(5) as $request)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $request->request_number }}</p>
                            <p class="text-sm text-gray-600">{{ $request->request_type }}</p>
                            <p class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="badge 
                            {{ $request->status === 'pending' ? 'badge-warning' : 
                               ($request->status === 'approved' ? 'badge-success' : 'badge-info') }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline">View All Requests</a>
                </div>
            </div>
        </div>

        <!-- Municipality Comparison (Admin Only) -->
        @if($municipalityStats && auth()->user()->role === 'admin')
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Municipality Performance</h3>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Municipality</th>
                            <th>Total Incidents</th>
                            <th>Critical Incidents</th>
                            <th>Resolved</th>
                            <th>Avg Response Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($municipalityStats as $stat)
                        <tr>
                            <td class="font-medium">{{ $stat->municipality }}</td>
                            <td>{{ $stat->total_incidents }}</td>
                            <td><span class="badge badge-error">{{ $stat->critical_incidents }}</span></td>
                            <td><span class="badge badge-success">{{ $stat->resolved_incidents }}</span></td>
                            <td>{{ $stat->avg_response_time ? round($stat->avg_response_time, 1) . ' min' : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Chart data from controller
        const chartData = @json($chartData);
        
        // Incident Trends Chart
        const trendsCtx = document.getElementById('incidentTrendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: chartData.trends.map(item => new Date(item.date).toLocaleDateString()),
                datasets: [{
                    label: 'Incidents',
                    data: chartData.trends.map(item => item.count),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Severity Distribution Chart
        const severityCtx = document.getElementById('severityChart').getContext('2d');
        new Chart(severityCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.severity.map(item => item.severity_level.charAt(0).toUpperCase() + item.severity_level.slice(1)),
                datasets: [{
                    data: chartData.severity.map(item => item.count),
                    backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Dashboard refresh functionality
        function refreshDashboard() {
            const dateRange = document.getElementById('dateRange').value;
            const url = new URL(window.location.href);
            url.searchParams.set('date_range', dateRange);
            window.location.href = url.toString();
        }

        // Auto-refresh every 5 minutes
        setInterval(() => {
            fetch('{{ route("api.dashboard.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    // Update statistics without full page reload
                    console.log('Dashboard data refreshed', data);
                })
                .catch(error => console.error('Error refreshing dashboard:', error));
        }, 300000); // 5 minutes
    });
</script>
@endpush