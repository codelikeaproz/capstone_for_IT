<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteToSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:promote-superadmin {email? : The email of the user to promote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote an admin user to superadmin (system-wide access)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('  SuperAdmin Promotion Tool');
        $this->info('===========================================');
        $this->newLine();

        // Get email from argument or ask
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Enter the email of the user to promote to SuperAdmin');
        }

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User not found with email: {$email}");
            return 1;
        }

        // Display user information
        $this->info("User Found:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->full_name],
                ['Email', $user->email],
                ['Current Role', strtoupper($user->role)],
                ['Municipality', $user->municipality ?? 'N/A'],
                ['Status', $user->is_active ? 'Active' : 'Inactive'],
            ]
        );

        $this->newLine();

        // Check current role
        if ($user->isSuperAdmin()) {
            $this->warn("⚠️  User is already a SuperAdmin!");
            return 0;
        }

        // Confirm promotion
        $this->warn('IMPORTANT: SuperAdmin Role grants FULL SYSTEM ACCESS to ALL municipalities!');
        $this->newLine();

        if (!$this->confirm('Are you sure you want to promote this user to SuperAdmin?', false)) {
            $this->info('Promotion cancelled.');
            return 0;
        }

        // Perform promotion
        $oldRole = $user->role;
        $user->update(['role' => 'superadmin']);

        // Log activity
        activity()
            ->performedOn($user)
            ->withProperties([
                'old_role' => $oldRole,
                'new_role' => 'superadmin',
                'promoted_via' => 'artisan_command'
            ])
            ->log('User promoted to SuperAdmin via console command');

        $this->newLine();
        $this->info('✅ Successfully promoted user to SuperAdmin!');
        $this->newLine();

        $this->info('Role Change Summary:');
        $this->table(
            ['Before', 'After'],
            [[strtoupper($oldRole), 'SUPERADMIN']]
        );

        $this->newLine();
        $this->info('🔑 This user now has:');
        $this->line('  • Full system access');
        $this->line('  • View all municipalities');
        $this->line('  • Manage all users, incidents, vehicles');
        $this->line('  • Create other superadmin users');

        return 0;
    }
}
