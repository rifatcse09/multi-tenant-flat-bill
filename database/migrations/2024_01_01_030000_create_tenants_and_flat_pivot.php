<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tenants', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable()->unique();
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('flat_tenant', function (Blueprint $t) {
            $t->id();
            $t->foreignId('flat_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->timestamps();
            $t->index(['flat_id','tenant_id','start_date','end_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('flat_tenant');
        Schema::dropIfExists('tenants');
    }
};
