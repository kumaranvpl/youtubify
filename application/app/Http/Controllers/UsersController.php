<?php namespace App\Http\Controllers;

use App\Services\Paginator;
use Hash;
use Auth;
use Input;
use App\User;

class UsersController extends Controller {

    /**
     * Eloquent User model instance.
     *
     * @var User
     */
    private $model;

    /**
     * Paginator Instance.
     *
     * @var Paginator
     */
    private $paginator;

    public function __construct(User $user, Paginator $paginator)
    {
        $this->middleware('admin', ['only' => ['index', 'destroy', 'destroyAll']]);
        $this->middleware('loggedIn', ['only' => ['follow', 'unfollow', 'followers']]);

        if (IS_DEMO) {
            $this->middleware('disableOnDemoSite', ['only' => ['destroy', 'destroyAll']]);
        }

        $this->model = $user;
        $this->paginator = $paginator;
    }

    /**
     * Follow user with given id.
     *
     * @param int $id
     * @return void
     */
    public function follow($id)
    {
        $user = $this->model->findOrFail($id);

        if ($user->id != Auth::user()->id) {
            Auth::user()->followedUsers()->attach($id);
        }
    }

    /**
     * UnFollow user with given id.
     *
     * @param int $id
     * @return void
     */
    public function unfollow($id)
    {
        $user = $this->model->findOrFail($id);

        if ($user->id != Auth::user()->id) {
            Auth::user()->followedUsers()->detach($id);
        }
    }

    /**
     * Fetch users followers.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function followers($id)
    {
        return $this->model->findOrFail($id)->followers()->get();
    }

    /**
     * Fetch users followed users.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function followedUsers($id)
    {
        return $this->model->findOrFail($id)->followedUsers()->get();
    }

    /**
     * Return user matching given id.
     *
     * @return User
     */
    public function show($id)
    {
        return $this->model->with(['followedUsers', 'followers', 'playlists' => function($q) {
            $q->with('tracks.album')->where('public', 1);
        }])->findOrFail($id);
    }

    /**
     * Return a collection of all registered users.
     *
     * @return Collection
     */
    public function index()
    {
        return $this->paginator->paginate($this->model, Input::all());
    }

    /**
     * Update given users information.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $currentUser = Auth::user();
        $input       = Input::all();
        $user        = User::findOrFail($id);

        if (($currentUser->isAdmin && ! IS_DEMO) || $currentUser->id == $user->id) {

            //hash the password if we get one passed in input
            if (isset($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            }

            $user->fill($input)->save();

            return response($user, 200);
        }

        return response(trans('app.noPermissions'), 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin) {
            return response(trans('app.cantDeleteAdmin'), 403);
        }

        return User::destroy($id);
    }

    /**
     * Delete all users given in input.
     *
     * return Response
     */
    public function destroyAll()
    {
        if ( ! Input::has('users') || ! Auth::user()->isAdmin) return;

        $ids = [];

        foreach(Input::get('users') as $k => $user) {
            if ($user['isAdmin'] || Auth::user()->id == $user['id']) continue;
            $ids[] = $user['id'];
        }

        if ($deleted = User::destroy($ids)) {
            return response(trans('app.deleted', ['number' => $deleted]));
        }
    }
}
