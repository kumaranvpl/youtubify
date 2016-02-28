<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('albums', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('release_date')->nullable()->index();
			$table->string('image')->nullable();
			$table->integer('artist_id')->unsigned()->default(0)->index();
			$table->tinyInteger('spotify_popularity')->nullable()->index();
			$table->boolean('fully_scraped')->default(1);

			$table->unique(['name', 'artist_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('albums');
	}

}
