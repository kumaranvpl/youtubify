<?php namespace App\Http\Middleware;

use Closure, Auth;

class isAdmin {

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin) {
            if ($request->ajax()) {
                return response(trans('app.noPermissions'), 403);
            } else {
                return redirect('/');
            }
        }

        return $next($request);
    }
}