<?php

namespace lumilock\lumilock\App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use lumilock\lumilock\App\Traits\ApiResponser;

class LumilockPermissionsMiddleware
{

    use ApiResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, string $app_path, string $permission_name)
    {
        try {
            if ($request->user() && $request->user()->hasPermission($app_path, $permission_name)) {
                return $next($request);
            } else {
                return $this->errorResponse('UNAUTHORIZED', Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            dd('AUTH ERROR : ' . $e);
        }
    }
}
