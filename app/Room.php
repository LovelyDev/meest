<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
  protected $appends = ['tenant_count'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'room_youtube_access_id',
    'room_access_id',
    'room_type_id',
    'title',
    'message',
    'description',
    'limit',
    'language',
    'user_id'
  ];

  /**
   * THe
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function tenants()
  {
    return $this->belongsToMany('App\Alias')->withTimestamps();
  }

  public function getTenantCountAttribute()
  {
    return $this->tenants()->count();
  }

}
