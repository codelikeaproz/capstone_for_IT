<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Two-Factor Authentication fields
            $table->string('two_factor_code', 6)->nullable()->after('password');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
            
            // Account Security fields
            $table->integer('failed_login_attempts')->default(0)->after('two_factor_expires_at');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            
            // Email Verification field
            $table->string('email_verification_token')->nullable()->after('email_verified_at');
            
            // Indexes for performance
            $table->index('two_factor_expires_at');
            $table->index('locked_until');
            $table->index('email_verification_token');
            $table->index(['email', 'failed_login_attempts']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['email', 'failed_login_attempts']);
            $table->dropIndex(['email_verification_token']);
            $table->dropIndex(['locked_until']);
            $table->dropIndex(['two_factor_expires_at']);
            
            // Drop columns
            $table->dropColumn([
                'two_factor_code',
                'two_factor_expires_at',
                'failed_login_attempts',
                'locked_until',
                'email_verification_token'
            ]);
        });
    }
};
