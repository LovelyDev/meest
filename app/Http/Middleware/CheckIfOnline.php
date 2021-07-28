<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfOnline
{
  public function handle($request, Closure $next)
  {
    $params = $request->all();

    $userId = session()->get('chat_user_id', 0);
    $userToken = session()->get('chat_user_token', '');

    $whitelistedUris = [
      '/api/auth/login',
      '/api/settings',
      '/api/get-token',
    ];


    if (!in_array(request()->getRequestUri(), $whitelistedUris)) {
      if (!Auth::guard('api')->check()) {
        $asdsa = 30;
        // return redirect()->to($request->path());
      }
    }


    return $next($request);
  }
}
