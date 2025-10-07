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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address');
            $table->boolean('successful')->default(false);
            $table->text('user_agent')->nullable();
            $table->string('failure_reason')->nullable(); // invalid_credentials, account_locked, etc.
            $table->integer('attempt_count')->default(1);
            $table->timestamp('attempted_at');
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
            
            // Indexes for security monitoring
            $table->index(['email', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index(['successful', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
