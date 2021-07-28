<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use mysql_xdevapi\Exception;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $guessRaw = request()['guess'];
    $guess = strtolower($guessRaw);
    $gender = request()['gender'];

    // We check if login is guess and create a user.
    if ($guess) {

      try {
        $name = "guest_{$guess}";

        $user = User::create([
          'name' => $guessRaw,
          'email' => "{$name}@chathorizon.net",
          'gender' => $gender,
          'password' => Hash::make('p@ssw0rd@123'),
        ]);

        Auth::loginUsingId($user->id);

      } catch (\Illuminate\Database\QueryException $e) {
        Session::flash('duplicate_user', "The username {$guess} is already taken, please try another name.");
      }
    }

    $this->middleware('guest')->except('logout');
  }
}
