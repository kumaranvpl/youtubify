<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		User::insert([
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
           ['username' => 'User-'.rand(1,20), 'first_name' => 'John', 'last_name' => 'Doe', 'avatar_url' => 'https://www.google.lt/images/srpr/logo11w.png', 'gender' => 'male', 'email' => 'test@test'.rand(1,10000).'.com', 'password' => 'xxx'],
        ]);
	}

}
