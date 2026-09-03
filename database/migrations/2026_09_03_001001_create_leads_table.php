<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('leads'); }
};
