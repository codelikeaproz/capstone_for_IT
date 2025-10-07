@extends('Layouts.app')

@section('title', 'Reports')

@push('styles')
<style>
    .report-card {
        transition: transform 0.2s;
    }
    .report-card:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reports</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Report Generation Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Generate Report</h2>
                
                <form action="{{ route('reports.generate') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Report Type -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Report Type</span>
                            </label>
                            <select name="report_type" class="select select-bordered w-full" required>
                                <option value="">Select Report Type</option>
                                <option value="incident_summary">Incident Summary Report</option>
                                <option value="incident_detailed">Detailed Incident Report</option>
                                <option value="vehicle_usage">Vehicle Usage Report</option>
                                <option value="request_analysis">Request Analysis Report</option>
                                <option value="victim_statistics">Victim Statistics Report</option>
                                <option value="municipality_comparison">Municipality Comparison Report</option>
                            </select>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Date Range</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" name="date_from" class="input input-bordered w-full" required>
                                <input type="date" name="date_to" class="input input-bordered w-full" required>
                            </div>
                        </div>
                        
                        <!-- Municipality Filter -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Municipality</span>
                            </label>
                            <select name="municipality" class="select select-bordered w-full">
                                <option value="">All Municipalities</option>
                                @if(isset($municipalities))
                                    @foreach($municipalities as $municipality)
                                        <option value="{{ $municipality }}">{{ $municipality }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <!-- Format -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Format</span>
                            </label>
                            <select name="format" class="select select-bordered w-full" required>
                                <option value="html">HTML</option>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-file-alt mr-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Report Types Information -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Report Types</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-chart-bar text-blue-500 mt-1 mr-2"></i>
                        <span><strong>Incident Summary:</strong> Overview of incidents with key metrics</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-list-alt text-green-500 mt-1 mr-2"></i>
                        <span><strong>Detailed Incidents:</strong> Complete incident details</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-truck text-yellow-500 mt-1 mr-2"></i>
                        <span><strong>Vehicle Usage:</strong> Vehicle deployment statistics</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clipboard-list text-purple-500 mt-1 mr-2"></i>
                        <span><strong>Request Analysis:</strong> Citizen request trends</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-user-injured text-red-500 mt-1 mr-2"></i>
                        <span><strong>Victim Statistics:</strong> Victim demographics and injuries</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt text-orange-500 mt-1 mr-2"></i>
                        <span><strong>Municipality Comparison:</strong> Cross-municipality analysis</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Export Options</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-file-code text-blue-500 mr-2"></i>
                        <span><strong>HTML:</strong> View in browser</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                        <span><strong>PDF:</strong> Printable document</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-file-excel text-green-500 mr-2"></i>
                        <span><strong>Excel:</strong> Spreadsheet format</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection