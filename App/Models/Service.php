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
        'picture_512',
        'picture_256',
        'picture_128',
        'picture_64',
        'picture_32',
        'picture_16'
    ];
}