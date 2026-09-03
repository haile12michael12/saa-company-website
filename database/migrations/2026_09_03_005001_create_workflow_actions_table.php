<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('type');
            $table->json('configuration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('workflow_actions'); }
};
