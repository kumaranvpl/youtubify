<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model {

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'albums';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'artist_id'     => 'integer',
        'fully_scraped'  => 'integer',
        'spotify_popularity' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'release_date', 'image', 'spotify_popularity'];

    public function artist()
    {
    	return $this->belongsTo('App\Artist');
    }

    public function tracks()
    {
    	return $this->hasMany('App\Track')->orderBy('number');
    }
}
