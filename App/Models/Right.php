<?php

namespace lumilock\lumilock\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Right extends Pivot
{
    use Traits\UsesUuid;
    use HasFactory;

    protected $table = 'rights';

    protected $fillable = [
        'user_id',
        'permission_id',
        'is_active'
    ];
    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \lumilock\lumilock\database\factories\RightFactory::new();
    }
}