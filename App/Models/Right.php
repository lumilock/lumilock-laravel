<?php

namespace lumilock\lumilock\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Right extends Pivot
{
    use Traits\UsesUuid;

    protected $table = 'rights';

    protected $fillable = [
        'user_id',
        'permission_id',
        'is_active'
    ];
}