<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'settings';

    protected $fillable = ['name', 'value'];
}
