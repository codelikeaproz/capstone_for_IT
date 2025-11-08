{{-- Assignment and Review Section --}}
@if(Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
<div class="mb-10">
    <h2 class="text-lg font-semibold text-base-content mb-1">Response Assignment</h2>
    <p class="text-sm text-base-content/60 mb-6">Assign staff and resources to this incident</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Assign Staff Member</span>
                <span class="label-text-alt text-base-content/60">Optional</span>
            </label>
            <select name="assigned_staff_id" class="select select-bordered w-full focus:outline-primary">
                <option value="">Select staff member</option>
                @foreach($staff as $member)
                    <option value="{{ $member->id }}" {{ old('assigned_staff_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->first_name }} {{ $member->last_name }} ({{ $member->municipality }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text font-medium">Assign Vehicle</span>
                <span class="label-text-alt text-base-content/60">Optional</span>
            </label>
            <select name="assigned_vehicle_id" class="select select-bordered w-full focus:outline-primary">
                <option value="">Select vehicle</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('assigned_vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_number }} - {{ ucwords(str_replace('_', ' ', $vehicle->vehicle_type)) }} ({{ $vehicle->municipality }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endif

