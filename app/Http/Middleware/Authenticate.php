<?php

/**
 * Check user for Authentication
 *
 * @package     HyraHotel
 * @subpackage  Middleware
 * @category    Authenticate
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Factory as Auth;
use Lang;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = @$guards[0] ?: 'web';

        $redirect_to = 'login';
        if($guard == 'admin') {
            $redirect_to = 'admin.login';
        }
        else if($guard == 'host') {
            $redirect_to = 'host.login';
        }
        if($request->filled('token')) {
            $request->headers->set('Authorization',"Bearer ". $request->token);
        }
        
        if (!$this->auth->guard($guard)->check()) {
            session(['url.intended' => url()->current()]);
            if($guard == 'api') {
                return response()->json(['status' => 'token_expired'],401);
            }
            $redirect_url = resolveRoute($redirect_to);
            return redirect($redirect_url);
        }

        $user = $this->auth->guard($guard)->user();
        if($user->status == 'inactive' || $user->status == '0') {
            $this->auth->guard($guard)->logout();
            if($guard == 'api') {
                return response()->json(['status' => 'inactive_user'],403);
            }
            flashMessage('danger',Lang::get('messages.failed'), Lang::get('messages.your_account_is_disabled'));
            $redirect_url = resolveRoute($redirect_to);
            return redirect($redirect_url);
        }

        return $next($request);
    }
}
