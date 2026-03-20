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
        Schema::create('collectes', function (Blueprint $table) {
            $table->id();
            // LE LIEN MAGIQUE : lie la collecte à un point existant
            $table->foreignId('point_id')->constrained()->onDelete('cascade');
            $table->string('semaine');
            $table->date('date_collecte');
            $table->decimal('montant', 15, 3); // Plus précis que le 'float' de JS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectes');
    }
};
