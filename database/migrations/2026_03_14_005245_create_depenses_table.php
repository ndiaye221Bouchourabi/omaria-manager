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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            // nullable() car une dépense globale n'a pas de point_id
            $table->foreignId('point_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type_depense');
            $table->enum('portee', ['point', 'globale']);
            $table->text('description')->nullable();
            $table->date('date_depense');
            $table->decimal('montant', 15, 3); // Plus précis que le 'float' de JS  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
