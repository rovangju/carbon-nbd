<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

use Carbon\Carbon;

class CallbackTest extends TestBase {

	public function testBasicCallback() {
		
		/* No matter what happens, we get FALSE */
		$cb = function(Carbon $dt) {
			return $dt->isWeekend();
		};
		
		$this->fresh->addCallback($cb);
		
		$dt = new Carbon('2014-01-04'); /* Saturday */
		$expected = '2014-01-06'; /* Monday */
		
		$response = $this->fresh->nbd($dt);
		
		$this->assertEquals(
			$expected,
			$response->format('Y-m-d')
		);
	}

	/**
	 * @medium
	 * 
	 * @expectedException \RuntimeException
	 */
	public function testMaxIters() {
		
		$cb = function() {
			return TRUE; /* Exclude ... every ... thing ... */
		};
			
		$this->fresh->addCallback($cb);
		
		$this->fresh->nbd();
		
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testNonFunction() {
		
		$cb = 'banana';
		
		$this->fresh->addCallback($cb);		
	}
	
}
 
