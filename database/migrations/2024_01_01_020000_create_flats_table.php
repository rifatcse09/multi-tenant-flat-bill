<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('building_id')->constrained()->cascadeOnDelete();
            $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $t->string('flat_number');
            $t->string('flat_owner_name')->nullable();
            $t->string('flat_owner_phone')->nullable();
            $t->timestamps();
            $t->index(['owner_id','building_id']);
            $t->unique(['building_id','flat_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('flats'); }
};
