<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plan_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->string('day_of_week');
            $table->string('meal');
            $table->foreignId('dish_id')->nullable()->constrained('dishes')->nullOnDelete();
            $table->foreignId('baby_dish_id')->nullable()->constrained('dishes')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'week_start', 'day_of_week', 'meal'], 'meal_plan_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plan_entries');
    }
};
