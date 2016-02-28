<?php namespace App\Services;

use Auth;
use Cache;
use Storage;
use Exception;
use App\Setting;
use Carbon\Carbon;

class Settings {

    /**
     * Array of all settings.
     *
     * @var array
     */
    private $all = [];

    /**
     * Settings that should not be loaded or replaced in .env file.
     *
     * @var array
     */
    private $skipFromEnv = ['DB_', 'APP_KEY', 'APP_ENV'];

    /**
     * Create a new settings service instance.
     *
     * @param Setting $settingModel
     */
    public function __construct(Setting $settingModel)
    {
        $this->model = $settingModel;
        $this->loadAllSettings();
    }

    /**
     * Load settings from database and env file and return them.
     *
     * @return array
     */
    private function loadAllSettings()
    {
        if (Auth::user() && Auth::user()->is_admin) {
            $this->all = Cache::get('settings.all.admin');

            if ($this->all && ! empty($this->all)) return;

            $this->all = $this->loadDBSettings();
            $this->all['env'] = $this->loadEnvSettings();

            if ($this->all && $this->all['env'] && ! empty($this->all['env'])) {
                Cache::put('settings.all.admin', $this->all, Carbon::now()->addDays(1));
            }
        } else {
            $this->all = Cache::get('settings.all.user');

            if ($this->all && ! empty($this->all)) return;

            $this->all = $this->loadDBSettings();

            if ($this->all && ! empty($this->all)) {
                Cache::put('settings.all.user', $this->all, Carbon::now()->addDays(1));
            }
        }
    }

    /**
     * Load all settings from database.
     *
     * @return array
     */
    private function loadDBSettings()
    {
        try {
            return $this->model->lists('value', 'name');
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Load all settings from .env file.
     *
     * @return array
     */
    private function loadEnvSettings()
    {
        $lines    = file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filtered = [];

        foreach ($lines as $line) {
            // Disregard comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            // Only use non-empty lines that look like setters
            if (strpos($line, '=') !== false && ! str_contains($line, $this->skipFromEnv)) {
                list($name, $value) = explode('=', $line, 2);
                $value = trim($value);
                $filtered[trim(strtolower($name))] = $value === 'null' ? '' : $value;
            }
        }

        return $filtered;
    }

    /**
     * Get all available settings from database and .env file.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * Get a setting by key or return default.
     *
     * @param string $key
     * @param string|null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->all[$key])) {
            if ($this->all[$key] === '0' || $this->all[$key] === '1') {
                return (int) $this->all[$key];
            } else {
                return $this->all[$key];
            }
        }

        return $default;
    }

    /**
     * Set multiple settings.
     *
     * @param $settings
     */
    public function setAll($settings) {
        foreach($settings as $name => $value) {
            if ($name === 'env' && is_array($value)) {
                foreach($value as $envName => $envValue) {
                    $this->set($envName, $envValue, true);
                }
            } else {
                $this->set($name, $value);
            }
        }
    }

    /**
     * Set single setting.
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $writeToEnv
     *
     * @return Setting|void
     */
    public function set($key, $value, $writeToEnv = false)
    {
        If ($this->get('installed') && ! Auth::user()->isAdmin) abort(403);

        if (isset($this->all['env']) && (in_array($key, $this->all['env']) || $writeToEnv)) {
            $this->writeToEnvFile($key, $value);
            return;
        }

        $setting = Setting::where('name', $key)->first();

        if ( ! $setting) {
            $setting = new Setting(['name' => $key]);
        }

        $setting->value = $value;
        $setting->save();

        Cache::forget('settings.all.admin');
        Cache::forget('settings.all.user');

        return $setting;
    }

    /**
     * Remove setting with given key.
     *
     * @param {string} $key
     */
    public function remove($key)
    {
        If ($this->get('installed') && ! Auth::user()->isAdmin) abort(403);

        Setting::where('name', $key)->delete();

        Cache::forget('settings.all.admin');
        Cache::forget('settings.all.user');
    }

    /**
     * Write given setting to .env file.
     *
     * @param $key
     * @param $value
     *
     * @return void
     */
    private function writeToEnvFile($key, $value)
    {
        if ( ! $value) $value = 'null';

        $key = strtoupper($key);

        $content = Storage::get('application/.env');

        if (str_contains($content, $key.'=')) {
            $content = preg_replace("/(.*?$key=).*?(.+?)\\n/msi", '${1}'.$value."\n", $content);
        } else {
            $content = preg_replace("/(#END)/msi", "$key=$value\n".'${1}'."\n", $content);
        }

        Storage::put('application/.env', $content);
    }
}
