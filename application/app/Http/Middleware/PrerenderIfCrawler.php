<?php namespace App\Http\Middleware;

use App;
use App\Artist;
use App\Album;
use App\Track;
use App\Playlist;
use Closure;
use Illuminate\Http\Request;

class PrerenderIfCrawler  {

    private $userAgents = [
        'baiduspider',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest',
        'developers.google.com/+/web/snippet',
        'googlebot',
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
        if ($this->shouldPrerender($request)) {
            $model = $this->getShareableModel($request);

            if ( ! $model) abort(404);

            return view('view-for-crawlers')->with('model', $model['model'])->with('type', $model['type'])->with('settings', App::make('Settings'));
        }

        return $next($request);
	}

    /**
     * Fetch shareable model from db based on route params.
     *
     * @param Request $request
     * @return Artist
     */
    private function getShareableModel(Request $request)
    {
        $parts = $request->segments();

        if ($parts[0] === 'artist') {
            return ['type' => 'artist', 'model' => Artist::where('name', urldecode($parts[1]))->firstOrFail()];
        } else if ($parts[0] === 'album') {
            $album = Album::where('name', urldecode($parts[2]))->whereHas('artist', function($q) use($parts) {
                $q->where('name', urldecode($parts[1]));
            })->first();

            return ['type' => 'album', 'model' => $album];
        } else if ($parts[0] === 'playlist') {
            return ['type' => 'playlist', 'model' => Playlist::findOrFail($parts[1])];
        } else if ($parts[0] === 'track') {
            return ['type' => 'track', 'model' => Track::findOrFail($parts[1])];
        }
    }

    /**
     * Returns whether the request must be prerendered server side for crawler.
     *
     * @param Request $request
     * @return bool
     */
    private function shouldPrerender(Request $request)
    {
        $userAgent   = strtolower($request->server->get('HTTP_USER_AGENT'));
        $bufferAgent = $request->server->get('X-BUFFERBOT');

        $shouldPrerender = false;

        if (!$userAgent) return false;

        if (!$request->isMethod('GET')) return false;

        //google bot
        if ($request->query->has('_escaped_fragment_')) $shouldPrerender = true;

        //other crawlers
        foreach ($this->userAgents as $crawlerUserAgent) {
            if (str_contains($userAgent, strtolower($crawlerUserAgent))) {
                $shouldPrerender = true;
            }
        }

        if ($bufferAgent) $shouldPrerender = true;

        if (!$shouldPrerender) return false;

        return true;
    }

}
