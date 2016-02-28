<?php namespace App\Services;

use Hash;
use App\User;
use App\Genre;
use App\Artist;
use App, Artisan, Auth, Storage, Config, Session, Dotenv;

class Installer {

    /**
     * Laravel filesystem service instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $fs;

    /**
     * Create new Installer instance.
     */
    public function __construct()
    {
        $this->fs = App::make('Illuminate\Filesystem\Filesystem');
    }

    /**
	 * PHP Extensions and their expected state
	 * (enabled, disabled) in order for this 
	 * app to work properly.
	 * 
	 * @var array
	 */
	private $extensions = array(
		array('name' => 'fileinfo', 'type' => 'extension', 'expected' => true),
		array('name' => 'mbstring', 'type' => 'extension', 'expected' => true),
		array('name' => 'pdo', 'type' => 'extension', 'expected' => true),
		array('name' => 'pdo_mysql', 'type' => 'extension', 'expected' => true),
		array('name' => 'gd', 'type' => 'extension', 'expected' => true),
		array('name' => 'Mcrypt', 'type' => 'extension', 'expected' => true),
		array('name' => 'mysql_real_escape_string', 'type' => 'extension', 'expected' => false),
		array('name' => 'curl', 'type' => 'extension', 'expected' => true),
		array('name' => 'putenv', 'type' => 'function', 'expected' => true),
		array('name' => 'getenv', 'type' => 'function', 'expected' => true),
	);

	/**
	 * Directories that need to be writable.
	 * 
	 * @var array
	 */
	private $dirs = array('../assets/avatars', 'storage', 'storage/app', 'storage/framework', 'storage/logs', '../assets/css/custom-stylesheets', 'resources/lang');

	/**
	 * Holds the compatability check results.
	 * 
	 * @var array
	 */
	private $compatResults = array('problem' => false);

	/**
	 * Check for any issues with the server.
	 * 
	 * @return JSON
	 */
	public function checkForIssues()
	{
		$this->compatResults['extensions'] = $this->checkExtensions();
		$this->compatResults['folders']    = $this->checkFolders();
		$this->compatResults['phpVersion'] = $this->checkPhpVersion();

		return json_encode($this->compatResults);
	}

	/**
	 * Check if we've got required php version.
	 * 
	 * @return integer
	 */
	public function checkPhpVersion()
	{
		return version_compare(PHP_VERSION, '5.4.0');
	}

	/**
	 * Check if required folders are writable.
	 * 
	 * @return array
	 */
	public function checkFolders()
	{
		$checked = array();

		foreach ($this->dirs as $dir)
		{
            $path = base_path($dir);

		 	$writable = is_writable($path);

		 	$checked[] = array('path' => realpath($path), 'writable' => $writable);

		 	if ( ! $this->compatResults['problem']) {
		 		$this->compatResults['problem'] = $writable ? false : true;
		 	}		 	
		}
		
		return $checked;
	}

	/**
	 * Check for any issues with php extensions.
	 * 
	 * @return array
	 */
	private function checkExtensions()
	{
		$problem = false;

		foreach ($this->extensions as &$ext)
		{
			if ($ext['type'] === 'function') {
                $loaded = function_exists($ext['name']);
            } else {
                $loaded = extension_loaded($ext['name']);
            }

			//make notice if any extensions status
			//doesn't match what we need
			if ($loaded !== $ext['expected'])
			{
				$problem = true;
			}

			$ext['actual'] = $loaded;
		}

		$this->compatResults['problem'] = $problem;

		return $this->extensions;
	}

	/**
	 * Store admin account and basic details in db.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function createAdmin(array $input)
	{
		//create admin account
        $user = new User();
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->permissions = array('admin' => 1);
        $user->save();

        //login user
        Auth::login($user);

        //mark as installed
        $content = Storage::get('application/.env');

        $content = preg_replace("/(.*?INSTALLED=).*?(.+?)\\n/msi", '${1}1'."\n", $content);

        Storage::put('application/.env', $content);

        $this->importSampleData();
	}

	/**
	 * Insert db credentials if needed, create schema and seed the database.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function createDb(array $input)
	{
        $needToHandleEnvFile = ! isset($input['alreadyFilled']) || ! $input['alreadyFilled'];
        Session::put('needToHandleEnvFile', $needToHandleEnvFile);

        $this->prepareDatabaseForMigration($input);

        //$this->generateAppKey();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->putAppInProductionEnv();
	}

    /**
     * Prepare for migration by putting new db credentials
     * into already loaded config and .env file
     *
     * @param $input
     */
    private function prepareDatabaseForMigration($input)
    {
        //insert database credentials into .env file
        $this->insertDBCredentials($input);

        //load our new env variables and make sure environment is
        //local for migration/seeding as otherwise it will error out
        Dotenv::load(base_path());
        App::detectEnvironment(function(){ return 'local'; });

        //get default database connection in case user is not using mysql
        $default = Config::get('database.default');

        //set new database credentials into config so
        //existing database connection gets updated with them
        foreach($input as $key => $value) {
            if ( ! $value) $value = '';
            Config::set("database.connections.$default.$key", $value);
        }
    }

