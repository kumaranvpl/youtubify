<?php

use Illuminate\Database\Migrations\Migration;

class changeFullyScrapedDefault extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $prefix = DB::getTablePrefix();
        DB::statement('ALTER TABLE '.$prefix.'albums MODIFY COLUMN fully_scraped TINYINT(1) NOT NULL DEFAULT 0;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}