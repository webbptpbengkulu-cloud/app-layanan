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
        Schema::create('downloadable_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_page_id')->constrained('information_pages')->cascadeOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->string('version')->default('1.0');
            $table->boolean('is_current')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloadable_forms');
    }
};
