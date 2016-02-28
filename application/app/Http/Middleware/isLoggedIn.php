<?php namespace App\Http\Middleware;

use Closure, Auth;

class isLoggedIn {

    public function handle($request, Closure $next)
    {
        if ( ! Auth::check()) {
            if ($request->ajax()) {
                return response(trans('app.noPermissions'), 403);
            } else {
                return redirect('/');
            }
        }

        return $next($request);
    }
}