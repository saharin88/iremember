<?php

use App\Models\Memorial;
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
        Schema::create('memorial_person', function (Blueprint $table) {
            $table->foreignIdFor(Memorial::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Person::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorial_person');
    }
};
