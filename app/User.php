<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'gender',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  protected $with = ['roles', 'aliases'];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

//  /**
//   * The list of rooms that the user is in.
//   *
//   * @return \Illuminate\Database\Eloquent\Relations\HasMany
//   */
//  public function rooms()
//  {
//    return $this->belongsToMany('App\Room');
//  }

  /**
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function roles()
  {
    return $this->belongsToMany('App\Role');
  }

  /**
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function aliases()
  {
    return $this->hasMany('App\Alias');
  }
}
