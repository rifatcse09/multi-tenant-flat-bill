<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            return;
        }

        if (! Schema::hasColumn('subscription_plans', 'price_total_cents')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->unsignedInteger('price_total_cents')->nullable();
            });
        }

        if (! Schema::hasColumn('subscription_plans', 'is_free')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->boolean('is_free')->default(false);
            });
        }

        if (! Schema::hasColumn('subscription_plans', 'billing_period_months')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->unsignedTinyInteger('billing_period_months')->nullable();
            });
        }

        if (! Schema::hasColumn('subscription_plans', 'is_featured')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            return;
        }

        $toDrop = array_values(array_filter(
            ['is_free', 'price_total_cents', 'billing_period_months', 'is_featured'],
            fn (string $column) => Schema::hasColumn('subscription_plans', $column)
        ));

        if ($toDrop !== []) {
            Schema::table('subscription_plans', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }
};
