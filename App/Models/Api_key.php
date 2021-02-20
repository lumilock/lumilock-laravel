<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Api_key extends Model
{
    use Traits\UsesUuid;

    protected $table = 'api_keys';

    protected $fillable = [
        'name',
        'uri',
        'token',
        'expires_at'
    ];

    // all permissions for this api key
    public function permissions ()
    {
        return $this->belongsToMany(
            'lumilock\lumilock\App\Models\Permission', // Modele cible que l'on souhaite récupérer
            'access', // Nom de la base pivot entre le modèle source et cible
            'api_key_id', // id qui correspond au modèle source, dans le pivot
            'permission_id' // id qui correspond au modèle cible, dans le pivot
            )->using('lumilock\lumilock\App\Models\Access') // chemin du modèle pivot
            ->withPivot([ // liste des élements se trouvant dans le modèle pivot autre que les ids
                'is_active'
            ]);
    }
}