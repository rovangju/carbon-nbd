<?php
namespace CarbonExt\NBD;

use CarbonExt\NBD\Calculator;
use CarbonExt\NBD\CoreCallbacks as C;
use Carbon\Carbon;

/**
 * This class is a fork of the author's repo and is based on an example
 * by the author in README.md.
 *
 * This class is a specific implementation designed to automatically account
 * for holidays as observed by the NASDAQ. It is not 100% perfect!
 * (does not skip Good Friday, which is difficult to determine, nor Dec 26th
 * which is often taken off in honor of Christmas the day before) so use at
 * your own discretion.
 *
 * I also added a new method, addBusinessDays(), so you can easily add more
 * than one business day (n business days) at a time to any given Carbon date.
 *
 * https://github.com/shorlbeck/nbd-calc  << My fork
 *
 * @author Justin Rovang <generate@itnobody.com>
 * @author Scott Horlbeck <me@scotthorlbeck.com>
 */
class NASDAQBusinessDayCalc extends Calculator {

    /**
     * Array of observed holidays. If the holiday is the same date every
     * year, pass in a string for the value such as "December 25th"
     *
     * You may also pass in an array with three integers corresponding to the
     * args accepted by C::ignoreNDOW(), and if that is still not enough
     * control, you can pass in a string name of a local method that will be
     * called to calculate the particular holiday. Just prefix the string with
     * 'method:' then define the method in this class. Method should return a
     * callback that can be added using $this->addCallback()
     *
     * Array keys (specifying the name of the holiday) are ignored for now,
     * appearing only for readability.
     *
     * @var array
     */
    protected $holidays = array(
        'New Years' => 'January 1st',
        'MLK Jr. Day' => [1,3,1], // 3rd Mon of Jan
        "President's Day" => [2,3,1], // 3rd Mon in Feb
        'Good Friday' => 'method:goodFriday', // 2 days before Easter Sun
        'Memorial Day' => [5,-1,1], // Last Mon of May
        'Independence Day' => 'July 4th',
        'Labor Day' => [9,1,1], // 1st Mon of Sep
        'Thanksgiving Day' => [11, 4, 4], // 4th Thu of Nov
        // Do NOT use 4th Fri of Nov method to determine day after
        // Thanksgiving. See Nov of 2024 on a calendar!
        'Day After Thanksgiving' => 'method:dayAfterThanksgiving',
        'Christmas' => 'December 25th',
    );

    public function __construct() {
        $this->addCallback(C::noWeekends());

        $fixedHolidays = array();
        $variableHolidays = array();

        foreach ($this->holidays as $name => $dt) {
          if (is_string($dt)) {
            if (strpos($dt, 'method:') === 0) {
              $method = str_replace('method:', '', $dt);
              if (method_exists($this, $method)) {
                $callback = $this->{$method}();
                $this->addCallback($callback);
              } else {
                throw new Exception("Method " . $method . " does not exist.");
              }
            } else {
              // Use ignoreRecurring strategy to ignore recurring
              // month-day combos
              $fixedHolidays[] = new Carbon($dt);
            }
          } elseif (is_array($dt) && count($dt) === 3 && is_integer($dt[0])) {
            // Use ignoreNDOW strategy for complex, verbal-oriented exceptions
            $this->addCallback(C::ignoreNDOW($dt[0], $dt[1], $dt[2]));
          }
        }

        $this->addCallback(C::ignoreRecurring($fixedHolidays));

        // Externally, you may wish to set a deadline of 4:30pm Eastern for
        // NASDAQ closing time, which closes at 4:30pm daily.
        // I have not had the chance to test this.
        /* $this->setDeadline(new Carbon('4:30pm')); */
    }

    public function addBusinessDays(Carbon $dt, $num = 1)
    {
      $newDt = $dt;
      for ($i=0; $i<$num; $i++) {
        $newDt = $this->nbd($newDt);
      }
      return $newDt;
    }

    private function dayAfterThanksgiving()
    {
      return function(Carbon $dt) {
        $dtYear = $dt->format('Y');
        $holiday = new Carbon('+1 days fourth thursday of november ' . $dtYear);
        return ($dt == $holiday);
      };
    }

    private function goodFriday()
    {
      return function(Carbon $dt) {
        $dtYear = $dt->format('Y');
        $easterOfDtYear = $this->getCorrectEasterDate($dtYear);
        $goodFriday = $easterOfDtYear->addDays(-2);
        return ($dt == $goodFriday);
      };
    }

    /**
     * Workaround for PHP bug with easter_date()
     * Uses easter_days() instead.
     *
     * @param $year 4-digit year
     * @return Carbon
     */
    private function getCorrectEasterDate($year)
    {
      return new Carbon($year . '-03-21 +' .easter_days($year) . ' days');
    }
}
