<?php

use Carbon\Carbon;

class Calculator {

    protected $holidays = array(
        'January 1st', /* New years */
        'July 4th', /* Independence Day */
        'November 28th', /* Thanksgiving */
        'December 25th', /* Christmas */

        'Last Monday of May', /* Memorial Day */
        'First Monday of September' /* Labor Day */
    );

    /**
     * @var Carbon
     */
    protected $deadline;

    /**
     * @param $holiday string|Carbon
     */
    public function addHoliday($holiday) {

        if (($holiday instanceof Carbon) == FALSE) {
            $holiday = new Carbon($holiday);
        }
        $this->holidays[] = $holiday;
    }

    /**
     * @return array|Carbon[]
     */
    public function holidays() {
        return $this->holidays;
    }

    public function isHoliday(Carbon $dt) {

        foreach ($this->holidays as $holiday) {

            if ($dt->eq($holiday)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function setDeadline(Carbon $dt) {
        $this->deadline = $dt;
    }

    /**
     * @return Carbon
     */
    public function deadline() {
        return $this->deadline();
    }

    public function nbd(Carbon $dt = NULL) {

        if (($dt instanceof Carbon) == FALSE) {
            $dt = new Carbon();
        }

        /* If after 3pm, effective next business day */
        if ($dt->hour >= 15) {
            $dt->addDay();
        }

        /* Time of day is no longer relevant */
        $dt->setTime(0,0,0);

        while ($dt->isWeekend() || $this->isHoliday($dt)) {
            $dt->addDay();
        }

        return $dt;
    }
}