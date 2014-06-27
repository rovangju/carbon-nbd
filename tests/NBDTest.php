<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

use Carbon\Carbon;

class NBDTest extends TestBase {

	public function testEmptyNBDCall() {
		$nbd = $this->fresh;

		$response = $nbd->nbd();

		$this->assertInstanceOf(
			'Carbon\Carbon',
			$response
		);
	}

	public function testReferenceAvoidance() {

		$nbd = $this->fresh;

		$dt = new Carbon('2014-01-04');
		$dtClone = clone $dt;

		$nbd->nbd($dt);

		/* Ensure original haven't been overwritten */
		$this->assertSame(
			(string)$dt,
			(string)$dtClone->addDay()
		);
	}

	public function testSimpleExclusion() {

		$nbd = $this->fresh;

		$nbd->addExclusion(new Carbon('2014-01-05')); /* Sun */

		$dt = new Carbon('2014-01-04'); /* Sat */

		$nbdResponse = $nbd->nbd($dt);


		/* Might as well confirm vanilla NBD is Monday */
		$nbdExpected = new Carbon('2014-01-06');

		$this->assertEquals(
			$nbdExpected->format('Y-m-d'),
			$nbdResponse->format('Y-m-d')
		);
	}

	public function testDeadline() {

		$nbd = $this->fresh;

		$deadline = new Carbon('2014-01-01 13:00:00');

		$nbd->setDeadline($deadline);

		$beforeDeadline = new Carbon('2014-01-01 12:59:59');
		$expected = new Carbon('2014-01-01');

		$response = $nbd->nbd($beforeDeadline);

		$this->assertEquals(
			$expected->format('Y-m-d'),
			$response->format('Y-m-d')
		);


		$afterDeadline = new Carbon('2014-01-01 13:00:01');
		$expected = new Carbon('2014-01-02');

		$response = $nbd->nbd($afterDeadline);

		$this->assertEquals(
			$expected->format('Y-m-d'),
			$response->format('Y-m-d')
		);
	}
}
