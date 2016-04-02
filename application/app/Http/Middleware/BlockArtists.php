<?php namespace App\Http\Middleware;

use App;
use Route;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class BlockArtists extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($artists = App::make('Settings')->get('blockedArtists')) {
            $artists = explode("\n", $artists);
            $artist  = str_replace('+', ' ', $request->segment(2));

            if ($request->segment(1) === 'artist' && $this->shouldBeBlocked($artist, $artists)) {
                abort(404);
            }
		}

        return $next($request);
	}

    /**
     * Check if given artist should be blocked.
     *
     * @param string $name
     * @param array $toBlock
     * @return boolean
     */
    private function shouldBeBlocked($name, $toBlock)
    {
        foreach ($toBlock as $blockedName) {
            $pattern = '/' . str_replace('*', '.*?', strtolower($blockedName)) . '/i';
            if (preg_match($pattern, $name)) return true;
        }
    }

}
