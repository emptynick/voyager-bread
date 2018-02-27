<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing breads
        Schema::create('bread', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name')->unique();
            $table->string('slug')->unique();
            $table->string('display_name_singular');
            $table->string('display_name_plural');
            $table->string('icon')->nullable();
            $table->string('model_name')->nullable();
            $table->string('policy_name')->nullable();
			$table->string('controller_name')->nullable();
			$table->integer('browse_list')->nullable()->unsigned();
			$table->integer('read_view')->nullable()->unsigned();
			$table->integer('add_view')->nullable()->unsigned();
			$table->integer('edit_view')->nullable()->unsigned();
            $table->timestamps();
        });

        // Create table for storing bread views
        Schema::create('bread_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bread_id')->unsigned();
            $table->string('view_type')->default('view');
            $table->string('name');
            $table->timestamps();

            $table->foreign('bread_id')->references('id')->on('bread')
                ->onUpdate('cascade')->onDelete('cascade');
        });

		// Create table for storing bread rows
        Schema::create('bread_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bread_view_id')->unsigned();
			$table->string('field')->nullable();
			$table->string('type');
			$table->integer('order')->nullable()->unsigned();
			$table->integer('width')->nullable()->unsigned();
			$table->text('options')->nullable();
            $table->text('validation_rules')->nullable();
            $table->timestamps();

			$table->foreign('bread_view_id')->references('id')->on('bread_views')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create table for Roles <-> Views Relationship
        /*Schema::create('bread_view_role', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->integer('bread_view_id')->unsigned()->index();
            $table->foreign('bread_view_id')->references('id')->on('bread_views');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bread');
        Schema::drop('bread_views');
        Schema::drop('bread_rows');
        /*Schema::drop('bread_view_role');*/
    }
}
