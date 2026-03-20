<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id(); // Remplace ton nextId()
            $table->string('nom_machine'); // ex: O'Maria 01
            $table->string('lieu');        // ex: Gare Centrale

            // On utilise un enum pour limiter les erreurs de saisie
            $table->enum('status', ['Actif', 'Inactif'])->default('Actif');

            $table->timestamps(); // Crée automatiquement created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
