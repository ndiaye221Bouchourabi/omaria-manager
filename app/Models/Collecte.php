<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collecte extends Model
{
    // On autorise le remplissage de ces champs
    protected $fillable = ['point_id', 'semaine', 'date_collecte', 'montant'];

    // Relation inverse : Une collecte appartient à un Point
    public function point()
    {
        return $this->belongsTo(Point::class);
    }
}
