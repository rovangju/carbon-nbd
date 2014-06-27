<?php

namespace CarbonExt\NBD;

use Carbon\Carbon;

/**
 * Class Calculator
 * @package CarbonExt\NBD
 */
class Calculator {
	
	static $N_MAX_ITER = 365; /* Limit how many days to keep 'bumping' the next business day candidate up to */

	protected $exclusions = array();
	protected $callbacks = array();

	/**
	 * @var Carbon
	 */
	protected $deadline;

	/**
	 * Add a date to exclude as a business day
	 *
	 * @param $exclusion string|Carbon
	 */
	public function addExclusion(Carbon $exclusion) {
		$exclusion->setTime(0,0,0);
		$this->exclusions[] = $exclusion;
	}

	/**
	 * Add a callaback to evaluate as an exclusion element
	 * 
	 * @param $callable
	 * @throws \InvalidArgumentException
	 */
	public function addCallback($callable) {
		
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException('Argument must be callable');
		}
		
		$this->callbacks[] = $callable;
	}

	/**
	 * Get callbacks regsitered
	 * 
	 * @return array
	 */
	public function callbacks() {
		return $this->callbacks;
	}

	/**
	 * Retrieve exclusions
	 *
	 * @return array|Carbon[]
	 */
	public function exclusions() {
		return $this->exclusions;
	}

	/**
	 * Determine if a date is a non-business day
	 *
	 * @param Carbon $dt
	 * @return bool
	 */
	public function isExcluded(Carbon $dt) {

		foreach ($this->exclusions() as $exc) {

			if ($dt->eq($exc)) {
				return TRUE;
			}
		}
		
		foreach ($this->callbacks() as $fn) {
			if ($fn($dt) == TRUE) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Set the time of day constraint to consider the next business day.
	 * E.g.: Some businesses will require a payment to be submitted by 3:00pm or it won't be effective until the next business day.
	 *
	 * @param Carbon $dt
	 */
	public function setDeadline(Carbon $dt) {
		$this->deadline = $dt;
	}

	/**
	 * Get the deadline
	 *
	 * @return Carbon
	 */
	public function deadline() {
		return $this->deadline;
	}

	/**
	 * Determine the next business day
	 *
	 * @param Carbon $dt Day to check
	 *
	 * @throws \RuntimeException
	 * @return Carbon Next business day (DATE ONLY, times will be zeroed out)
	 */
	public function nbd(Carbon $dt = NULL) {

		if (($dt instanceof Carbon) == FALSE) {
			$dt = new Carbon();
		}

		if ($this->deadline()) {
			if ($this->deadline()->lt($dt)) {
				$dt->addDay();
			}
		} else {
			$dt->addDay();
		}

		/* Time becomes irrelevant */
		$dt->setTime(0,0,0);

		$iters = 0;
		
		while ($this->isExcluded($dt)) {
			
			if ($iters == static::$N_MAX_ITER) {
				throw new \RuntimeException('Maximum iterations met for next business day calculation');
			}
			
			$dt->addDay();
			$iters++;
		}

		return $dt;
	}
}
