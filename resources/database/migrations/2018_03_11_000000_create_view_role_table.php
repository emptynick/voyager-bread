<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing bread rows
        Schema::create('bread_view_role', function (Blueprint $table) {
            $table->integer('bread_view_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->enum('action', ['browse', 'read', 'edit', 'add']);

            $table->foreign('bread_view_id')->references('id')->on('bread_views');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bread_view_role');
    }
}
