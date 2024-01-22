<?php

use App\Enums\Sex;
use App\Enums\State;
use App\Models\MilitaryPosition;
use App\Models\Rank;
use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('state', unsigned: true)->default(State::PUBLISHED);
            $table->string('first_name')->nullable()->index();
            $table->string('last_name')->nullable()->index();
            $table->string('middle_name')->nullable()->index();
            match (DB::getDriverName()) {
                'sqlite' => $table->string('full_name')->virtualAs('first_name || " " || last_name || CASE WHEN middle_name IS NOT NULL THEN " " || middle_name ELSE "" END')->index(),
                default => $table->string('full_name')->virtualAs('CONCAT(first_name, " ", last_name, IF(middle_name IS NOT NULL, CONCAT(" ", middle_name), ""))'),
            };
            $table->string('call_sign')->nullable();
            $table->string('slug')->unique();
            $table->date('birth')->nullable();
            $table->date('death')->nullable();
            $table->date('burial')->nullable();
            $table->date('wound')->nullable();
            $table->foreignId('birth_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('death_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('burial_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('wound_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->text('death_details')->nullable();
            $table->text('obituary')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('sex')->default(Sex::MALE);
            $table->boolean('civil')->default(0);
            $table->foreignIdFor(Unit::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Rank::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(MilitaryPosition::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
