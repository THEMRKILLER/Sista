<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

class ExposeHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    $response = $next($request);

    $token = JWTAuth::getToken();
    if(!$token){
        throw new BadRequestHtttpException('Token not provided');
    }
    try{
        $token = JWTAuth::refresh($token);
    }catch(TokenInvalidException $e){
        throw new AccessDeniedHttpException('The token is invalid');
    }
    $response->header('Access-Control-Expose-Headers', 'Authorization' );
    $response->headers->set('Authorization', 'Bearer '.$token);

    return $response;

    }
}
