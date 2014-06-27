<?php

use CarbonExt\NBD\CoreCallbacks as C;
use Carbon\Carbon;

class CoreCallbacksTest extends TestBase {

	public function testFactoryApproach() {
	
		$this->fresh->addCallback(
			C::noWeekends()
		);

		$dt = new Carbon('2014-01-04'); /* Saturday */
		
		$result = $this->fresh->nbd($dt);
		
		$this->assertEquals(
			'2014-01-06',
			$result->format('Y-m-d')
		);
		
	}

	public function testNoWeekends() {

		$cb = C::noWeekends();

		$dt = new Carbon('Monday');
		$this->assertFalse($cb($dt));

		$dt = new Carbon('Saturday');
		$this->assertTrue($cb($dt));

	}

	public function testIgnoreDaysOfWeek() {

		$cb = C::ignoreDaysOfWeek(array(6,0)); /* Saturday, Sunday */

		$dt = new Carbon('Monday');
		$this->assertFalse($cb($dt));

		$dt = new Carbon('Saturday');
		$this->assertTrue($cb($dt));

	}

	public function testIgnoreDaysOfYear() {

		$cb = C::ignoreDaysOfYear(array(0, 364));

		$dt = new Carbon('2014-01-02');
		$this->assertFalse($cb($dt));

		$dt = new Carbon('2014-01-01');
		$this->assertTrue($cb($dt));

		$dt = new Carbon('2014-12-31');
		$this->assertTrue($cb($dt));

	}
	
	public function testIgnoreDaysOfMonth() {

		$cb = C::ignoreDaysOfMonth(array(1,6));

		$dt = new Carbon('2014-01-02');
		$this->assertFalse($cb($dt));

		$dt = new Carbon('2014-01-01');
		$this->assertTrue($cb($dt));

		$dt = new Carbon('2014-01-06');
		$this->assertTrue($cb($dt));
		
	}
	
}
 
