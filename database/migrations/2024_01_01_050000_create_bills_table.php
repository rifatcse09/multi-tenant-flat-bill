<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bills', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('flat_id')->constrained()->cascadeOnDelete();
            $t->foreignId('bill_category_id')->constrained('bill_categories')->cascadeOnDelete();
            $t->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $t->date('month');
            $t->decimal('amount',12,2);
            $t->decimal('due_carry_forward',12,2)->default(0);
            $t->enum('status',['paid','unpaid'])->default('unpaid');
            $t->string('notes',255)->nullable();
            $t->timestamps();
            $t->index(['owner_id','flat_id','bill_category_id','month']);
            $t->unique(['flat_id','bill_category_id','month']);
        });
    }
    public function down(): void { Schema::dropIfExists('bills'); }
};