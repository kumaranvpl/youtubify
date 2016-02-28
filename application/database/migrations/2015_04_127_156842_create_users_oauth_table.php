<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersOauthTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_oauth', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('service');
			$table->string('token')->unique();
			$table->timestamps();

			$table->index('user_id');
			$table->unique(array('user_id', 'service'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_oauth');
	}

}
