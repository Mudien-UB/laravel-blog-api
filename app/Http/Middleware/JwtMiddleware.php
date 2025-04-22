<?php

namespace App\Http\Middleware;

use App\Http\Resources\BaseResource;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();

            auth()->setUser($user);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return BaseResource::respond(Response::HTTP_UNAUTHORIZED, 'Token expired');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return BaseResource::respond(Response::HTTP_UNAUTHORIZED, 'Token invalid');
        } catch (Exception $e) {
            return BaseResource::respond(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return $next($request);
    }
}
