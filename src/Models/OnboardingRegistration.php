<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnboardingRegistration extends Model
{
    protected $fillable = [
        'uuid', 'application_id', 'email', 'organization_name', 
        'subdomain', 'status', 'api_key', 'api_secret', 'metadata',
        'dns_configured', 'ssl_configured', 'provisioning_attempts'
    ];

    protected $casts = [
        'metadata' => 'array',
        'dns_configured' => 'boolean',
        'ssl_configured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->uuid = $model->uuid ?? (string) Str::uuid());
    }

    public function isActivated(): bool { return $this->status === 'activated'; }
    public function isInfrastructureReady(): bool { return $this->dns_configured && $this->ssl_configured; }
}
