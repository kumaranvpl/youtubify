<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreArtist extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('genre_artist', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('genre_id');
			$table->integer('artist_id');
			$table->timestamps();

			$table->unique(['artist_id', 'genre_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('genre_artist');
	}

}
