<?php

/**
* 
*/

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use Monolog\Logger;
use Edna\Secure;

class SecureTest extends \PHPUnit_Framework_TestCase
{
	public $secret   = "aHR0cDovL3d3dy5vcnRpZ3JhLmNvbXNlY3JldA==";
	public $username = "support@reisslocal.co.uk";
	public $password = "hello2017";

	public function testRun()
	{
		// enable cookies
		$secure = Secure::getInstance(false);

		// set keys and authentication
		$secure->setKeys($this->secret)->setAuth($this->username, $this->password)->run();
	}
}