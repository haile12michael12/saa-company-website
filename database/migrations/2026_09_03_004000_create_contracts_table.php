<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->string('status')->default('draft');
            $table->text('content')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('contracts'); }
};
