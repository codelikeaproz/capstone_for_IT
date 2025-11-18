<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incidentTypes = [
            [
                'code' => 'traffic_accident',
                'name' => 'Traffic Accident',
                'description' => 'Vehicle collisions, road accidents, and traffic-related incidents',
                'default_severity' => 'high',
                'icon' => 'fas fa-car-crash',
                'color' => 'orange',
                'requires_vehicle' => true,
                'requires_medical_response' => true,
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'medical_emergency',
                'name' => 'Medical Emergency',
                'description' => 'Health emergencies requiring immediate medical attention',
                'default_severity' => 'critical',
                'icon' => 'fas fa-heartbeat',
                'color' => 'red',
                'requires_vehicle' => true,
                'requires_medical_response' => true,
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'fire_incident',
                'name' => 'Fire Incident',
                'description' => 'Fires in buildings, vehicles, or outdoor areas',
                'default_severity' => 'critical',
                'icon' => 'fas fa-fire',
                'color' => 'red',
                'requires_vehicle' => true,
                'requires_medical_response' => false,
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'natural_disaster',
                'name' => 'Natural Disaster',
                'description' => 'Earthquakes, floods, landslides, typhoons, and other natural calamities',
                'default_severity' => 'critical',
                'icon' => 'fas fa-house-tsunami',
                'color' => 'purple',
                'requires_vehicle' => true,
                'requires_medical_response' => true,
                'priority_level' => 1,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'criminal_activity',
                'name' => 'Criminal Activity',
                'description' => 'Criminal incidents requiring emergency response and police coordination',
                'default_severity' => 'high',
                'icon' => 'fas fa-user-secret',
                'color' => 'indigo',
                'requires_vehicle' => false,
                'requires_medical_response' => false,
                'priority_level' => 2,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'other',
                'name' => 'Other Emergency',
                'description' => 'General emergencies that do not fit into other categories',
                'default_severity' => 'medium',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'gray',
                'requires_vehicle' => false,
                'requires_medical_response' => false,
                'priority_level' => 3,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($incidentTypes as $type) {
            DB::table('incident_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
