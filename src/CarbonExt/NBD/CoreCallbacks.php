<?php
/**
 * Carbon Extension: Next Business Day (NBD)
 * @license GPLv2
 * @author Justin Rovang <generate@itnobody.com>
 */

namespace CarbonExt\NBD;

use Carbon\Carbon;

/**
 * A small library of pre-packaged callbacks for convenience.
 * Each method returns a callable method for use as a callback.
 */
abstract class CoreCallbacks {

	/**
	 * Exclude weekends from NBD calculation
	 * 
	 * @return callable
	 */
	static public function noWeekends() {
		return function(Carbon $dt) {
			return $dt->isWeekend();
		};
	}

    /**
     *
     * Exclude certain date strings by their month and day; IGNORING the year.
     *
     * @param Carbon[] $recurring
     *
     * @throws \InvalidArgumentException
     * @return callable
     */
    static public function ignoreRecurring(array $recurring) {
        
        $idx = array();
        
        foreach ($recurring as $r) {
            if (($r instanceof Carbon) == FALSE) {
                throw new \InvalidArgumentException('Array of Carbon object expected');
            }
            $idx[] = $r->month.$r->day;
        }
        
        return function(Carbon $dt) use ($idx) {
            return in_array(
                $dt->month.$dt->day,
                $idx
            );
        };
    }

    /**
     * Exclude a more complicated method for something like a holiday, e.g.: Memorial Day - "Last Monday in May"
     * this could be solved using CoreCallbacks::ignoreNDOW(5, -1, 1) 
     * 
     * @param integer $month 1 based index of month, 1 = Jan, 12 = dec
     * @param integer $nth Ignore the "nth" $dayOfWeek of the given month (1-6, To get 'LAST' $dayOfWeek, use -1)
     * @param integer $dayOfWeek Zero based index day of the week - 0 = Sunday, 6 = Saturday
     *
     * @return callable
     */
    static public function ignoreNDOW($month, $nth, $dayOfWeek) {
        
        return function(Carbon $context) use ($month, $nth, $dayOfWeek) {

            if ($context->month !== $month) {
                return FALSE;
            }

            $cmp = new Carbon($context->format('Y-m-').'01'); /* Set our walker up to the first of the context's month */

            $ticks = 0;

            /**
             * @var $stack Carbon[]
             */
            $stack = []; /* Used to track 'nth' */

            for ($i = 0; $i < $context->daysInMonth; $i++) {

                if ($cmp->dayOfWeek == $dayOfWeek) {

                    $stack[] = clone $cmp;

                    $ticks++;

                    if ($cmp->eq($context)) {

                        /* "FIRST" or generic 'nth' */
                        if ($nth == $ticks) {
                            return TRUE;
                        }
                    }
                }

                $cmp->addDay();
            }

            /* For brevity, checking for the -1 anyways */
            if ($nth == -1) {

                /**
                 * @var $last Carbon
                 */
                $last = array_pop($stack);
                return $last->eq($context);
            }

            return FALSE;
            
        };

    }

	/**
	 * Exclude certain days of the week from NBD calculation
	 * 
	 * @param array $days Zero based index of days. 0 = Sunday, 6 = Saturday
	 * 
	 * @return callable
	 */
	static public function ignoreDaysOfWeek(array $days) {
		return function(Carbon $dt) use ($days) {
			return in_array(
				$dt->dayOfWeek,
				$days
			);
		};
	}

	/**
	 * Exclude certain days of the year from NBD calculation
	 *
	 * @param array $days Zero based index of days. 0 = Jan 1st, 364 = Dec 31st
	 *
	 * @return callable
	 */
	static public function ignoreDaysOfYear(array $days) {

		return function(Carbon $dt) use ($days) {
			return in_array(
				$dt->dayOfYear,
				$days
			);
		};
	}

	/**
	 * Exclude certain days of the month from NBD calculation
	 *
	 * @param array $days 1 based index of days. 1 = Jan 1st, 6 = Jan 6th
	 *
	 * @return callable
	 */
	static public function ignoreDaysOfMonth(array $days) {
		
		return function(Carbon $dt) use ($days) {
			return in_array(
				$dt->day,
				$days
			);
		};
	}
	
}
 
