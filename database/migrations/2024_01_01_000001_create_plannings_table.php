<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id('PlanningId');
            $table->string('RequestCode', 100)->unique();
            $table->string('CandidateToken', 100)->default('VEH-TEST1234');
            $table->dateTime('CreatedAt')->useCurrent();
            $table->string('Status', 50)->default('completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};
