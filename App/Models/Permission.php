<?php

namespace lumilock\lumilock\App\Models;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use Traits\UsesUuid;

    protected $table = 'permissions';

    protected $fillable = [
        'service_id',
        'name'
    ];

    // Service link to this permission
    public function service ()
    {
        return $this->belongsTo('lumilock\lumilock\App\Models\service','service_id');
    }

    // all users
    public function users ()
    {
        return $this->belongsToMany(
            'lumilock\lumilock\App\Models\User', // Modele cible que l'on souhaite récupérer
            'rights', // Nom de la base pivot entre le modèle source et cible
            'permission_id', // id qui correspond au modèle source, dans le pivot
            'user_id' // id qui correspond au modèle cible, dans le pivot
            )->using('lumilock\lumilock\App\Models\Right') // chemin du modèle pivot
            ->withPivot([ // liste des élements se trouvant dans le modèle pivot autre que les ids
                'is_active'
            ]);
    }
}