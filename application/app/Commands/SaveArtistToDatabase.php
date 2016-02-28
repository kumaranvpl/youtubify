<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use App\Services\Artist\ArtistSaver;

class SaveArtistToDatabase extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	/**
	 * Artist data.
	 * 
	 * @var array
	 */
	private $data;

	/**
	 * Create new SaveArtistToDatabase command instance.
	 * 
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(ArtistSaver $saver)
	{
		$saver->save($this->data);
	}

}
