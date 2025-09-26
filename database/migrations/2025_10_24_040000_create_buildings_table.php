<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('buildings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $t->string('name');
            $t->string('address')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->index('owner_id');
        });
    }
    public function down(): void { Schema::dropIfExists('buildings'); }
};