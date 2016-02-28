<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Album
 *
 * @property-read \App\Artist $artist
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Track[] $tracks
 */
	class Album {}
}

namespace App{
/**
 * App\Artist
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Album[] $albums
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Artist[] $similar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Genre[] $genres
 */
	class Artist {}
}

namespace App{
/**
 * App\Genre
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Artist[] $artists
 */
	class Genre {}
}

namespace App{
/**
 * App\Playlist
 *
 * @property-read mixed $is_owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Track[] $tracks
 */
	class Playlist {}
}

namespace App{
/**
 * App\Setting
 *
 */
	class Setting {}
}

namespace App{
/**
 * App\Social
 *
 * @property-read \App\User $user
 */
	class Social {}
}

namespace App{
/**
 * App\Track
 *
 * @property-read mixed $artists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \App\Album $album
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Playlist[] $playlists
 */
	class Track {}
}

namespace App{
/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Social[] $oauth
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Track[] $tracks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Playlist[] $playlists
 * @property-read mixed $is_admin
 */
	class User {}
}

