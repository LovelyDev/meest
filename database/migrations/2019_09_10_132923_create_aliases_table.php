<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias')->unique();
            $table->string('slug')->unique();
            $table->char('gender')->default('m');
            $table->string('bodies')->default('001');
            $table->string('hair')->default('001');
            $table->string('faces')->default('001');
            $table->string('pants')->default('001');
            $table->string('shirts')->default('001');
            $table->string('coats')->default('');
            $table->string('shoes')->default('001');
            $table->string('head_accessories')->default('');
            $table->string('accessories')->default('');
            $table->string('specials')->default('');
            $table->float('hours', 8, 2)->default(0);
            $table->boolean('disabled')->default(false);
            $table->unsignedInteger('alias_child_id')->default(0);
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('aliases');
    }
}
