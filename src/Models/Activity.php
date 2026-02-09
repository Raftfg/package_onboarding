<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        // On suppose que l'utilisateur est celui de l'application hôte
        $userModel = config('auth.providers.users.model');
        return $this->belongsTo($userModel);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
