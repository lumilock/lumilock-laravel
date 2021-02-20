<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use Traits\UsesUuid;

    protected $table = 'access';

    protected $fillable = [
        'permission_id',
        'api_key_id',
        'is_active'
    ];
}