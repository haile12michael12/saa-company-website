<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('metric_key'); // page_views, unique_visitors, leads_generated, quotes_accepted, revenue_collected
            $table->decimal('metric_value', 14, 2)->default(0);
            $table->date('metric_date');
            $table->json('dimension')->nullable(); // source, channel, category
            $table->timestamps();

            $table->unique(['company_id', 'metric_key', 'metric_date']);
            $table->index(['metric_key', 'metric_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_metrics');
    }
};
