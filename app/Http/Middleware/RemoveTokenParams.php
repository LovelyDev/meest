<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class RemoveTokenParams
{
  public function handle($request, Closure $next)
  {
    $params = $request->all();

    if (isset($params['token']) && isset($params['csrf'])
      && $params['token'] && $params['csrf']) {
      Auth::loginUsingId($params['csrf']);

      $request->query->remove('token');
      $request->query->remove('csrf');

      // Store passport access token in session.
      session()->put('chat_user_id', $params['csrf']);
      session()->put('chat_user_token', $params['token']);
      session()->save();

      return redirect()->to($request->path());
    }

    return $next($request);
  }
}
