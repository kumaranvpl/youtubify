<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistTrackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('playlist_track', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('playlist_id')->unsigned();
			$table->integer('track_id')->unsigned();

			$table->unique(['track_id', 'playlist_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('playlist_track');
	}

}
