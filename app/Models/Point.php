<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Point extends Model
{
    use HasFactory;

    // On autorise Laravel à remplir ces champs automatiquement
    protected $fillable = ['nom_machine', 'lieu', 'status'];

    // Relation : Un point a plusieurs collectes
    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    // Relation : Un point a plusieurs dépenses
    public function depenses()
    {
        return $this->hasMany(Depense::class)->where('portee', 'point');
    }
}