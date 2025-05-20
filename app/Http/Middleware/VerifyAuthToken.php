<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $headers = apache_request_headers();
        $return_value = false;

        if (array_key_exists('authtoken', $headers) || array_key_exists('Authtoken', $headers)) {

            $auth_token = $headers['authtoken']??$headers['Authtoken'];

            $check = chkAuthToken($auth_token);

            if (!empty($check) && $check == 1) {

                $return_value = true;
            }
        }

        if ($return_value == false) {
            $response = ['status' => false, 'message' => "An error occurred. Please try again.", 'data' => null];
            return response()->json($response, 200);
        }


        return $next($request);
    }
}
