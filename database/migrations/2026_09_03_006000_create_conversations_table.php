<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('email');
            $table->string('subject')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('conversations'); }
};
