<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('bill_adjustments', function (Blueprint $t) {
      $t->id();
      $t->foreignId('bill_id')->constrained()->cascadeOnDelete();
      $t->decimal('amount', 10, 2);   // + add due, - discount/waiver
      $t->string('reason')->nullable();
      $t->string('type')->default('manual_due'); // manual_due|discount|waiver|correction
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('bill_adjustments'); }
};