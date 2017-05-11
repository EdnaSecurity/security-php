<?php

/**
* 
*/

namespace Edna;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use Monolog\Logger;

class Secure
{
	/**
	 * E-DNA secret key
	 */
	protected $secretkey;

	/**
	 * Results of the API
	 */
	protected $results = null;

	/**
	 * Auth HTTP username
	 */
	protected $username = null;

	/**
	 * Auth HTTP password
	 */
	protected $password = null;

	/**
	 * E-DNA API URI
	 */
	protected $uri = "https://dash.e-dna.co/";

	/**
	 * API Requests timeout
	 */
	protected $timeout = 2;

	/**
	 * The Requests mode
	 */
	protected $mode = "monitor";

	/**
	 * Redirection option to wether redirect to our CDN or just return the results
	 */
	protected $auto_redirect = true;

	/**
	 * Enable cookies
	 */
	protected $enable_cookie = true;

	/**
	 * Cookie name
	 */
	protected $cookie_name = "_edna_sc";

	/**
	 * Cookie lifetime
	 */
	protected $cookie_lifetime = 3600;

	/**
	 * Cookie data array
	 */
	protected $cookie_array = [];

	/**
	 * Activate logger
	 */
	protected $activate_logger = false;

	/**
	 * Logger path
	 */
	protected $logger_path = '/';

	/**
	 * Logger name
	 */
	protected $logger_name = "edna-app";

	/**
	 * Saves the instance object of the class
	 */
	protected static $instance = false;

	/**
	 * Init
	 */
	private function __construct($enable_cookie)
	{
		// set the cookie anble value
		$this->enable_cookie = $enable_cookie;

		// if the cookie is enabled then save the global value
		$this->cookie_array =& $_COOKIE;
	}

	/**
	 * Creates an instance of the current class
	 */
	public static function getInstance($enable_cookie = false)
	{
		is_bool(static::$instance) ? new self($enable_cookie) : static::$instance;
	}

	/**
	 * Change settings before sending requests
	 */
	public function setSettings($uri = '', $timeout = 2, $redirect = true)
	{
		// if not empty uri then replace the default one
		! empty($uri) and $this->uri = $uri;

		// set the timeout
		is_int($timeout) and $this->timeout = $timeout;

		// change the action after finding a threat
		is_bool($redirect) and $this->auto_redirect = $redirect;

		return $this;
	}

	/**
	 * Sets the cookie cookies
	 */
	public function setCookies($name = '_edna_sc', $value = false, $expire = 3600, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		// validate the cookie name
		if ( empty($name) )
		{
			$name = $this->cookie_name;
		}
		else
		{
			$this->cookie_name = $name;
		}

		// if the cookies are enabled then set a cookie
		if ( ! array_key_exists($name, $this->cookie_array) )
		{
			setcookie($name, $value, time() + $expire, $path, $domain, $secure, $httponly);
		}

		return $this;
	}

	/**
	 * This function will set secret and public key
	 */
	public function setKeys($secret)
	{
		$this->secretkey = $secret;
	}

	/**
	 * This function will set the HTTP Auth headers
	 */
	public function setAuth($username, $password)
	{
		$this->username = $username;
		$this->password = $password;

		return $this;
	}

	/**
	 * Sends a Sync request to E-DNA API endpoint
	 */
	public function sendSync($action = "secure", array $arguments = [])
	{
		// Build the endpoint
		$uri = $this->buildUri($action);

		$arguments['secret'] = $this->secretkey;

		$client  = new Client();
        $curl    = new CurlMultiHandler();
        $handler = HandlerStack::create($curl);

		$this->results = $client->request('POST', $uri, [
		    'debug' => true,
			'query'   => $arguments,
			'auth'    => [$this->username, $this->password],
			'verify'  => false,
			'timeout' => $this->timeout
		])->getBody();

		return $this;
	}

	/**
	 * Sends an Async request to E-DNA endpoint
	 */
	public function sendAsync($action = "secure", array $arguments = [])
	{
		// Build the endpoint
		$uri = $this->buildUri($action);

		$arguments['secret'] = $this->secretkey;

        $curl    = new CurlMultiHandler();
        $handler = HandlerStack::create($curl);
        $client  = new Client(['handler' => $handler]);
        $request = new Request('POST', $uri);

		$promise = $client->sendAsync($request, [
		    'debug' => true,
			'query'   => $arguments,
			'auth'    => [$this->username, $this->password],
			'verify'  => false,
			'timeout' => $this->timeout
		])->then(function ($response) {
			$this->results = $response->getBody();
		});

		$promise->wait();

		return $this;
	}

	/**
	 * Parse the recieved results to see what action is needed
	 */
	public function _check()
	{
		if ( $results = $this->getResults() )
		{
			
		}
	}

	/**
	 * Builds the URI endpoint and returns it
	 */
	public function buildUri($action)
	{
		return $this->uri . ltrim($action, '/');
	}

	/**
	 * Gets the results of all requests
	 */
	public function getResults($array = false)
	{
		if ( is_string($this->results) )
		{
			$results = json_decode($this->results, $array);
		}
		else
		{
			$results = $this->results;
		}

		return $results;
	}
}