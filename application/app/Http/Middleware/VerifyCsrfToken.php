<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	protected $except_urls = [
		'get-artist',
		'get-album',
		'get-artist-top-tracks',
        'get-lyrics',
        'track',
        'radio',
        'artist'
    ];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if (in_array($request->segment(1), $this->except_urls)) {
			return $next($request);
		}

		return parent::handle($request, $next);
	}

}
