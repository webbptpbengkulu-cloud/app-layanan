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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_category_id')->constrained('client_categories')->restrictOnDelete();
            $table->char('nik', 16)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender');
            $table->text('address');
            $table->foreignId('village_id')->constrained('villages')->restrictOnDelete();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_category_id');
            $table->index('village_id');
            $table->index('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
