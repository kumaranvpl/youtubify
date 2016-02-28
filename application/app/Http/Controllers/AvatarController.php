<?php namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Auth, Input, Image, Storage;

class AvatarController extends Controller {

    public function __construct()
    {
        $this->middleware('loggedIn');
    }

    /**
     * Change users avatar to given one.
     *
     * @param  int|string $id
     * @param  Request $request
     *
     * @return Response
     */
    public function change($id, Request $request)
    {
        $currentUser = Auth::user();

        //check if current user is trying to change his own avatar or if he's an admin
        if ( ! $currentUser->isAdmin && $id != $currentUser->id) {
            return response(trans('app.noPermissions'), 403);
        }

        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png,jpg'
        ]);

        //resize image to 120x120 and encode as png
        $data = Image::make(Input::file('file'))->fit(120, 120, null, 'top')->encode('png');
        $path = '/assets/avatars/'.Str::random(10).'.png';
        $url  = $path;

        if ($currentUser->avatar_url) {
            $this->deleteFromFilesystem($currentUser->avatar_url);
        }

        if (file_put_contents(base_path().'/..'.$path, $data)) {
            $currentUser->avatar_url = $url;
            $currentUser->save();
            return $url;
        }

        return response(trans('app.genericError'), 500);
    }

    /**
     * Remove custom avatar from user with given id.
     *
     * @param  string|int $id
     * @return Response
     */
    public function remove($id)
    {
        $currentUser = Auth::user();

        //check if current user can remove the avatar
        if ( ! $currentUser->isAdmin && (int) $id !== $currentUser->id) {
            return response(trans('app.noPermissions'), 403);
        }

        if ($currentUser->avatar_url) {
            $this->deleteFromFilesystem($currentUser->avatar_url);
            $currentUser->avatar_url = '';
            $currentUser->save();
            return response(trans('app.avatarRemoveSuccess'), 200);
        }
    }

    /**
     * Delete avatar at given url from filesystem.
     *
     * @param string $url
     */
    public function deleteFromFilesystem($url)
    {
        @unlink(base_path().'/..'.str_replace(url(), '', $url));
    }
}
