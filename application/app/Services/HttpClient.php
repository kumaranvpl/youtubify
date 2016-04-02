<?php namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ParseException;

class HttpClient {

	private $client;
	private $showFeedback;

	public function __construct($options = [], $showFeedback = false)
	{
		$options['timeout'] = 8.0;
		$options['exceptions'] = false;



		$this->client = new Client($options);
		$this->showFeedback = $showFeedback;
	}

	public function get($url, $options = [])
	{
        $r = $this->client->get($url, array_merge($options, ['exceptions' => false]));

		if ($r->getStatusCode() === 429 && $r->hasHeader('Retry-After')) {
			$seconds = $r->getHeader('Retry-After') ? $r->getHeader('Retry-After') : 5;
			$this->feedback('Hit rate limit, sleeping for '.$seconds.' sec.');
			sleep($seconds);
			$this->feedback('Retrying call, to: '.$url);
			$r = $this->get($url);
		}

        try {
            $json = is_array($r) ? $r : $r->json();
        } catch (ParseException $e) {
            $json = '';
        }

		return $json;
	}

	public function post($url, $options)
	{
		$r = $this->client->post($url, array_merge($options, ['exceptions' => false]));

		if ($r->getStatusCode() === 429 && $r->hasHeader('Retry-After')) {
			$seconds = $r->getHeader('Retry-After') ? $r->getHeader('Retry-After') : 5;
			$this->feedback('Hit rate limit, sleeping for '.$seconds.' sec.');
			sleep($seconds);
			$this->feedback('Retrying call, to: '.$url);
			$r = $this->get($url);
		}

		return is_array($r) ? $r : $r->json();
	}

	public function feedback($msg)
	{
		if ( ! $this->showFeedback) return;

		$msg = $msg."<br>";
		echo $msg;
		flush();

		$levels = ob_get_level();
		for ($i=0; $i<$levels; $i++)
			ob_end_flush();
	}
}