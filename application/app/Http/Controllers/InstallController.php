<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Services\Installer;
use Input, PDO, Exception, Auth;

class InstallController extends Controller {

    /**
     * Installer instance.
     * 
     * @var App\Services\Installer
     */
    private $installer;

    /**
     * Create new InstallController instance.
     */
	public function __construct(Installer $installer)
	{
        $this->installer = $installer;
	}

    public function index()
    {
        return view('install/main');
    }

    /**
     * Check for any compatability issues.
     * 
     * @return array
     */
    public function compat()
    {
        return $this->installer->checkForIssues();
    }

    /**
     * Create database schema.
     * 
     * @return Response
     */
    public function createDb()
    {
        $input = Input::all();
        
        if ( ! Input::get('alreadyFilled'))
        {
            $db =  'mysql:host='.$input['host'].';dbname='.$input['database'];
        
            //test db connection with user supplied credentials
            try {
                $conn = new PDO($db, $input['username'], $input['password']);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(\PDOException $e) {
                return response($e->getMessage(), 403);
            }
        }

        //create database schema
        try {
            $this->installer->createDb($input);
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }

        return response('Database created and seeded successfully.', 200);
    }

    /**
     * Store basic site information and admin account
     * details to database.
     * 
     * @return mixed
     */
    public function createAdmin()
    {
        if ( ! Input::get('email')) {
            return response('Email field is required', 403);
        }

        if ( ! Input::get('password')) {
            return response('Password field is required.', 403);
        }

        if (Input::get('password') !== Input::get('confirmPassword')) {
            return response('Passwords do not match.', 403);
        }

        try {
            $this->installer->createAdmin(Input::all());
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}