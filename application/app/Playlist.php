<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'public'];

    protected $appends = ['is_owner'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'playlists';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
        'public' => 'integer',
    ];

    public function getIsOwnerAttribute()
    {
        $owner = 0;

        if (isset($this->attributes['owner'])) {
            $owner = $this->attributes['owner'];
        }

        if (isset($this->pivot->owner)) {
            $owner = $this->pivot->owner;
        }

        return $owner;
    }

    /**
     * Many to many relationship with user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Many to many relationship with track model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }
}
