<?php namespace App\Console\Commands;

use DB;
use Hash;
use App\User;
use App\Playlist;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class ResetAdmin extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'reset-admin-account';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset admin account';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$admin = User::where('email', 'admin@admin.com')->firstOrFail();

        $admin->avatar_url = '';
        $admin->username = '';
        $admin->password = Hash::make('admin');
        $admin->save();
        $admin->tracks()->detach();
        $ids = $admin->playlists()->wherePivot('owner', 1)->select('playlists.id')->get();
        $ids = $ids->map(function($model) { return $model->id; })->toArray();

        Playlist::whereIn('id', array_values($ids))->delete();
        DB::table('playlist_track')->whereIn('playlist_id', $ids)->delete();
        DB::table('playlist_user')->whereIn('playlist_id', $ids)->delete();
	}

}
