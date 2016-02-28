<?php namespace App\Http\Controllers;

use App;
use Lang;
use Input;
use Illuminate\Http\Request;

class TranslationsController extends Controller {

    /**
     * Laravel filesystem service instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $fs;

    /**
     * Create new TranslationsController instance.
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->fs = App::make('Illuminate\Filesystem\Filesystem');
        $this->path = base_path('resources/lang');

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['except' => ['getLinesAndLocales', 'getLines']]);
        }
    }

    /**
     * Return all available translation locales and language lines.
     *
     * @return array
     */
    public function getLinesAndLocales()
    {
        $locales = [];

        foreach($this->fs->directories($this->path) as $path) {
            $name = basename($path);
            if ($name !== 'original') $locales[] = $name;
        }

        $active = App::make('Settings')->get('trans_locale', 'en');

        return [
            'locales'      => $locales,
            'lines'        => $this->fs->getRequire($this->path.'/'.$active.'/app.php'),
            'activeLocale' => $active
        ];
    }

    /**
     * Get language lines for specific locale.
     *
     * @param {string} $locale
     * @return array|null
     */
    public function getLines($locale)
    {
        $path = $this->path.'/'.$locale.'/app.php';

        if ($this->fs->exists($path)) {
            return $this->fs->getRequire($path);
        }
    }

    /**
     * Update language lines in translation file.
     *
     * @param Request $request
     */
    public function updateLines(Request $request)
    {
        $this->validate($request, [
            'lines'  => 'required|array',
            'locale' => 'required|min:2|max:50'
        ]);

        $locale = Input::get('locale');
        $path   = $this->path."/$locale/app.php";
        $lines  = Input::get('lines');

        if ($this->fs->exists($this->path."/$locale")) {

            //$newString = str_replace($this->fs->getRequire($path), $lines, $this->fs->get($path));

            $oldLines = $this->fs->get($path);

            foreach($lines as $key => $value) {
                $oldLines = preg_replace("/(\"$key\".*?=>.*?\").+?(\",)/", "\${1}$value\${2}", $oldLines);

            }

            $this->fs->put($path, $oldLines);
        }
    }

    /**
     * Reset translation lines to original for given locale.
     *
     * @param Request $request
     * @return array
     */
    public function resetTranslations(Request $request)
    {
        $this->validate($request, [
            'locale' => 'required|min:2|max:50'
        ]);

        $locale = e(Input::get('locale'));

        if ($this->fs->exists($this->path."/$locale")) {
            $this->fs->copyDirectory($this->path.'/original', $this->path.'/'.$locale);
        }

        return $this->fs->getRequire($this->path.'/'.$locale.'/app.php');
    }

    /**
     * Create a folder for new locale.
     *
     * @param Request $request
     * @return string
     */
    public function createNewLocale(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:50'
        ]);

        $this->fs->copyDirectory($this->path.'/original', $this->path.'/'.e(Input::get('name')));

        return e(Input::get('name'));
    }

    /**
     * Delete locale folder with given name.
     *
     * @param string $name
     */
    public function deleteLocale($name)
    {
        $path = $this->path.'/'.$name;

        if ($name === 'en') {
            return response(trans('app.cantDeleteEn'), 403);
        }

        //make sure we don't delete original 'en' locale
        if ($name !== 'en' && $this->fs->exists($path)) {
            $this->fs->deleteDirectory($path);
        }
    }
}
