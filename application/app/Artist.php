<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'artists';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'spotify_popularity' => 'integer',
        'fully_scraped' => 'integer',
    ];

    protected $guarded = ['id'];

    public function albums()
    {
    	return $this->hasMany('App\Album');
    }

    public function similar()
    {
        return $this->belongsToMany('App\Artist', 'similar_artists', 'artist_id', 'similar_id');
    }

    /**
     * Many to many relationship with genre model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres()
    {
        return $this->belongsToMany('App\Genre', 'genre_artist');
    }

    public function setCreatedAt($value)
    {
        //disable created_at timestap
    }
}
