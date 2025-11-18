<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicalStatuses = [
            [
                'code' => 'uninjured',
                'name' => 'Uninjured',
                'description' => 'No visible injuries or medical complaints',
                'severity_level' => 1,
                'requires_hospitalization' => false,
                'requires_ambulance' => false,
                'requires_immediate_care' => false,
                'color' => 'green',
                'badge_class' => 'badge-success',
                'icon' => 'fas fa-check-circle',
                'is_fatality' => false,
                'counts_as_injury' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'minor_injury',
                'name' => 'Minor Injury',
                'description' => 'Minor injuries requiring first aid but not hospitalization',
                'severity_level' => 2,
                'requires_hospitalization' => false,
                'requires_ambulance' => false,
                'requires_immediate_care' => false,
                'color' => 'yellow',
                'badge_class' => 'badge-warning',
                'icon' => 'fas fa-band-aid',
                'is_fatality' => false,
                'counts_as_injury' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'major_injury',
                'name' => 'Major Injury',
                'description' => 'Serious injuries requiring medical treatment and possible hospitalization',
                'severity_level' => 3,
                'requires_hospitalization' => true,
                'requires_ambulance' => true,
                'requires_immediate_care' => true,
                'color' => 'orange',
                'badge_class' => 'badge-error',
                'icon' => 'fas fa-user-injured',
                'is_fatality' => false,
                'counts_as_injury' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'critical',
                'name' => 'Critical',
                'description' => 'Life-threatening condition requiring immediate medical intervention',
                'severity_level' => 4,
                'requires_hospitalization' => true,
                'requires_ambulance' => true,
                'requires_immediate_care' => true,
                'color' => 'red',
                'badge_class' => 'badge-error',
                'icon' => 'fas fa-heartbeat',
                'is_fatality' => false,
                'counts_as_injury' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'deceased',
                'name' => 'Deceased',
                'description' => 'Fatality - deceased at scene or during transport',
                'severity_level' => 5,
                'requires_hospitalization' => false,
                'requires_ambulance' => false,
                'requires_immediate_care' => false,
                'color' => 'gray',
                'badge_class' => 'badge-neutral',
                'icon' => 'fas fa-cross',
                'is_fatality' => true,
                'counts_as_injury' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($medicalStatuses as $status) {
            DB::table('medical_statuses')->insert(array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
