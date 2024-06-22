<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsSubModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions_submodules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rol');
            $table->unsignedBigInteger('submodule');
            $table->integer('r')->default(0);
            $table->integer('w')->default(0);
            $table->integer('u')->default(0);
            $table->integer('d')->default(0);

            $table->foreign('rol')->references('id')->on('roles');
            $table->foreign('submodule')->references('id')->on('submodules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions_submodules');
    }
}
