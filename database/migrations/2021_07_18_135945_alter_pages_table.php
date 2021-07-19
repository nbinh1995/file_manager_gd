<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function($table)
        {
            $table->string('raw_image')->nullable()->after('check');
            $table->string('clean_image')->nullable()->after('check');
            $table->string('type_image')->nullable()->after('check');
            $table->string('sfx_image')->nullable()->after('check');
            // $table->string('check_image')->nullable()->after('check');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
