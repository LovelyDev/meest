<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliasRoomTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('alias_room', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('room_id');
      $table->foreign('room_id')->references('id')->on('rooms');
      $table->unsignedInteger('alias_id');
      $table->foreign('alias_id')->references('id')->on('aliases')->onDelete('cascade');
      $table->unsignedInteger('role_id');
      $table->foreign('role_id')->references('id')->on('roles');
      $table->ipAddress('ip_address');
      $table->unique(['room_id', 'alias_id', 'role_id'], 'ROOM_ALIAS_ROLE');
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
    Schema::dropIfExists('alias_room');
  }
}
