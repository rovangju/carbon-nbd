<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

require __DIR__.'/../vendor/autoload.php';

use CarbonExt\NBD\Calculator;

class TestBase extends PHPUnit\Framework\TestCase {

	/**
	 * @var Calculator
	 */
	protected $fresh;

	protected function setUp() {

		date_default_timezone_set('America/Chicago');

		$this->fresh = new Calculator();
	}

}
