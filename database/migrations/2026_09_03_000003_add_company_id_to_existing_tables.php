<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'customers', 'leads', 'quotes', 'invoices', 'appointments', 'projects',
        'contracts', 'workflows', 'conversations', 'webhooks', 'subscription_plans',
        'subscriptions',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
