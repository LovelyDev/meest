<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvatarDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avatar_defaults', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('gender')->default('m');
            $table->string('bodies')->default('');
            $table->string('hair')->default('');
            $table->string('faces')->default('001');
            $table->string('pants')->default('');
            $table->string('shirts')->default('');
            $table->string('coats')->default('');
            $table->string('shoes')->default('');
            $table->string('head_accessories')->default('');
            $table->string('accessories')->default('');
            $table->string('specials')->default('');
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
        Schema::dropIfExists('avatar_defaults');
    }
}
