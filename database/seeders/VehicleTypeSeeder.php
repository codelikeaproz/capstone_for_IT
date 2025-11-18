<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                'code' => 'ambulance',
                'name' => 'Ambulance',
                'description' => 'Emergency medical transport vehicle equipped with medical equipment',
                'icon' => 'fas fa-ambulance',
                'color' => 'red',
                'typical_equipment' => json_encode([
                    'Stretcher',
                    'First Aid Kit',
                    'Oxygen Tank',
                    'Defibrillator',
                    'IV Equipment',
                    'Splints and Braces',
                    'Emergency Medications',
                ]),
                'typical_capacity' => 2,
                'typical_fuel_capacity' => 60.00,
                'response_types' => json_encode([
                    'medical_emergency',
                    'traffic_accident',
                    'natural_disaster',
                ]),
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'fire_truck',
                'name' => 'Fire Truck',
                'description' => 'Fire suppression vehicle with water pump and firefighting equipment',
                'icon' => 'fas fa-fire-extinguisher',
                'color' => 'red',
                'typical_equipment' => json_encode([
                    'Fire Hose',
                    'Water Pump',
                    'Ladder',
                    'Fire Extinguishers',
                    'Axes and Tools',
                    'Breathing Apparatus',
                    'Protective Gear',
                ]),
                'typical_capacity' => 6,
                'typical_fuel_capacity' => 150.00,
                'response_types' => json_encode([
                    'fire_incident',
                    'natural_disaster',
                ]),
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'rescue_vehicle',
                'name' => 'Rescue Vehicle',
                'description' => 'Specialized vehicle for rescue operations and emergency response',
                'icon' => 'fas fa-truck-pickup',
                'color' => 'orange',
                'typical_equipment' => json_encode([
                    'Rescue Tools',
                    'Ropes and Harnesses',
                    'Cutting Equipment',
                    'First Aid Kit',
                    'Communication Equipment',
                    'Emergency Lights',
                ]),
                'typical_capacity' => 4,
                'typical_fuel_capacity' => 80.00,
                'response_types' => json_encode([
                    'traffic_accident',
                    'natural_disaster',
                    'other',
                ]),
                'priority_level' => 2,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'patrol_car',
                'name' => 'Patrol Car',
                'description' => 'Police patrol vehicle for security and emergency coordination',
                'icon' => 'fas fa-car',
                'color' => 'blue',
                'typical_equipment' => json_encode([
                    'Radio Communication',
                    'First Aid Kit',
                    'Traffic Cones',
                    'Emergency Lights',
                    'Safety Vests',
                ]),
                'typical_capacity' => 4,
                'typical_fuel_capacity' => 50.00,
                'response_types' => json_encode([
                    'criminal_activity',
                    'traffic_accident',
                    'other',
                ]),
                'priority_level' => 3,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'support_vehicle',
                'name' => 'Support Vehicle',
                'description' => 'General support and logistics vehicle for emergency operations',
                'icon' => 'fas fa-truck',
                'color' => 'gray',
                'typical_equipment' => json_encode([
                    'Basic Tools',
                    'Communication Equipment',
                    'Supplies Transport',
                    'Generator',
                ]),
                'typical_capacity' => 6,
                'typical_fuel_capacity' => 70.00,
                'response_types' => json_encode([
                    'other',
                    'natural_disaster',
                ]),
                'priority_level' => 4,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'traviz',
                'name' => 'TRAVIZ',
                'description' => 'Multi-purpose transport vehicle for disaster response',
                'icon' => 'fas fa-van-shuttle',
                'color' => 'indigo',
                'typical_equipment' => json_encode([
                    'Passenger Seats',
                    'Emergency Supplies',
                    'Communication Equipment',
                    'First Aid Kit',
                ]),
                'typical_capacity' => 12,
                'typical_fuel_capacity' => 65.00,
                'response_types' => json_encode([
                    'natural_disaster',
                    'medical_emergency',
                ]),
                'priority_level' => 3,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'pick_up',
                'name' => 'Pick-Up',
                'description' => 'Pick-up truck for transport and logistics support',
                'icon' => 'fas fa-truck-pickup',
                'color' => 'teal',
                'typical_equipment' => json_encode([
                    'Cargo Bed',
                    'Basic Tools',
                    'Towing Equipment',
                    'Emergency Supplies',
                ]),
                'typical_capacity' => 3,
                'typical_fuel_capacity' => 55.00,
                'response_types' => json_encode([
                    'other',
                    'natural_disaster',
                ]),
                'priority_level' => 4,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($vehicleTypes as $type) {
            DB::table('vehicle_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
