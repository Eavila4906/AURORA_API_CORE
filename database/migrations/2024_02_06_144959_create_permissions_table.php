<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('rol');
            $table->unsignedBigInteger('module');
            $table->integer('r')->default(0);
            $table->integer('w')->default(0);
            $table->integer('u')->default(0);
            $table->integer('d')->default(0);

            $table->foreign('rol')->references('id')->on('roles');
            $table->foreign('module')->references('id')->on('modules');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}