<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test 2FA email sending';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Create or find test user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'municipality' => 'Valencia City',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        $this->info("Testing email to: {$email}");
        
        // Generate 2FA code
        $code = $user->generateTwoFactorCode();
        
        $this->info("2FA code generated: {$code}");
        $this->info("Code expires at: {$user->two_factor_expires_at}");
        
        $this->info("Email should be sent successfully!");
        
        return 0;
    }
}