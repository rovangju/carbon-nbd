<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

use Carbon\Carbon;
use CarbonExt\NBD\NASDAQBusinessDayCalc;

class NASDAQBusinessDayCalcTest extends TestBase {

	protected function setUp() {

		date_default_timezone_set('America/New_York'); // Notice it's NY, for NASDAQ

		$this->fresh = new NASDAQBusinessDayCalc();
	}

	public function testEmptyNBDCall() {
		$nbd = $this->fresh;

		$response = $nbd->nbd();

		$this->assertInstanceOf(
			'Carbon\Carbon',
			$response
		);
	}

	public function testSimpleAddDays() {
		$nbd = $this->fresh;
		$dt = new Carbon('2016-08-26'); // Fri

		$nbdResponse = $nbd->addBusinessDays($dt, 2);
		$nbdExpected = new Carbon('2016-08-30'); // Tue

		$this->assertEquals(
			$nbdExpected->format('Y-m-d'),
			$nbdResponse->format('Y-m-d')
		);
	}

	public function testSkipsLaborDay()
	{
		$nbd = $this->fresh;
		$dt = new Carbon('2016-08-31'); // Wed before Labor Day, 2016-09-05
		$nbdResponse = $nbd->addBusinessDays($dt, 3);
		$nbdExpected = new Carbon('2016-09-06');

		$this->assertEquals(
			$nbdExpected->format('Y-m-d'),
			$nbdResponse->format('Y-m-d')
		);
	}

	public function testAddThirtyBusinessDays()
	{
		$nbd = $this->fresh;
		$dt = new Carbon('2016-08-26'); // Friday

		$nbdResponse = $nbd->addBusinessDays($dt, 30);
		$nbdExpected = new Carbon('2016-10-10'); // Mon

		$this->assertEquals(
			$nbdExpected->format('Y-m-d'),
			$nbdResponse->format('Y-m-d')
		);
	}

	public function testDeadline() {
		$nbd = $this->fresh;

		$deadline = new Carbon('4:30pm');

		$nbd->setDeadline($deadline);

		$beforeDeadline = new Carbon('4:29pm');
		$expected = new Carbon('2016-08-30');
		$response = $nbd->nbd($beforeDeadline);

		$this->assertEquals(
			$expected->format('Y-m-d'),
			$response->format('Y-m-d')
		);


		$afterDeadline = new Carbon('4:31pm');
		$expected = new Carbon('2016-08-31');
		$response = $nbd->nbd($afterDeadline);

		$this->assertEquals(
			$expected->format('Y-m-d'),
			$response->format('Y-m-d')
		);
	}
}
