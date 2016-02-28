<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('playlist_user', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('playlist_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->boolean('owner')->unsigned()->default(1)->index();

			$table->unique(['playlist_id', 'user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('playlist_user');
	}

}
