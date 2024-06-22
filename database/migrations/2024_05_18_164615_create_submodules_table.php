<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmodulesTable extends Migration
{
    /**
     * Run the migrations.
     *  
     * @return void
     */
    public function up()
    {
        Schema::create('submodules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules');
            $table->string('submodule', 25);
            $table->string('path', 25);
            $table->string('description', 150)->nullable();
            $table->string('icon', 150);
            $table->integer('status');
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
        Schema::dropIfExists('submodules');
    }
}
