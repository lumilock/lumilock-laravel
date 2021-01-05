<?php

namespace lumilock\lumilock\App\Models;

use Exception;
use Illuminate\Auth\Authenticatable;
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
    public function permissions ()
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
}
