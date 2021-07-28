<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('rooms', function (Blueprint $table) {
      $table->increments('id');
      $table->boolean('spectator_only')->default(0);
      $table->string('title')->unique();
      $table->text('description');
      $table->text('welcome')->nullable();
      $table->unsignedInteger('limit')->default(30);
      $table->string('language')->default('en');
      $table->string('image_path')->nullable();
      $table->string('socket_id')->default('');
      $table->unsignedBigInteger('room_youtube_access_id');
      $table->foreign('room_youtube_access_id')->references('id')->on('room_youtube_accesses');
      $table->unsignedBigInteger('room_access_id');
      $table->foreign('room_access_id')->references('id')->on('room_accesses');
      $table->unsignedBigInteger('room_type_id');
      $table->foreign('room_type_id')->references('id')->on('room_types');
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users');
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
    Schema::dropIfExists('rooms');
  }
}
