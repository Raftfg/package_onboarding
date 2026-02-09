<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnboardingSession extends Model
{
    protected $fillable = [
        'uuid',
        'email',
        'organization_name',
        'subdomain',
        'database_name',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->uuid = $model->uuid ?? (string) Str::uuid());
    }

    /**
     * Vérifie si la session est complétée
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si la session est en attente d'activation
     */
    public function isPendingActivation(): bool
    {
        return $this->status === 'pending_activation';
    }

    /**
     * Marque la session comme en attente d'activation
     */
    public function markAsPendingActivation(): void
    {
        $this->update(['status' => 'pending_activation']);
    }

    /**
     * Marque la session comme complétée
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
