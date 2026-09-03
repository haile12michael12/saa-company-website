<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('payments'); }
};
