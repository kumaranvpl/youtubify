<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'genres';

    protected $guarded = ['id'];

    /**
     * Many to many relationship with artist model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Artist', 'genre_artist');
    }
}
