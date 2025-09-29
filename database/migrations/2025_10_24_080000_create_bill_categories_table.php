<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bill_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $t->string('name');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['owner_id','name']);
        });
    }
    public function down(): void { Schema::dropIfExists('bill_categories'); }
};