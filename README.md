# Carbon Extension: Next Business Day (NBD)

[![Build Status](https://travis-ci.org/rovangju/carbon-nbd.svg?branch=master)](https://travis-ci.org/rovangju/carbon-nbd)

-------

Have some business logic that requires a somewhat complicated calculation for deadlines or effective dates that need to be modified based on holidays, weekends or special dates? This extension for the PHP [Carbon project](https://github.com/briannesbitt/Carbon/) (a DateTime extension itself) is for you!



## Overview
The basic principal for this utility is to perform date based logic to determine the "next business day" for a provided date. There are various ways to layer in complexity to supplement holidays and deadlines. 

The meat and potatoes functionality is provided by the `nbd(Carbon $c)` method. The method will perform it's checking routine by checking the various criteria and incrementing the day by one in a loop. The `nbd()` method will return an updated `Carbon::` object with the date set to the appropriate business day.
 
**Please note: This is for date logic only**.

### Basic usage ###
```php
use CarbonExt\NBD\Calculator;
use CarbonExt\NBD\CoreCallbacks C;
use Carbon\Carbon;

$nbd = new Calculator();

/* Date to find the next business day for */
$dt = new Carbon('2014-01-01');

/* Cherry pick special dates */
$nbd->addExclusion(new Carbon('2014-01-02'));
var_dump($nbd->nbd($dt)); /* Carbon obj: 2014-01-03 */

```

### Callback support
```php

/* Custom exclusion callbacks for layering in complexity */
$nbd->addCallback(C::noWeekends());
$nbd->addCallback(C::ignoreDaysOfWeek(array(4))); /* No Saturdays, uses Carbon's 0-based offsets */

$cfn = function(Carbon $dt) {
	return ($dt->day % 2 == 0);
}

$nbd->addCallback($cfn); /* Only on even days of the month */
```

### Deadline support
Say you want to allow payments made _today, before 3:00pm_ effective today. If the payment was submitted _after_ 3:00pm, it is not effective until the next business day...

**NOTE: Deadlines are ONLY effective based on hour logic; the date passed into setDeadline() is unregarded!**

```php
$nbd->setDeadline(new Carbon('3:00pm'));

$nbd->nbd(new Carbon('2014-01-01 2:59:59pm')); /* Carbon obj: 2014-01-01 00:00:00 */
$nbd->nbd(new Carbon('2014-01-01 3:00:01pm')); /* Carbon obj: 2014-01-02 00:00:00 */
```

-------

## A more complete use case via extension...

```php
use CarbonExt\NBD\Calculator;
use CarbonExt\NBD\CoreCallbacks C;
use Carbon\Carbon;

class BusinessDayCalc extends Calculator {

	protected $observedHolidays = array(
		'January 1st', /* New years */
		'July 4th', /* Independence Day */
		'November 28th', /* Thanksgiving */
		'December 25th', /* Christmas */
		'Last Monday of May', /* Memorial Day */
		'First Monday of September' /* Labor Day */
	);

	public function __construct() {

		$this->addCallback(C::noWeekends());

		foreach ($this->observedHolidays as $dt) {
			$this->addException(new Carbon($dt));
		}		
	}
}

$nbd = new BusinessDayCalc();
/* Now all your business use-case rules are automatically built in */

```
