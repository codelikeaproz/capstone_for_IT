{{-- Natural Disaster Specific Details --}}
@if($incident->incident_type === 'natural_disaster')
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <i class="fas fa-cloud-showers-heavy text-blue-500"></i>
                Natural Disaster Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($incident->disaster_type)
                    <div>
                        <label class="label font-semibold">Disaster Type</label>
                        <div class="badge badge-lg badge-error">{{ ucfirst($incident->disaster_type) }}</div>
                    </div>
                @endif

                @if($incident->affected_area_size)
                    <div>
                        <label class="label font-semibold">Affected Area Size</label>
                        <div class="text-lg font-bold text-red-600">{{ number_format($incident->affected_area_size, 2) }} km²</div>
                    </div>
                @endif

                @if($incident->families_affected)
                    <div>
                        <label class="label font-semibold">Families Affected</label>
                        <div class="text-lg font-bold text-orange-600">{{ number_format($incident->families_affected) }} Families</div>
                    </div>
                @endif

                @if($incident->structures_damaged)
                    <div>
                        <label class="label font-semibold">Structures Damaged</label>
                        <div class="text-lg font-bold text-red-600">{{ number_format($incident->structures_damaged) }} Structure(s)</div>
                    </div>
                @endif

                @if($incident->shelter_needed)
                    <div>
                        <label class="label font-semibold">Shelter Status</label>
                        <div class="badge badge-lg badge-warning">
                            <i class="fas fa-home mr-1"></i>
                            Shelter Required
                        </div>
                    </div>
                @endif

                @if($incident->infrastructure_damage)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Infrastructure Damage</label>
                        <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->infrastructure_damage }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Disaster Stats Summary --}}
            @if($incident->families_affected || $incident->structures_damaged)
                <div class="stats stats-vertical lg:stats-horizontal shadow mt-4 w-full">
                    @if($incident->families_affected)
                        <div class="stat">
                            <div class="stat-figure text-error">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div class="stat-title">Families Affected</div>
                            <div class="stat-value text-error">{{ number_format($incident->families_affected) }}</div>
                        </div>
                    @endif

                    @if($incident->structures_damaged)
                        <div class="stat">
                            <div class="stat-figure text-warning">
                                <i class="fas fa-building text-2xl"></i>
                            </div>
                            <div class="stat-title">Structures Damaged</div>
                            <div class="stat-value text-warning">{{ number_format($incident->structures_damaged) }}</div>
                        </div>
                    @endif

                    @if($incident->affected_area_size)
                        <div class="stat">
                            <div class="stat-figure text-info">
                                <i class="fas fa-map text-2xl"></i>
                            </div>
                            <div class="stat-title">Affected Area</div>
                            <div class="stat-value text-info text-2xl">{{ number_format($incident->affected_area_size, 1) }} km²</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif


