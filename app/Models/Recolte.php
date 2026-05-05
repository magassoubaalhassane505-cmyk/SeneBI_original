<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['parcelle_id', 'date_recolte', 'quantite', 'prix_unitaire', 'user_id'])]
class Recolte extends Model
{
    use HasFactory;

    protected $casts = [
        'date_recolte' => 'date',
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
    ];

    // Relations
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getRevenuTotalAttribute()
    {
        return $this->quantite * $this->prix_unitaire;
    }

    public function getRendementAttribute()
    {
        if ($this->parcelle && $this->parcelle->surface > 0) {
            return $this->quantite / $this->parcelle->surface;
        }
        return 0;
    }
}
