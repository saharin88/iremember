<?php

use App\Models\Award;
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
        Schema::create('award_person', function (Blueprint $table) {
            $table->foreignIdFor(Award::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Person::class)->constrained()->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->text('additional_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_person');
    }
};
