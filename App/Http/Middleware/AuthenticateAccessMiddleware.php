<?php

namespace lumilock\lumilock\App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use lumilock\lumilock\App\Traits\ApiResponser;

class AuthenticateAccessMiddleware
{

    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $validSecrets = explode(',', env('ACCEPTED_SECRETS'));
            if (in_array($request->header('Authorization_sso_secret'), $validSecrets)) {
                return $next($request);
            }
            return $this->errorResponse('UNAUTHORIZED', Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            dd('AUTH ERROR : ' . $e);
        }
    }
}
