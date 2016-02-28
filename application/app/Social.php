<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Social extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_oauth';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['service', 'token', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
