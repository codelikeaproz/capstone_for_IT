<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number',
            'license_plate' => 'required|string|max:50|unique:vehicles,license_plate',
            'vehicle_type' => 'required|in:ambulance,fire_truck,rescue_vehicle,patrol_car,support_vehicle,traviz,pick_up',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'fuel_capacity' => 'required|numeric|min:1|max:500',
            'fuel_consumption_rate' => 'nullable|numeric|min:0',
            'municipality' => 'required|string|max:255',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'equipment_list' => 'nullable|array',
            'equipment_list.*' => 'string|max:255',
            'insurance_policy' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date|after:today',
            'registration_expiry' => 'nullable|date|after:today',
            'next_maintenance_due' => 'nullable|date|after:today',
            'maintenance_notes' => 'nullable|string|max:1000',
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
            'fuel_capacity' => 'fuel capacity',
            'fuel_consumption_rate' => 'fuel consumption rate',
            'assigned_driver_id' => 'assigned driver',
            'equipment_list' => 'equipment list',
            'insurance_expiry' => 'insurance expiry date',
            'registration_expiry' => 'registration expiry date',
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
            'insurance_expiry.after' => 'Insurance expiry date must be in the future.',
            'registration_expiry.after' => 'Registration expiry date must be in the future.',
        ];
    }
}
