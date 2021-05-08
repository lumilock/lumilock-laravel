<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Traits\UsesUuid;

    protected $table = 'services';

    protected $fillable = [
        'name',
        'uri',
        'secret',
        'path',
        'picture',
        'address'
    ];

    // all permissions of the currante service
    public function permissions ()
    {
        return $this->hasMany('lumilock\lumilock\App\Models\Permission', 'service_id');
    }

    // all the services to which the user has access
    public function access ()
    {
        return $this->permissions()->where('name', '=', 'access')->has('authorized');
    }
}