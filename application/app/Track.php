<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model {

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'youtube_id', 'artists', 'duration', 'album_name'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tracks';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'album_id' => 'integer',
        'number'   => 'integer',
        'spotify_popularity' => 'integer',
    ];

    /**
     * Turn artists from string to array. *|* is a delimiter.
     *
     * @param string $artists
     * @return array
     */
    public function getArtistsAttribute($artists)
    {
        return explode('*|*', $artists);
    }

    /**
     * Many to many relationship with user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    /**
     * Many to one relationship with album model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    /**
     * Many to many relationship with playlist model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function playlists()
    {
        return $this->belongsToMany('App\Playlist')->withPivot('position');
    }
}
