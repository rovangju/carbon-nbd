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
    
    const N_FIRST = -1;
    const N_LAST = -2;

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
     * this could be solved using CoreCallbacks::ignoreNDOW(5, N_LAST, 1) 
     * 
     * @param integer $month 1 based index of month, 1 = Jan, 12 = dec
     * @param integer $nth Ignore the "nth" $dayOfWeek of the given month (1-6, FIRST, LAST)
     * @param integer $dayOfWeek Zero based index day of the week - 0 = Sunday, 6 = Saturday
     *
     * @return callable
     */
    static public function ignoreNDOW($month, $nth, $dayOfWeek) {
        
        $context = new Carbon();
        
        if ($context->month !== $month) {
            return FALSE;
        }
        
        $cmp = new Carbon($context->format('Y-m-').'01'); /* Set our walker up to the first of the context's month */
        
        $ticks = 0;
        
        for ($i = 0; $i < $context->daysInMonth; $i++) {
            
            if ($cmp->dayOfWeek == $dayOfWeek) {
                
                if ($nth == static::N_FIRST) {
                    return TRUE;
                }
                
                $ticks++;
            }
            
            if ($ticks == $nth) {
            }
            
            $cmp->addDay();
        }
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
 
