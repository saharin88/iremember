<?php

use App\Models\MilitaryPosition;
use App\Models\Person;
use App\Models\Rank;
use App\Models\Unit;
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
        Schema::create('person_unit', function (Blueprint $table) {
            $table->foreignIdFor(Person::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Unit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Rank::class)->nullable()->constrained();
            $table->foreignIdFor(MilitaryPosition::class)->nullable()->constrained();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_unit');
    }
};
