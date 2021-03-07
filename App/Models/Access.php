<?php

namespace lumilock\lumilock\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Access extends Pivot
{
    use Traits\UsesUuid;

    protected $table = 'access';

    protected $fillable = [
        'permission_id',
        'api_key_id',
        'is_active'
    ];
}