<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\Victim;
use Illuminate\Support\Facades\Hash;

class BukidnonAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'jumaoasralph2003@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'municipality' => 'Maramag',
            'phone_number' => '+63 88 813 5772',
            'address' => 'Market, Maramag, Bukidnon',
            'is_active' => true,
        ]);

        // Create Staff Users for different municipalities
        $municipalities = [
            'Valencia City' => [
                ['first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'maria.santos@valencia.gov.ph'],
                ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'email' => 'juan.delacruz@valencia.gov.ph'],
            ],
            'Malaybalay City' => [
                ['first_name' => 'Ana', 'last_name' => 'Rodriguez', 'email' => 'ana.rodriguez@malaybalay.gov.ph'],
                ['first_name' => 'Carlos', 'last_name' => 'Garcia', 'email' => 'carlos.garcia@malaybalay.gov.ph'],
            ],
            'Don Carlos' => [
                ['first_name' => 'Elena', 'last_name' => 'Reyes', 'email' => 'elena.reyes@doncarlos.gov.ph'],
            ],
            'Quezon' => [
                ['first_name' => 'Roberto', 'last_name' => 'Mendoza', 'email' => 'roberto.mendoza@quezon.gov.ph'],
            ],
            'Manolo Fortich' => [
                ['first_name' => 'Sofia', 'last_name' => 'Cruz', 'email' => 'sofia.cruz@manolofortich.gov.ph'],
            ],
            'Maramag' => [
                ['first_name' => 'Ralph', 'last_name' => 'Jumaoas', 'email' => 'jumaoasralph77@gmail.com'],
            ],
        ];

        $staffUsers = [];
        foreach ($municipalities as $municipality => $users) {
            foreach ($users as $userData) {
                $staff = User::create([
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'staff',
                    'municipality' => $municipality,
                    'phone_number' => '+63 9' . rand(100000000, 999999999),
                    'address' => $municipality . ', Bukidnon',
                    'is_active' => true,
                ]);
                $staffUsers[] = $staff;
            }
        }

        // Create Responder Users
        $responders = [];
        foreach ($municipalities as $municipality => $users) {
            for ($i = 1; $i <= 3; $i++) {
                $responder = User::create([
                    'first_name' => 'Responder',
                    'last_name' => $municipality . ' ' . $i,
                    'email' => 'responder' . $i . '@' . strtolower(str_replace(' ', '', $municipality)) . '.gov.ph',
                    'password' => Hash::make('responder123'),
                    'role' => 'responder',
                    'municipality' => $municipality,
                    'phone_number' => '+63 9' . rand(100000000, 999999999),
                    'address' => $municipality . ', Bukidnon',
                    'is_active' => true,
                ]);
                $responders[] = $responder;
            }
        }

        // Create Vehicles
        $vehicleTypes = ['ambulance', 'fire_truck', 'rescue_vehicle', 'patrol_car', 'support_vehicle'];
        $makes = ['Toyota', 'Isuzu', 'Mitsubishi', 'Ford', 'Nissan'];
        $vehicles = [];

        foreach ($municipalities as $municipality => $users) {
            foreach ($vehicleTypes as $index => $type) {
                $vehicle = Vehicle::create([
                    'vehicle_number' => 'BNL-' . str_pad(count($vehicles) + 1, 3, '0', STR_PAD_LEFT),
                    'license_plate' => 'ABC ' . rand(1000, 9999),
                    'vehicle_type' => $type,
                    'status' => collect(['available', 'available', 'available', 'in_use', 'maintenance'])->random(),
                    'make' => $makes[array_rand($makes)],
                    'model' => collect(['Hiace', 'Elf', 'Ranger', 'Navara', 'L300'])->random(),
                    'year' => rand(2015, 2023),
                    'color' => collect(['White', 'Red', 'Blue', 'Yellow', 'Orange'])->random(),
                    'fuel_capacity' => rand(40, 80),
                    'current_fuel_level' => rand(10, 100),
                    'fuel_consumption_rate' => rand(8, 15),
                    'odometer_reading' => rand(10000, 150000),
                    'total_distance' => rand(50000, 200000),
                    'municipality' => $municipality,
                    'assigned_driver_id' => $responders[array_rand($responders)]->id,
                    'last_maintenance_date' => now()->subDays(rand(1, 90)),
                    'next_maintenance_due' => now()->addDays(rand(30, 180)),
                    'equipment_list' => json_encode([
                        'First Aid Kit', 'Fire Extinguisher', 'Emergency Radio', 'GPS Device'
                    ]),
                    'gps_enabled' => true,
                    'current_latitude' => 8.1300 + (rand(-100, 100) / 1000),
                    'current_longitude' => 125.1300 + (rand(-100, 100) / 1000),
                ]);
                $vehicles[] = $vehicle;
            }
        }

        // Create Incidents
        $incidentTypes = ['traffic_accident', 'medical_emergency', 'fire_incident', 'natural_disaster', 'criminal_activity'];
        $severityLevels = ['low', 'medium', 'high', 'critical'];
        $statuses = ['pending', 'active', 'resolved', 'closed'];
        $incidents = [];

        for ($i = 1; $i <= 25; $i++) {
            $municipality = array_rand($municipalities);
            $incidentDate = now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            $incident = Incident::create([
                'incident_number' => 'INC-' . $incidentDate->year . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'incident_type' => $incidentTypes[array_rand($incidentTypes)],
                'severity_level' => $severityLevels[array_rand($severityLevels)],
                'status' => $statuses[array_rand($statuses)],
                'location' => collect([
                    'National Highway, Km 15',
                    'Barangay Poblacion Central',
                    'Downtown Commercial Area',
                    'Rural Road, Sitio 1',
                    'Provincial Road Junction',
                    'Market Area',
                    'School Zone',
                    'Hospital Vicinity'
                ])->random() . ', ' . $municipality,
                'municipality' => $municipality,
                'latitude' => 8.1300 + (rand(-200, 200) / 1000),
                'longitude' => 125.1300 + (rand(-200, 200) / 1000),
                'description' => collect([
                    'Multi-vehicle collision on main highway during rush hour. Emergency services dispatched immediately.',
                    'Medical emergency reported at residential area. Patient requires immediate transport to hospital.',
                    'Structure fire reported in commercial building. Fire department responding with full equipment.',
                    'Flooding in low-lying areas due to heavy rainfall. Evacuation procedures initiated.',
                    'Traffic accident involving motorcycle and jeepney. Minor injuries reported.',
                    'Emergency medical response needed for elderly patient with chest pains.',
                    'Kitchen fire in residential house successfully contained by local fire volunteers.',
                    'Landslide blocking main access road to remote barangay.',
                ])->random(),
                'incident_date' => $incidentDate,
                'weather_condition' => collect(['clear', 'cloudy', 'rainy', 'stormy'])->random(),
                'road_condition' => collect(['dry', 'wet', 'slippery', 'damaged'])->random(),
                'casualty_count' => rand(0, 5),
                'injury_count' => rand(0, 3),
                'fatality_count' => rand(0, 1),
                'property_damage_estimate' => rand(0, 1) ? rand(5000, 500000) : null,
                'vehicle_involved' => rand(0, 1),
                'assigned_staff_id' => $staffUsers[array_rand($staffUsers)]->id,
                'assigned_vehicle_id' => rand(0, 1) ? $vehicles[array_rand($vehicles)]->id : null,
                'reported_by' => collect([$admin->id, ...$staffUsers, ...$responders])->random()->id,
                'response_time' => $incidentDate->addMinutes(rand(5, 45)),
                'resolved_at' => in_array($statuses[array_rand($statuses)], ['resolved', 'closed']) ? $incidentDate->addHours(rand(1, 8)) : null,
            ]);
            $incidents[] = $incident;
        }

        // Create Victims for some incidents
        foreach ($incidents as $incident) {
            if ($incident->casualty_count > 0) {
                for ($v = 0; $v < $incident->casualty_count; $v++) {
                    Victim::create([
                        'incident_id' => $incident->id,
                        'first_name' => collect(['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Elena'])->random(),
                        'last_name' => collect(['Santos', 'Garcia', 'Rodriguez', 'Cruz', 'Reyes', 'Mendoza', 'Torres', 'Flores'])->random(),
                        'age' => rand(5, 80),
                        'gender' => collect(['male', 'female'])->random(),
                        'contact_number' => '+63 9' . rand(100000000, 999999999),
                        'medical_status' => collect(['uninjured', 'minor_injury', 'major_injury', 'critical'])->random(),
                        'injury_description' => collect([
                            'Minor cuts and bruises',
                            'Sprained ankle',
                            'Head trauma',
                            'Broken arm',
                            'Internal bleeding',
                            'Burn wounds',
                            'Respiratory issues'
                        ])->random(),
                        'transportation_method' => collect(['ambulance', 'private_vehicle', 'on_foot'])->random(),
                        'victim_role' => collect(['driver', 'passenger', 'pedestrian', 'bystander'])->random(),
                        'helmet_used' => rand(0, 1),
                        'seatbelt_used' => rand(0, 1),
                    ]);
                }
            }
        }

        $this->command->info('BukidnonAlert database seeded successfully!');
        // $this->command->info('Admin credentials: admin@bukidnonalert.gov.ph / BukidnonAlert@2025');
        // $this->command->info('Staff credentials: [email] / password123');
        // $this->command->info('Responder credentials: [email] / responder123');
    }
}
