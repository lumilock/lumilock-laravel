<?php

namespace lumilock\lumilock\App\Gates;

use Illuminate\Auth\Access\Response;
use lumilock\lumilock\App\Models\User;

class LumilockGate
{
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \lumilock\lumilock\App\Models\User  $user
     * @param  string  $app_path
     * @param  string  $permission_name
     * @param  string  $errorMessage
     * @return bool
     */
    public static function use(User $user, string $app_path, string $permission_name, string $errorMessage = 'Not Authorized')
    {
        dd($app_path);
        return $user->hasPermission($app_path, $permission_name)
            ? Response::allow()
            : Response::deny($errorMessage);
    }
}
