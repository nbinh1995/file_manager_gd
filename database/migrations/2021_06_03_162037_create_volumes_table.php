<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volumes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('book_id');
            $table->string('filename');
            $table->text('path');
            $table->enum('status',['pending','completed'])->default('pending');
            $table->timestamps();
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volumes');
    }
}
