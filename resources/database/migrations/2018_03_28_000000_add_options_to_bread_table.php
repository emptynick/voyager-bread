<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToBreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing breads
        Schema::table('bread', function (Blueprint $table) {
            $table->text('options')->nullable()->after('controller_name');
        });

        Schema::table('bread_views', function (Blueprint $table) {
            $table->text('options')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bread', function (Blueprint $table) {
            $table->dropColumn('options');
        });

        Schema::table('bread_views', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
