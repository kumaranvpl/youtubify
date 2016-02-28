<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimilarArtistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('similar_artists', function(Blueprint $table)  {
			$table->increments('id');
			$table->integer('artist_id')->unsigned();
			$table->integer('similar_id')->unsigned();

			$table->unique(['artist_id', 'similar_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('similar_artists');
	}

}
