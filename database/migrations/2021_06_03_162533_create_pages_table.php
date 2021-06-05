<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('filename')->unique();
            $table->unsignedInteger('volume_id');
            $table->unsignedInteger('raw_id');
            $table->unsignedInteger('clean_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->unsignedInteger('sfx_id')->nullable();
            $table->unsignedInteger('check_id')->nullable();
            $table->enum('raw',['pending','doing','done'])->default('done');
            $table->enum('clean',['pending','doing','done'])->default('pending');
            $table->enum('type',['pending','doing','done'])->default('pending');
            $table->enum('sfx',['pending','doing','done'])->default('pending');
            $table->enum('check',['pending','doing','done'])->default('pending');
            $table->foreign('volume_id')->references('id')->on('volumes')->onDelete('cascade');
            $table->foreign('raw_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('clean_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sfx_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('check_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('pages');
    }
}
