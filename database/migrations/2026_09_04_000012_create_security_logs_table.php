<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type'); // login_success, login_failed, password_reset, token_generated, unauthorized_access, role_changed
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('severity')->default('info'); // info, warning, high, critical
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index(['company_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
