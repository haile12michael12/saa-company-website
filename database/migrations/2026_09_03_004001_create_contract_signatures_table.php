<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('signer_name');
            $table->string('signer_email')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('contract_signatures'); }
};
