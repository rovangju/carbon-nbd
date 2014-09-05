<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

use Carbon\Carbon;
use CarbonExt\NBD\Calculator;
use CarbonExt\NBD\CoreCallbacks as CB;

/**
 * Class Issue1Test
 * https://github.com/rovangju/carbon-nbd/issues/1
 * 
 */
class Issue1Test extends TestBase {

    public function testRecurring() {

        $in = new Carbon('2015-12-31'); /* What is the next business day for Dec 31? */

        $c = new Calculator();
        
        $c->addCallback(
            CB::ignoreRecurring(array(
                new Carbon('January 1st')        
                )
            )
        );

        $this->assertEquals(
            '2016-01-02',
            $c->nbd($in)->format('Y-m-d')
        );
    }
    
    public function testComplexRecurring() {

        $dt1 = new Carbon('2016-06-30');
        $dt2 = new Carbon('2015-06-25');
        
        error_log($dt1->diffInDays($dt2));
        
        $this->markTestSkipped('TBD');
    }
}
 
