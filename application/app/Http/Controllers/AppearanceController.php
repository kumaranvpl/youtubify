<?php namespace App\Http\Controllers;

use App;
use Input;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Finder\SplFileInfo;

class AppearanceController extends Controller {

	/**
	 * Laravel filesystem service instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	private $fs;

	/**
	 * Name of the main sass file that holds all import statements.
	 *
	 * @var string
	 */
	private $mainFile = 'app.scss';

	/**
	 * Name of the sass variables file.
	 *
	 * @var string
	 */
	private $variablesFile = 'variables.scss';

	public function __construct()
	{
		$this->middleware('loggedIn');
		$this->middleware('admin');

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['except' => ['getAvailableStylesheets', 'createStylesheetArray']]);
        }

		$this->fs = App::make('Illuminate\Filesystem\Filesystem');

		$this->sassDirectory = realpath(base_path('resources/sass'));
		$this->defaultStylesheetPath = base_path('../assets/css/styles.min.css');
		$this->variablesFilePath = base_path('resources/sass/'.$this->variablesFile);
		$this->stylesheetsPath = base_path('../assets/css/custom-stylesheets');
	}

	/**
	 * Return available custom stylesheets and sass files needed for sass.js compiler.
	 *
	 * @return array
	 */
	public function getAvailableStylesheets()
	{
		$dirs = $this->fs->directories($this->stylesheetsPath);

		$sheets = [];

		foreach($dirs as $dir) {
			$sheets[] = $this->createStylesheetArray($dir);
		}

		return ['sheets' => $sheets, 'files' => $this->getSassFiles()];
	}

	/**
	 * Create stylesheet array from all the data we need in appearence editor.
	 *
	 * @param string $dir
	 * @param array|null $vars
	 * @return array
	 */
	private function createStylesheetArray($dir, $vars = null)
	{
		if ( ! $vars) {
			try {
				$vars = json_decode($this->fs->get($dir.'/variables.json'), true);
			} catch (Exception $e) {
				$vars = $this->formatVariables($this->fs->get($this->variablesFilePath))['vars'];
			}
		}

		try {
			$css  = $this->fs->get($dir.'/custom-css.css');
		} catch (Exception $e) { $css = ''; }

		return [
			'name'       => basename($dir),
			'customCss'  => $css,
			'variables'  => $vars,
			'mainColors' => $this->getMainColors($vars)
		];
	}

	/**
	 * Create a folder for new stylesheet and save its css and sass variables.
	 *
	 * @param Request $request
	 * @return string
	 */
	public function createNewStylesheet(Request $request)
	{
		$this->validate($request, ['name' => 'required|min:1|max:80']);

		//path to stylesheet directory
		$base = base_path('../assets/css/custom-stylesheets/'.Input::get('name').'/');

		if ($this->fs->isDirectory($base)) {
			return response(trans('app.stylesheetExists'), 422);
		}

		$this->fs->makeDirectory($base);

		//copy default css into stylesheets styles file
		$this->fs->put($base.'styles.min.css', $this->fs->get($this->defaultStylesheetPath));

		//get default variables for stylesheet
		$variables = $this->formatVariables($this->fs->get($this->variablesFilePath))['vars'];

		$this->fs->put($base.'variables.json', json_encode($variables));

		return $this->createStylesheetArray($base, $variables);
	}

	/**
	 * Update an existing stylesheet.
	 *
	 * @param Request $request
	 * @return string
	 */
	public function updateStylesheet(Request $request)
	{
		if (IS_DEMO) return Input::get('name');

        $this->validate($request, [
			'name'      => 'required|min:1|max:80',
			'variables' => 'required|min:100',
			'css'       => 'required|min:100'
		]);

		$base = base_path('../assets/css/custom-stylesheets/'.Input::get('name').'/');

		$this->fs->put($base.'styles.min.css', Input::get('css'));
		$this->fs->put($base.'variables.json', Input::get('variables'));

		if ($css = Input::get('customCss')) {
			$this->fs->put($base.'custom-css.css', $css);
		}

		return Input::get('name');
	}

	/**
	 * Delete directory of stylesheet matching given name.
	 *
	 * @param string $name
	 */
	public function deleteStylesheet($name)
	{
		$path = $this->stylesheetsPath.'/'.$name;

		if ($this->fs->exists($path) && $name !== 'original') {
			$this->fs->deleteDirectory($path);
		}
	}

	/**
	 * Rename stylesheet matching given name.
	 *
	 * @param string $name
	 */
	public function renameStylesheet($name)
	{
		$path = $this->stylesheetsPath.'/'.$name;

		if ($this->fs->exists($path) && $name !== 'original') {
			$this->fs->move($path, str_replace($name, Input::get('newName'), $path));
		}
	}

	/**
	 * Reset stylesheets matching given name variables to original values.
	 *
	 * @param string $name
	 * @return array
	 */
	public function resetStylesheetVariables($name)
	{
		$sheet = $this->stylesheetsPath.'/'.$name;
		$original = $this->stylesheetsPath.'/'.'original';

        if ($this->fs->exists($sheet)) {
            if ($name === 'original') {
                $this->fs->cleanDirectory($sheet);
                $this->fs->put($sheet.'/styles.min.css', $this->fs->get($this->defaultStylesheetPath));
            } else {
                $this->fs->deleteDirectory($sheet);
                $this->fs->copyDirectory($original, $sheet);
            }
        }

		$vars = $this->formatVariables($this->fs->get($this->variablesFilePath))['vars'];

		return [
			'variables' => $vars,
			'mainColors' => $this->getMainColors($vars)
		];
	}

	/**
	 * Get sass files needed for sass.js compiler to work.
	 *
	 * @return array
	 */
	private function getSassFiles()
	{
		$files = $this->fs->Allfiles($this->sassDirectory);

		$sass = ['imports' => [], 'main' => ''];

		foreach($files as $file) {
			$fileName = $this->makeFileName($file);
			$content = $this->fs->get($file->getRealPath());

			if ($fileName === $this->mainFile) {
				$sass['main'] = $content;
			} else if ($fileName === $this->variablesFile) {
				$sass['variables'] = $this->formatVariables($content);
			} else {
				$sass['imports'][$fileName] = $content;
			}
		}

		return $sass;
	}

	/**
	 * Extract main colors for a stylesheet from variables array.
	 *
	 * @param array $vars
	 * @return array
	 */
	private function getMainColors($vars)
	{
		$general = head($vars); $mainColors = [];

		foreach($general as $var) {
			if (str_contains($var['name'], 'color')) {
				$mainColors[] = $var['value'];
			}
		}

		return $mainColors;
	}

	/**
	 * Make filename from fileInfo object. Force forward slashes.
	 *
	 * @param SplFileInfo $file
	 * @return string
	 */
	private function makeFileName(SplFileInfo $file)
	{
		$name = str_replace($this->sassDirectory.DIRECTORY_SEPARATOR, '', $file->getRealPath());

		return str_replace('\\', '/', $name);
	}

	/**
	 * Format sass variables into [['name' => name, 'value' => value]...] array.
	 *
	 * @param string $string
	 * @return array
	 */
	private function formatVariables($string)
	{
		preg_match_all('/\/\/--(.+?)--.*?##(.+?)##(.+?)\/\/##/is', $string, $matches);

		$groupNames = [];

		foreach($matches[1] as $key => $groupName) {
			$groupNames[trim($groupName)] = $matches[2][$key];
		}

		$variables = [];

		$index = 0;
		foreach($groupNames as $key => $name) {
			$variables[$key] = $this->formatVariablesLines($matches[3][$index]);
			$index++;
		}

		return ['descriptions' => $groupNames, 'vars' => $variables];
	}

	/**
	 * Format sass variables string ($color:red;$width:20px) into array.
	 *
	 * @param $string
	 * @return array
	 */
	private function formatVariablesLines($string)
	{
		//match variable description(1), name(2) and value(3) in 3 separate groups.
		preg_match_all('/(?:\/\/\*\*(.+?))?\$(.+?):(.+?);/is', $string, $matches);

		$formatted = [];

		foreach($matches[0] as $key => $variable) {
			$formatted[] = [
				'name'        => trim($matches[2][$key]),
				'value'       => trim($matches[3][$key]),
				'description' => trim($matches[1][$key])
			];
		}

		return $formatted;
	}
}
