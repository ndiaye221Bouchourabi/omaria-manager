<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = ['point_id', 'type_depense', 'portee', 'description', 'date_depense', 'montant'];

    // Relation : Une dépense peut appartenir à un Point
    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    // Un "Scope" : C'est un filtre réutilisable très puissant
    // Pour appeler : Depense::globales()->get()
    public function scopeGlobales($query)
    {
        return $query->where('portee', 'globale');
    }
}