<?php

use App\Models\Battle;
use App\Models\Person;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battle_person', function (Blueprint $table) {
            $table->foreignIdFor(Battle::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Person::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battle_person');
    }
};
