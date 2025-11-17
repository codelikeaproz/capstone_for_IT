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

                @if($incident->disaster_description)
                    <div class="md:col-span-2">
                        <label class="label font-semibold">Disaster Description</label>
                        <div class="bg-blue-50 p-4 rounded border border-blue-200">
                            <pre class="text-sm whitespace-pre-wrap">{{ $incident->disaster_description }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
