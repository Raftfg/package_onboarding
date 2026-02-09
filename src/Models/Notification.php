<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'read_at',
        'data',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model');
        return $this->belongsTo($userModel);
    }

    public function markAsRead(): bool
    {
        if ($this->read_at === null) {
            return $this->update(['read_at' => now()]);
        }
        return false;
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
