<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeverityLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $severityLevels = [
            [
                'code' => 'critical',
                'name' => 'Critical',
                'description' => 'Life-threatening situations requiring immediate response',
                'priority_level' => 1,
                'response_time_minutes' => 5,
                'color' => 'red',
                'badge_class' => 'badge-error',
                'icon' => 'fas fa-circle-exclamation',
                'requires_immediate_notification' => true,
                'requires_supervisor_approval' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'high',
                'name' => 'High',
                'description' => 'Serious situations requiring urgent response',
                'priority_level' => 2,
                'response_time_minutes' => 15,
                'color' => 'orange',
                'badge_class' => 'badge-warning',
                'icon' => 'fas fa-triangle-exclamation',
                'requires_immediate_notification' => true,
                'requires_supervisor_approval' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'medium',
                'name' => 'Medium',
                'description' => 'Moderate situations requiring timely response',
                'priority_level' => 3,
                'response_time_minutes' => 30,
                'color' => 'yellow',
                'badge_class' => 'badge-warning',
                'icon' => 'fas fa-exclamation',
                'requires_immediate_notification' => false,
                'requires_supervisor_approval' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'low',
                'name' => 'Low',
                'description' => 'Minor situations requiring standard response',
                'priority_level' => 4,
                'response_time_minutes' => 60,
                'color' => 'green',
                'badge_class' => 'badge-success',
                'icon' => 'fas fa-circle-info',
                'requires_immediate_notification' => false,
                'requires_supervisor_approval' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($severityLevels as $level) {
            DB::table('severity_levels')->insert(array_merge($level, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
