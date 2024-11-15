<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('app_id')->constrained('aurora_apps');
            $table->string('module', 25);
            $table->string('path', 25);
            $table->string('description', 150)->nullable();
            $table->string('icon', 150)->nullable()->default('<i class="fas fa-circle"></i>');
            $table->integer('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
