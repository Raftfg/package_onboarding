<?php

namespace Raftfg\OnboardingPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;
    protected $fillable = ['app_name', 'description', 'master_key', 'status'];
    protected $hidden = ['master_key'];
}
