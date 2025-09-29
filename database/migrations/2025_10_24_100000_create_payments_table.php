<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('payments', function (Blueprint $t) {
      $t->id();
      $t->foreignId('bill_id')->constrained()->cascadeOnDelete();
      $t->decimal('amount', 10, 2);
      $t->dateTime('paid_at');
      $t->string('method')->nullable();
      $t->string('reference')->nullable();
      $t->json('meta')->nullable();
      $t->timestamps();
      $t->softDeletes();
    });
  }
  public function down(): void { Schema::dropIfExists('payments'); }
};