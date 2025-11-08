<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $vehicle = $this->route('vehicle');

        // Admin can update any vehicle
        if ($user->role === 'admin') {
            return true;
        }

        // Staff can only update vehicles in their municipality
        return $user->role === 'staff' && $user->municipality === $vehicle->municipality;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicle = $this->route('vehicle');

        return [
            'vehicle_number' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'vehicle_number')->ignore($vehicle->id)],
            'license_plate' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'license_plate')->ignore($vehicle->id)],
            'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,support_vehicle,traviz,pick_up',
            'status' => 'required|in:available,in_use,maintenance,out_of_service',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'fuel_capacity' => 'required|numeric|min:1|max:500',
            'current_fuel_level' => 'required|numeric|min:0|max:100',
            'fuel_consumption_rate' => 'nullable|numeric|min:0',
            'odometer_reading' => 'required|integer|min:0',
            'municipality' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|array',
            'equipment_list.*' => 'string|max:255',
            'insurance_policy' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
            'registration_expiry' => 'nullable|date',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'next_maintenance_due' => 'nullable|date',
            'maintenance_notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'vehicle_number' => 'vehicle number',
            'license_plate' => 'license plate',
            'vehicle_type' => 'vehicle type',
            'current_fuel_level' => 'current fuel level',
            'fuel_capacity' => 'fuel capacity',
            'fuel_consumption_rate' => 'fuel consumption rate',
            'odometer_reading' => 'odometer reading',
            'assigned_driver_id' => 'assigned driver',
            'equipment_list' => 'equipment list',
            'insurance_expiry' => 'insurance expiry date',
            'registration_expiry' => 'registration expiry date',
            'last_maintenance_date' => 'last maintenance date',
            'next_maintenance_due' => 'next maintenance due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_number.unique' => 'This vehicle number is already registered in the system.',
            'license_plate.unique' => 'This license plate is already registered in the system.',
            'current_fuel_level.max' => 'Fuel level cannot exceed 100%.',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
        ];
    }
}
