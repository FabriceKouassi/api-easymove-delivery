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
        Schema::create('permis', function (Blueprint $table) {
            $table->id();
            $table->string('front_img')->nullable();
            $table->string('back_img')->nullable();
            $table->string('human_selfie_img')->nullable();
            $table->date('expiry_date');
            $table->string('driving_licence_id');
            $table->boolean('isValidated')->default(false);
            $table->string('motif_refus')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permis');
    }
};
