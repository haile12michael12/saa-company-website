<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasTable('users')) {
            return;
        }

        $company = DB::table('companies')->first();
        if (! $company) {
            $companyId = DB::table('companies')->insertGetId([
                'name' => config('app.name', 'Company'),
                'slug' => 'default-company',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $companyId = $company->id;
        }

        DB::table('users')->whereNull('company_id')->update(['company_id' => $companyId]);
    }

    public function down(): void
    {
        // Backfilled ownership is intentionally retained during rollback.
    }
};
