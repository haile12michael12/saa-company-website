<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('webhook_logs'); }
};
