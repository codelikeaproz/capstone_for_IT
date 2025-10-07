@extends('Layouts.app')

@section('title', 'Generated Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $reportData['title'] ?? 'Generated Report' }}</h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="btn btn-outline">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button class="btn btn-primary">
                <i class="fas fa-download mr-2"></i>Download PDF
            </button>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Report Period</p>
                <p class="font-semibold">{{ $reportData['period'] ?? 'N/A' }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Municipality</p>
                <p class="font-semibold">{{ $reportData['municipality'] ?? 'All Municipalities' }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Generated On</p>
                <p class="font-semibold">{{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
        
        @if(isset($reportData['summary']))
        <!-- Summary Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($reportData['summary'] as $key => $value)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        @if(isset($reportData['by_severity']))
        <!-- By Severity -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">By Severity</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Severity Level</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['by_severity'] as $severity => $count)
                        <tr>
                            <td class="capitalize">{{ $severity }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        @if(isset($reportData['by_type']))
        <!-- By Type -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">By Incident Type</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Incident Type</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['by_type'] as $type => $count)
                        <tr>
                            <td class="capitalize">{{ str_replace('_', ' ', $type) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        @if(isset($reportData['incidents']))
        <!-- Detailed Incidents -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detailed Incidents</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Incident Number</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Severity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['incidents'] as $incident)
                        <tr>
                            <td>{{ $incident->incident_number }}</td>
                            <td class="capitalize">{{ str_replace('_', ' ', $incident->incident_type) }}</td>
                            <td>{{ $incident->location }}</td>
                            <td>{{ $incident->incident_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge 
                                    @if($incident->severity_level === 'critical') badge-error 
                                    @elseif($incident->severity_level === 'high') badge-warning 
                                    @elseif($incident->severity_level === 'medium') badge-info 
                                    @else badge-success @endif">
                                    {{ ucfirst($incident->severity_level) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $incident->getStatusBadgeAttribute() }}">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        @if(isset($reportData['municipalities']))
        <!-- Municipality Comparison -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Municipality Comparison</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Municipality</th>
                            <th>Total Incidents</th>
                            <th>Critical Incidents</th>
                            <th>Resolved Incidents</th>
                            <th>Avg Response Time (min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['municipalities'] as $municipality)
                        <tr>
                            <td class="font-medium">{{ $municipality->municipality }}</td>
                            <td>{{ $municipality->total_incidents }}</td>
                            <td><span class="badge badge-error">{{ $municipality->critical_incidents }}</span></td>
                            <td><span class="badge badge-success">{{ $municipality->resolved_incidents }}</span></td>
                            <td>{{ $municipality->avg_response_time ? round($municipality->avg_response_time, 1) : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <div class="text-center">
        <a href="{{ route('reports.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Reports
        </a>
    </div>
</div>
@endsection