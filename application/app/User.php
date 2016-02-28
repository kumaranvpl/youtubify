<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password', 'first_name', 'last_name', 'username', 'avatar', 'gender'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['remember_token'];

	protected $appends = array('isAdmin', 'followers_count');

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
    ];

	public function followedUsers()
	{
		return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followed_id');
	}

	public function followers()
	{
		return $this->belongsToMany('App\User', 'follows', 'followed_id', 'follower_id');
	}

	public function getFollowersCountAttribute()
	{
		return $this->followers()->count();
	}

	/**
	 * Many to one relationship with Social model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function oauth()
	{
		return $this->hasMany('App\Social');
	}

	/**
	 * Many to many relationship with track model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function tracks()
	{
		return $this->belongsToMany('App\Track')->withTimestamps();
	}

	/**
	 * Many to many relationship with user model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function playlists()
	{
		return $this->belongsToMany('App\Playlist')->withPivot('owner');
	}

	/**
	 * Return if user this model belongs to is admin.
	 *
	 * @return bool
	 */
	public function getIsAdminAttribute()
	{
        return isset($this->permissions->admin) && (int) $this->permissions->admin === 1;
	}

	public function getPermissionsAttribute()
	{
		return isset($this->attributes['permissions']) ? json_decode($this->attributes['permissions']) : [];
	}

	public function setPermissionsAttribute($value)
	{
		$this->attributes['permissions'] = json_encode($value);
	}


	/**
	 * Return users name, if doesn't have any then return email.
	 *
	 * @return string
	 */
	public function getNameOrEmail() {
		$name = '';

		if ($this->first_name) {
			$name = $this->first_name;
		}

		if ($name && $this->last_name) {
			$name .= ' ' . $this->last_name;
		}

		if ($name) {
			return $name;
		} else {
			return $this->email;
		}
	}
}
