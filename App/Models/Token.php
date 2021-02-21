<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use Traits\UsesUuid;

    protected $table = 'tokens';

    protected $fillable = [
        'user_id',
        'expires_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'token'
    ];

    // User link to this token
    public function user ()
    {
        return $this->belongsTo('lumilock\lumilock\App\Models\User','user_id');
    }
}