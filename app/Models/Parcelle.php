<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nom', 'region', 'surface', 'culture', 'user_id'])]
class Parcelle extends Model
{
    use HasFactory;

    protected $casts = [
        'surface' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recoltes()
    {
        return $this->hasMany(Recolte::class);
    }

    public function visites()
    {
        return $this->hasMany(Visite::class);
    }

    public function intrantsConsommes()
    {
        return $this->hasMany(IntrantConsomme::class);
    }
}