	/**
	 * Insert user supplied db credentials into .env file.
	 * 
	 * @param  array   $input
	 * @return void
	 */
	private function insertDBCredentials(array $input)
	{
        //if user has created/modified .env file manually we can bail
        if ( ! Session::get('needToHandleEnvFile', true)) return;

        $content = Storage::get('application/.env.example');

        foreach ($input as $key => $value) {
            if ( ! $value) $value = 'null';

            $content = preg_replace("/(.*?DB_$key=).*?(.+?)\\n/msi", '${1}'.$value."\n", $content);
        }

		//put new credentials in a .env file
        Storage::put('application/.env', $content);
	}

    /**
     * Generate new app key and put it into .env file.
     */
    private function generateAppKey()
    {
        //if user has created/modified .env file manually we can bail
        if ( ! Session::get('needToHandleEnvFile', true)) return;
        Session::forget('needToHandleEnvFile');

        $content = Storage::get('application/.env');

        //set app key while we're editing .env file
        $key = str_random(32);
        $content = preg_replace("/(.*?APP_KEY=).*?(.+?)\\n/msi", '${1}'.$key."\n", $content);

        Storage::put('application/.env', $content);
    }

    /**
     * Change app env to production and set debug to false in .env file.
     */
    private function putAppInProductionEnv()
    {
        //if user has created/modified .env file manually we can bail
        if ( ! Session::get('needToHandleEnvFile', true)) return;

        $content = Storage::get('application/.env');

        //set env to production
        $content = preg_replace("/(.*?APP_ENV=).*?(.+?)\\n/msi", '${1}production'."\n", $content);

        //set debug to false
        $content = preg_replace("/(.*?APP_DEBUG=).*?(.+?)\\n/msi", '${1}false'."\n", $content);

        //set base url for env
        $content = preg_replace("/(.*?BASE_URL=).*?(.+?)\\n/msi", '${1}'.url()."\n", $content);

        Storage::put('application/.env', $content);
    }

    /**
     * Import same sample artists into database.
     */
    private function importSampleData()
    {
        $paths = $this->fs->files(base_path('storage/app/sample_data'));

        foreach($paths as $path) {
            $data = json_decode($this->fs->get($path), true);

            $genreModel = Genre::where('name', basename($path, '.json'))->first();

            foreach($data as $artist) {
                $props = [];

                foreach($artist as $key => $property) {
                    if ($key === 'pivot' || $key === 'id' || str_contains($key, '_at') || $key === 'similar') continue;

                    if ($key === 'albums') {
                        $albums = $property;
                    } else if ($key === 'fully_scraped') {
                        $props[$key] = 0;
                    } else {
                        $props[$key] = $property;
                    }
                }

                //insert artist and attach to genre
                $artistModel = Artist::create($props);
                $genreModel->artists()->attach($artistModel->id);

                //format albums
                $formatted = [];
                foreach($albums as $album) {
                    $formattedAlbum = [
                        'name' => $album['name'],
                        'image'  => $album['image'],
                        'spotify_popularity'  => $album['spotify_popularity'],
                        'release_date' => $album['release_date'],
                        'tracks' => $album['tracks'],
                        'fully_scraped' => 1,
                    ];

                    //format tracks
                    $formattedTracks = [];
                    foreach($formattedAlbum['tracks'] as $track) {
                        $formattedTracks[] = [
                            'duration'   => $track['duration'],
                            'name'       => $track['name'],
                            'number'     => $track['number'],
                            'album_name' => $formattedAlbum['name'],
                            'artists'    => implode('*|*', $track['artists']),
                            'spotify_popularity' => $track['spotify_popularity'],
                        ];
                    }

                    $formattedAlbum['tracks'] = $formattedTracks;
                    $formatted[] = $formattedAlbum;
                }

                $saver = App::make('App\Services\Artist\ArtistSaver');
                $saver->saveAlbums(['albums' => $formatted], $artistModel);
                $saver->saveTracks($formatted, $artistModel);
            }
        }
    }
}