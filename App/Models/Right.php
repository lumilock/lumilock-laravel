<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    use Traits\UsesUuid;

    protected $table = 'rights';

    protected $fillable = [
        'user_id',
        'permission_id',
        'is_active'
    ];
}