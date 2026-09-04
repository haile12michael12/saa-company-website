<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'title')) {
                $table->string('title')->nullable()->after('number');
            }
            if (!Schema::hasColumn('quotes', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('subtotal'); // 'fixed' or 'percentage'
            }
            if (!Schema::hasColumn('quotes', 'discount_rate')) {
                $table->decimal('discount_rate', 12, 2)->default(0)->after('discount_type');
            }
            if (!Schema::hasColumn('quotes', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_rate');
            }
            if (!Schema::hasColumn('quotes', 'tax_rate')) {
                $table->decimal('tax_rate', 12, 2)->default(0)->after('tax');
            }
            if (!Schema::hasColumn('quotes', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('total');
            }
            if (!Schema::hasColumn('quotes', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('quotes', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('terms');
            }
            if (!Schema::hasColumn('quotes', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('quotes', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('quotes', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('quotes', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
            if (!Schema::hasColumn('quotes', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('quotes', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('rejection_reason')->constrained('projects')->nullOnDelete();
            }
            if (!Schema::hasColumn('quotes', 'token')) {
                $table->string('token', 64)->nullable()->unique()->after('project_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $columnsToDrop = [
                'title', 'discount_type', 'discount_rate', 'discount_amount',
                'tax_rate', 'currency', 'terms', 'approved_at', 'approved_by',
                'sent_at', 'accepted_at', 'rejected_at', 'rejection_reason',
                'project_id', 'token'
            ];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('quotes', $col)) {
                    if (in_array($col, ['approved_by', 'project_id'])) {
                        $table->dropForeign([$col]);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
