{{-- Traffic Accident Specific Details --}}
@if($incident->incident_type === 'traffic_accident')
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-car-crash text-red-500"></i>
                Traffic Accident Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($incident->vehicle_count)
                    <div>
                        <label class="label font-semibold">Number of Vehicles Involved</label>
                        <div class="badge badge-lg badge-warning">{{ $incident->vehicle_count }} Vehicle(s)</div>
                    </div>
                @endif

                @if(is_array($incident->license_plates) && count($incident->license_plates) > 0)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">License Plates</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($incident->license_plates as $plate)
                                <span class="badge badge-outline badge-lg font-mono">{{ $plate }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($incident->driver_information)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Driver Information</label>
                        <div class="bg-gray-50 p-4 rounded border">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->driver_information }}</pre>
                        </div>
                    </div>
                @endif

                @if($incident->vehicle_details)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Vehicle Details</label>
                        <div class="bg-gray-50 p-4 rounded border">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->vehicle_details }}</pre>
                        </div>
                    </div>
                @endif

                @if($incident->road_condition)
                    <div>
                        <label class="label font-semibold">Road Condition</label>
                        <div class="badge badge-info badge-lg">{{ ucfirst(str_replace('_', ' ', $incident->road_condition)) }}</div>
                    </div>
                @endif

                @if($incident->weather_condition)
                    <div>
                        <label class="label font-semibold">Weather at Time of Incident</label>
                        <div class="badge badge-info badge-lg">{{ ucfirst($incident->weather_condition) }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

