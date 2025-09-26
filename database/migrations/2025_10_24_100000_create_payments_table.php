<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $t->decimal('amount',12,2);
            $t->timestamp('paid_at')->nullable();
            $t->string('method',50)->nullable();
            $t->string('ref',100)->nullable();
            $t->timestamps();
            $t->index(['bill_id','paid_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
