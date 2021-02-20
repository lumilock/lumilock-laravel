<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use Traits\UsesUuid;

    protected $table = 'tokens';

    protected $fillable = [
        'user_id',
        'service_id',
        'token',
        'expires_at'
    ];

    // Service link to this token
    public function service ()
    {
        return $this->belongsTo('lumilock\lumilock\App\Models\service','service_id');
    }

    // User link to this token
    public function user ()
    {
        return $this->belongsTo('lumilock\lumilock\App\Models\User','user_id');
    }
}