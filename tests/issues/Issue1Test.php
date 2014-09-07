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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadRecurringArray() {

        $c = new Calculator();

        $c->addCallback(
            CB::ignoreRecurring(array('A string?')
            )
        );
        
    }

    
    public function testNDOW() {

        $in = new Carbon('2014-09-01'); 

        $c = new Calculator();

        /* Throwing this in for code coverage */
        $c->addCallback(
            CB::ignoreNDOW(1, 1, 1)
        );

        /* Skip first tuesday */
        $c->addCallback(
            CB::ignoreNDOW(9, 1, 2)
        );

        $c->addCallback(
            CB::ignoreNDOW(9, 1, 3)
        );

        $this->assertEquals(
            '2014-09-04',
            $c->nbd($in)->format('Y-m-d')
        );
    }
    
    public function testNDOWLast() {

        /* ignoreNDOW */
        
        $in = new Carbon('2015-05-24'); /* Day before - NBD should skip 25th */
        
        $c = new Calculator();

        $c->addCallback(
            CB::ignoreNDOW(5, -1, 1)
        );

        $this->assertEquals(
            '2015-05-26',
            $c->nbd($in)->format('Y-m-d')
        );
    }
}
 
