<?php

namespace App\Http\Controllers;

use App\Alias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\User;
use App\Role;
use App\AvatarDefault;

class AuthController extends Controller
{
  /**
   * Create user
   *
   * @param  [string] name
   * @param  [string] email
   * @param  [string] password
   * @param  [string] password_confirmation
   * @return [string] message
   */
  public function signup(Request $request)
  {
    $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|confirmed'
    ]);
    $user = new User([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password)
    ]);
    $user->save();
    return response()->json([
      'message' => 'Successfully created user!'
    ], 201);
  }

  /**
   * Login user and create token
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [boolean] remember_me
   * @return [string] access_token
   * @return [string] token_type
   * @return [string] expires_at
   */
  public function login(Request $request)
  {

    $params = request()->all();
    $guessRaw = $params['guess'];
    $guess = strtolower($guessRaw);
    $gender = $params['gender'];

    // We check if login is guess and create a user.
    if ($guess) {
      try {
        $cleanName = preg_replace('/\s+/', '_', $guess);
        $name = "guest_{$cleanName}";
        $alias = "Guest ". ucwords($guess);
        $password = 'p@ssw0rd@123';

        $user = User::create([
          'name' => $guessRaw,
          'email' => "{$name}@chathorizon.net",
          'gender' => $gender,
          'password' => Hash::make($password),
        ]);

        // Add guest role to the user.
        $guestRole = Role::find(12);
        $user->roles()->attach($guestRole);

        // Create guest alias.
        $alias = Alias::create([
          'alias' => $alias,
          'slug' => $name,
          'gender' => $gender,
          'role_id' => $guestRole->id,
          'user_id' => $user->id,
        ]);

        // Save default avatar
        $avatar =  AvatarDefault::where('gender', $gender)->first();

        $alias->bodies = $avatar['bodies'];
        $alias->hair = $avatar['hair'];
        $alias->faces = $avatar['faces'];
        $alias->pants = $avatar['pants'];
        $alias->shirts = $avatar['shirts'];
        $alias->coats = $avatar['coats'];
        $alias->shoes = $avatar['shoes'];
        $alias->head_accessories = $avatar['head_accessories'];
        $alias->accessories = $avatar['accessories'];
        $alias->specials = $avatar['specials'];
        $alias->save();

        request()['email'] = $user->email;
        request()['password'] = $password;

      } catch (\Illuminate\Database\QueryException $e) {

        abort(422, "The username {$guess} is already taken, please try another name.");
      }
    }

    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
      'remember_me' => 'boolean'
    ]);

    $credentials = request(['email', 'password']);

    if (!Auth::attempt($credentials))

      return response()->json([
        'message' => "{$params['email']} is not a valid user, or credentials is incorrect"
      ], 401);

    $user = $request->user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;

    if ($request->remember_me)
      $token->expires_at = Carbon::now()->addWeeks(1);
    $token->save();

    return response()->json([
      'csrf' => $user->id,
      'user_name' => $user->name,
      'access_token' => $tokenResult->accessToken,
      'token_type' => 'Bearer',
      'expires_at' => Carbon::parse(
        $tokenResult->token->expires_at
      )->toDateTimeString()
    ]);
  }

  /**
   * Login user and create token
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [boolean] remember_me
   * @return [string] access_token
   * @return [string] token_type
   * @return [string] expires_at
   */
  public function loginGuess(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
      'remember_me' => 'boolean'
    ]);
    $credentials = request(['email', 'password']);

    if (!Auth::attempt($credentials))

      return response()->json([
        'message' => 'Unauthorized'
      ], 401);

    $user = $request->user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->token;

    if ($request->remember_me)
      $token->expires_at = Carbon::now()->addWeeks(1);
    $token->save();

    return response()->json([
      'access_token' => $tokenResult->accessToken,
      'token_type' => 'Bearer',
      'expires_at' => Carbon::parse(
        $tokenResult->token->expires_at
      )->toDateTimeString()
    ]);
  }

  /**
   * Logout user (Revoke the token)
   *
   * @return [string] message
   */
  public function logout(Request $request)
  {
    $request->user()->token()->revoke();
    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }

  /**
   * Get the authenticated User
   *
   * @return [json] user object
   */
  public function user(Request $request)
  {
    return response()->json($request->user());
  }

  /**
   * Updates the user model.
   *
   * @return [json] user object
   */
  public function update($id)
  {
    $params = request()->all();
    $user = User::find($id);
    $user->chat_interface = $params['chat_interface'];
    $user->save();
    $user->refresh();

    return response()->json($user);
  }
}
