<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('room_user', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('room_id');
      $table->foreign('room_id')->references('id')->on('rooms');
      $table->unsignedInteger('alias_id');
      $table->foreign('alias_id')->references('id')->on('aliases')->onDelete('cascade');
      $table->unsignedInteger('role_id');
      $table->foreign('role_id')->references('id')->on('roles');

//      $table->unsignedBigInteger('user_id');
//      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//      $table->unique(['room_id', 'user_id', 'role_id'], 'ROOM_USER_ROLE');

      $table->unique(['room_id', 'alias_id', 'role_id'], 'ROOM_ALIAS_ROLE');
      $table->boolean('avatar')->default(0);
      $table->ipAddress('ip_address');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('room_user');
  }
}
