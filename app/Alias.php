<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'alias', 'slug', 'gender', 'role_id', 'user_id',
  ];

  protected $with = ['role', 'rooms'];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function role()
  {
    return $this->belongsTo('App\Role');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function users()
  {
    return $this->belongsTo('App\User');
  }

  /**
   * The list of rooms that the user is in.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function rooms()
  {
    return $this->belongsToMany('App\Room');
  }
}
