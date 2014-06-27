<?php


namespace CarbonExt\NBD;


use Carbon\Carbon;

/**
 * Class CoreCallbacks
 * @package CarbonExt\NBD
 * 
 * 
 * 
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
	 * @param array $days Zero based index of days. 1 = Jan 1st, 6 = Jan 6th
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
 
