<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nom', 'type', 'quantite_actuelle', 'seuil_critique', 'cout_unitaire'])]
class Stock extends Model
{
    use HasFactory;

    protected $casts = [
        'quantite_actuelle' => 'decimal:2',
        'seuil_critique' => 'decimal:2',
        'cout_unitaire' => 'decimal:2',
    ];

    // Relations
    public function intrantsConsommes()
    {
        return $this->hasMany(IntrantConsomme::class);
    }

    // Méthodes utilitaires
    public function estCritique()
    {
        return $this->quantite_actuelle <= $this->seuil_critique;
    }

    public function getPourcentageRemplissage()
    {
        // Supposons une capacité maximale de 10000 kg pour le calcul
        $capaciteMax = 10000;
        return ($this->quantite_actuelle / $capaciteMax) * 100;
    }
}
