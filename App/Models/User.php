<?php

namespace lumilock\lumilock\App\Models;

use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use lumilock\lumilock\Facades\lumilock;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    use Traits\UsesUuid;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login',
        'first_name',
        'last_name',
        'email',
        'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];


    /**
     * Get the first_name of the user and format it.
     *
     * @param string
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = lumilock::name($value);
    }

    /**
     * Get the last_name of the user and format it.
     *
     * @param string
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = lumilock::name($value);
    }

    public function setLoginAttribute($value)
    {
        // search two array of chars
        // 1- split  by  '$split$'
        $loginSplit = explode("\$split\$", $value);
        // 2- split by '.'
        $loginPoint = explode(".", $value);

        if (count($loginPoint) == 2 && count($loginSplit) == 1) { // check if it's split by '.' and not by '$split$'
            $logins = $loginPoint;
        } else if (count($loginSplit) == 2) { // else check if it's split by '$split$'
            $logins = $loginSplit;
        } else {
            throw new Exception("login format is wrong, you have more then one spliter : " . $value);
        }
        // clean special char and convert to lowercase
        $leftLogin = lumilock::clean($logins[0]);
        $rightLogin = lumilock::clean($logins[1]);
        // concat and separate by '.'
        $login = $leftLogin . "." . $rightLogin;
        // count all users with same login
        $count = User::where('login', $login)->count();
        $nbr = 1;
        // check if login already exist
        if ($count != 0) {
            while ($count != 0) { // while a similare login exist try a new login
                $nbr++; // inscrement the number to concat with login
                $count = User::where('login', $login . $nbr)->count(); // check with the new number
            }
            $this->attributes['login'] = $login . $nbr; // save login
        } else {
            $this->attributes['login'] = $login; // save login
        }
    }

    // all permissions for this user
    public function permissions()
    {
        return $this->belongsToMany(
            'lumilock\lumilock\App\Models\Permission', // Modele cible que l'on souhaite récupérer
            'rights', // Nom de la base pivot entre le modèle source et cible
            'user_id', // id qui correspond au modèle source, dans le pivot
            'permission_id' // id qui correspond au modèle cible, dans le pivot
        )->using('lumilock\lumilock\App\Models\Right') // chemin du modèle pivot
            ->withPivot([ // liste des élements se trouvant dans le modèle pivot autre que les ids
                'is_active'
            ]);
    }

    /**
     * hasPermission define if a user has or not a specific permission for a specific service
     * thansk to 2 params
     * @param String $appPath : the unique path of a service
     * @param String $permissionName : the name of the service permission
     * @return Boolean : is the user authorized for this permission ? 
     */
    public function hasPermission($appPath, $permissionName)
    {
        return $this->belongsToMany(
            'lumilock\lumilock\App\Models\Permission', // Modele cible que l'on souhaite récupérer
            'rights', // Nom de la base pivot entre le modèle source et cible
            'user_id', // id qui correspond au modèle source, dans le pivot
            'permission_id' // id qui correspond au modèle cible, dans le pivot
        )->using('lumilock\lumilock\App\Models\Right') // chemin du modèle pivot
            ->wherePivot('is_active', True) // Get all permission that user has to true
            ->where('name', '=', $permissionName) // filter by permissions name, get only permissions which have the name $permissionName from params
            ->with('service') // get the service like to this or these permissions thanks to the permission Model function (service)
            ->whereHas('service', function ($query) use ($appPath) { // filter by service path
                $query->where('path', '=', $appPath); // get the only one which have the service path $appPath from params
            })
            ->get()
            ->count() > 0;
    }

    // all tokens of the currante user
    public function tokens()
    {
        return $this->hasMany('lumilock\lumilock\App\Models\Token', 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \lumilock\lumilock\database\factories\UserFactory::new();
    }
}
