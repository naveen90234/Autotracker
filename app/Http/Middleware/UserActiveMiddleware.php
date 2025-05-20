<?php

namespace App\Http\Middleware;
use App\Providers\RouteServiceProvider;

use Closure;
use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class UserActiveMiddleware
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
        $guard = Auth::getDefaultDriver();
        if( $request->is('api/*')){
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            if($userId)
            {
                $profile = User::find($userId);
                if($profile)
                {
                    if($profile->status == 0)
                    {
                        $response['status'] = false;
                        $response['data'] = (object)[];
                        $response['message'] = 'Your account has been deactivated, please contact to administrator.';

						// JWTAuth::invalidate(JWTAuth::getToken());
						// UserDevice::where(['user_id'=>$userId])->delete();
                        return response()->json($response,401);
                    }
                }else{
                    $response['status'] = false;
                    $response['data'] = (object)[];
                    $response['message'] = 'User not found.';

                    // JWTAuth::invalidate(JWTAuth::getToken());
                    // UserDevice::where(['user_id'=>$userId])->delete();
                    return response()->json($response,401);
                }
            }
        }
        else
        {
            if(Auth::guard($guard)->check() && Auth::guard($guard)->User()->status == 0 && in_array($guard,['web']))
            {
                Auth::guard($guard)->Logout();
                $request->session()->flash('alert-danger', 'Your account has been deactivated, please contact to administrator.');
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
