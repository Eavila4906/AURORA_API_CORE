<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('aurora_apps');
            $table->foreignId('submodule_id')->constrained('submodules');
            $table->string('item', 25);
            $table->string('path', 25);
            $table->string('description', 150)->nullable();
            $table->string('icon', 150)->nullable()->default('<i class="fas fa-circle"></i>');
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
        Schema::dropIfExists('items');
    }
}
