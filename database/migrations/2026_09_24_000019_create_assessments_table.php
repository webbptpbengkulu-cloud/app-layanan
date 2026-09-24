<?php

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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rehabilitation_case_id')->constrained('rehabilitation_cases')->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->text('result');
            $table->text('service_needs');
            $table->text('recommendation');
            $table->boolean('needs_referral')->default(false);
            $table->timestamps();

            $table->index('rehabilitation_case_id');
            $table->index('officer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
