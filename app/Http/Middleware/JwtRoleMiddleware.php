<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtRoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
        } catch (\Exception $e) {
            return responseJson('Unauthorized', Response::HTTP_UNAUTHORIZED, 'Error');
        }

        if (!$user) {
            return responseJson('Unauthorized', Response::HTTP_UNAUTHORIZED, 'Error');
        }

        $userRole = $token->getClaim('user')['role_id'];

        foreach ($roles as $role) {
            if ($userRole != $role) {
                return responseJson('Unauthorized', Response::HTTP_FORBIDDEN, 'Error');
            }
        }

        return $next($request);
    }
}
