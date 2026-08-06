<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('PlanningId');
            $table->integer('SlotOrder');
            $table->string('SlotName', 100);
            $table->integer('OriginalQuantity')->default(0);
            $table->integer('BalancedQuantity')->default(0);
            $table->boolean('IsActive')->default(true);
            $table->foreign('PlanningId')->references('PlanningId')->on('plannings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_slots');
    }
};
