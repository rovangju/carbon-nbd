<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

use Carbon\Carbon;

class CalculatorTest extends TestBase {

	public function testAddExclusion() {

		$dt = new Carbon();

		$this->fresh->addExclusion($dt);

		$this->assertSame(
			array($dt),
			$this->fresh->exclusions()
		);
	}

	public function testIsExclusionFailure() {

		$dt = new Carbon('2014-01-01');
		$dt2 = new Carbon('2014-01-02');

		$this->fresh->addExclusion($dt);

		$this->assertFalse(
			$this->fresh->isExcluded($dt2)
		);

	}

	public function testIsExclusion() {

		/* Using same object */
		$dt = new Carbon('2014-01-01 13:00:00');

		$this->fresh->addExclusion($dt);

		$this->assertTrue(
			$this->fresh->isExcluded($dt)
		);

		/* Using different object with same datetime */
		$newDt = new Carbon('January 1st, 2014 00:00:00');

		$this->assertSame(
			(string)$dt,
			(string)$newDt
		);

		$this->assertTrue(
			$this->fresh->isExcluded($newDt)
		);

	}

	public function testDeadline() {

		$this->assertNull(
			$this->fresh->deadline()
		);

		$dl = new Carbon('9:30pm');

		$this->fresh->setDeadline($dl);

		$this->assertSame(
			$this->fresh->deadline(),
			$dl
		);

	}
	
	public function testCallbacks() {
		
		$fn = function() {
			throw new \Exception('Test');
		};
		
		$this->fresh->addCallback($fn);
		
		$this->assertEquals(
			array($fn),
			$this->fresh->callbacks()
		);
	}
}
