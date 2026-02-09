<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = ['application_id', 'key_prefix', 'name', 'status'];

    public static function generate(string $name, array $extra = [])
    {
        $key = 'ak_' . Str::random(48);
        $prefix = substr($key, 0, 8);
        
        return [
            'key' => $key,
            'key_prefix' => $prefix,
            'model' => self::create([
                'application_id' => $extra['application_id'],
                'key_prefix' => $prefix,
                'name' => $name,
                'status' => 'active'
            ])
        ];
    }
}
