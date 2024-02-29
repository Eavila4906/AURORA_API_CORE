<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('rol');
            $table->integer('status')->default(0);

            $table->foreign('user')->references('id')->on('users');
            $table->foreign('rol')->references('id')->on('roles');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
