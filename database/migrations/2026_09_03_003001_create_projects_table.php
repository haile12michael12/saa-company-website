<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('planned');
            $table->text('description')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('projects'); }
};
