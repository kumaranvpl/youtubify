<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('artists', function(Blueprint $table)  {
			$table->increments('id');
			$table->string('name')->unique();
			$table->integer('spotify_followers')->nullable()->unsigned();
			$table->tinyInteger('spotify_popularity')->nullable()->unsigned()->index();
			$table->string('image_small')->nullable();
			$table->string('image_large')->nullable();
			$table->boolean('fully_scraped')->default(1);
			$table->timestamp('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('artists');
	}

}
