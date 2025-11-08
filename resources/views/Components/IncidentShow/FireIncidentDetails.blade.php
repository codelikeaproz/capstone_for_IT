{{-- Fire Incident Specific Details --}}
@if($incident->incident_type === 'fire')
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-fire text-orange-500"></i>
                Fire Incident Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($incident->building_type)
                    <div>
                        <label class="label font-semibold">Building Type</label>
                        <div class="badge badge-lg badge-warning">{{ ucfirst($incident->building_type) }}</div>
                    </div>
                @endif

                @if($incident->fire_spread_level)
                    <div>
                        <label class="label font-semibold">Fire Spread Level</label>
                        <div class="badge badge-lg {{
                            $incident->fire_spread_level === 'widespread' ? 'badge-error' :
                            ($incident->fire_spread_level === 'spreading' ? 'badge-warning' : 'badge-info')
                        }}">
                            {{ ucfirst($incident->fire_spread_level) }}
                        </div>
                    </div>
                @endif

                @if($incident->buildings_affected)
                    <div>
                        <label class="label font-semibold">Buildings Affected</label>
                        <div class="text-lg font-bold text-orange-600">{{ $incident->buildings_affected }} Building(s)</div>
                    </div>
                @endif

                @if($incident->evacuation_required)
                    <div>
                        <label class="label font-semibold">Evacuation Status</label>
                        <div class="badge badge-lg badge-error">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Evacuation Required
                        </div>
                    </div>
                @endif

                @if($incident->evacuated_count)
                    <div>
                        <label class="label font-semibold">People Evacuated</label>
                        <div class="text-lg font-bold text-blue-600">{{ $incident->evacuated_count }} Person(s)</div>
                    </div>
                @endif

                @if($incident->fire_cause)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Suspected Fire Cause</label>
                        <div class="bg-orange-50 p-4 rounded border border-orange-200">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->fire_cause }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Critical Fire Alert --}}
            @if($incident->fire_spread_level === 'widespread' || $incident->buildings_affected > 3)
                <div class="alert alert-error mt-4">
                    <i class="fas fa-fire"></i>
                    <span class="font-semibold">Major Fire Incident - Request Additional Fire Units</span>
                </div>
            @endif
        </div>
    </div>
@endif

