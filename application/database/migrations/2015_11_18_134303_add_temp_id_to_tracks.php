<?php

use Illuminate\Database\Migrations\Migration;

class AddTempIdToTracks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tracks', function($table)
		{
    		$table->string('temp_id', 8)->index()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tracks', function($table)
		{
		    $table->dropColumn('temp_id');
		});
	}

}