<?php

use App\Models\MilitaryBranch;
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
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(MilitaryBranch::class)->nullable()->constrained();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->unique(['name', 'military_branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_models');
    }
};
