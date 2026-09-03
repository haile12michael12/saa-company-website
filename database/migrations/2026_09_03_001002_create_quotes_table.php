<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('quotes'); }
};
