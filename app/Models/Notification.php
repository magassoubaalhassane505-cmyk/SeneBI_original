<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'level',
        'action_url',
        'metadata',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function notifyManager(string $type, string $title, string $message, string $level = 'info', ?string $actionUrl = null): void
    {
        $managers = User::where('role', 'manager')
            ->where('is_active', true)
            ->get();

        foreach ($managers as $manager) {
            static::create([
                'user_id' => $manager->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'level' => $level,
                'action_url' => $actionUrl,
            ]);
        }
    }

    public static function notifyUser(int $userId, string $type, string $title, string $message, string $level = 'info', ?string $actionUrl = null): void
    {
        static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'level' => $level,
            'action_url' => $actionUrl,
        ]);
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    public function getLevelClassAttribute(): string
    {
        return match ($this->level) {
            'danger', 'critical' => 'danger',
            'warning' => 'warning',
            'success' => 'success',
            default => 'info',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'stock' => 'fa-boxes',
            'visite' => 'fa-calendar-alt',
            'parcelle' => 'fa-map-marked-alt',
            'consommation' => 'fa-seedling',
            'rentabilite' => 'fa-calculator',
            'inscription' => 'fa-user-plus',
            default => 'fa-bell',
        };
    }
}
