<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['stock_id', 'parcelle_id', 'quantite_consommee', 'date_consommation', 'user_id'])]
class IntrantConsomme extends Model
{
    use HasFactory;

    protected $casts = [
        'quantite_consommee' => 'decimal:2',
        'date_consommation' => 'date',
    ];

    // Relations
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getCoutTotalAttribute()
    {
        return $this->quantite_consommee * $this->stock->cout_unitaire;
    }
}
