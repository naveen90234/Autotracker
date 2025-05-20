<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class TokenExpiryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $response['status'] = false;
        // $response['data'] = (object)[];
        $response['data'] = "";

        if (!$token = JWTAuth::getToken()) {
            // $response['message'] = 'Token is required.';
            $response['message'] = 'There might be some network error, please try again later.';

            return response()->json($response);
        }

        try {
            $user = JWTAuth::authenticate($token);
        } catch (TokenExpiredException $e) {
            // $response['message'] = 'Token Expired! Please Login Again.';
            $response['message'] = 'There might be some network error, please try again later.';

            return response()->json($response);

        } catch (JWTException $e) {

            // $response['message'] = 'Invalid Token! Please Provide the correct token.';
            $response['message'] = 'There might be some network error, please try again later.';

            return response()->json($response);
        }

        if (!$user) {
            $response['message'] = 'User not found.';
            return response()->json($response, 201);
        }

        return $next($request);
    }
}
