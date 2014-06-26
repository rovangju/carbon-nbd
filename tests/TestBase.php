<?php

require __DIR__.'/../vendor/autoload.php';

use CarbonExt\NBD\Calculator;

class TestBase extends PHPUnit_Framework_TestCase {

	/**
	 * @var Calculator
	 */
	protected $fresh;

	protected function setUp() {

		date_default_timezone_set('America/Chicago');

		$this->fresh = new Calculator();
	}

}
