<?php

namespace lumilock\lumilock\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Traits\UsesUuid;
    use HasFactory;

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

    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \lumilock\lumilock\database\factories\ServiceFactory::new();
    }
}