<?php namespace App\Exceptions;

use Auth;
use Input;
use Exception;
use Raven_Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException',
        'Illuminate\Database\Eloquent\ModelNotFoundException',
        'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
        if (env('ENABLE_SENTRY') && $this->shouldReport($e)) {
            $user = Auth::user();
            $client = new Raven_Client(env('RAVEN_URL'));

            if ($user) {
                $client->user_context($user->toArray());
            }

            $client->captureException($e, ['extra' => ['input' => Input::except(['password', 'password_confirmation'])]]);
        }

        return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if ($e instanceof ModelNotFoundException || $e instanceof MethodNotAllowedHttpException) {
			abort(404);
		}

		return parent::render($request, $e);
	}

}
