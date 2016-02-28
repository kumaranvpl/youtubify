<?php namespace App\Http\Controllers;

use App;
use Input;

class MailController extends Controller {

    /**
     * Laravel filesystem service instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $fs;

    /**
    * Create new MailController instance.
    */
    public function __construct()
    {
        $this->middleware('loggedIn');
        $this->middleware('admin');

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['only' => ['saveTemplate']]);
        }

        $this->fs  = App::make('Illuminate\Filesystem\Filesystem');
        $this->dir = base_path('resources/views/emails');
    }

    /**
     * Fetch contents of all mail templates.
     *
     * @return array
     */
	public function getTemplates()
    {
	    $templates = [];

        $paths = $this->fs->files($this->dir);

        foreach($paths as $path) {
            if ( ! str_contains($path, 'original')) {
                $templates[basename($path, '.blade.php')] = $this->fs->get($path);
            }
        }

        return $templates;
	}

    /**
     * Save mail template.
     *
     * @param string $name
     */
    public function saveTemplate($name)
    {
        $path = $this->dir."/$name.blade.php";

        //backup original email template if isn't backed up already
        if ( ! $this->fs->exists($this->dir."/$name-original.blade.php")) {
            $this->fs->put($this->dir."/$name-original.blade.php", $this->fs->get($path));
        }

        $this->fs->put($path, Input::get('content'));
    }
}
