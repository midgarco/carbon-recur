<?php

/*
 * This file is part of the Carbon-Recur package.
 *
 * (c) Jeff Dupont <jeff.dupont@phxis.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Recur;

use Carbon\Carbon;
use InvalidArgumentException;

class Recur {

  /**
   * The start date of the rules
   *
   * @var array
   */
  static $start;

  /**
   * The end date of the rules
   *
   * @var array
   */
  static $end;

  protected static $tz = 'UTC';

  protected static $rules = [];
  protected static $exceptions = [];

  static $units;
  static $measure;
  static $from;

  // A dictionary used to match rule measures to rule types
  protected static $ruleTypes = [
      "days" => "interval",
      "weeks" => "interval",
      "months" => "interval",
      "years" => "interval",
      "daysOfWeek" => "calendar",
      "daysOfMonth" => "calendar",
      "weeksOfMonth" => "calendar",
      "weeksOfYear" => "calendar",
      "monthsOfYear" => "calendar"
  ];

  // a dictionary of plural and singular measures
  protected static $measures = [
      "days" => "day",
      "weeks" => "week",
      "months" => "month",
      "years" => "year",
      "daysOfWeek" => "dayOfWeek",
      "daysOfMonth" => "dayOfMonth",
      "weeksOfMonth" => "weekOfMonth",
      "weeksOfYear" => "weekOfYear",
      "monthsOfYear" => "monthOfYear"
  ];

  public function __construct( $start = null, $end = null, $tz = null )
  {
    // initialize all properties
    static::$start = null;
    static::$end = null;
    static::$from = null;
    static::$rules = [];
    static::$exceptions = [];
    static::$tz = 'UTC';


    // Check if the first parameter is an options array
    if ( is_array($start) ) {
      $options = $start;
    }

    if ( $tz ) {
      static::$tz = $tz;
    }
    static::$tz = ( ! empty($options['timezone']) ) ? $options['timezone'] : static::$tz;

    // Setup the start date
    if ( ! empty($options["start"]) ) {
      $start = $options["start"];
    }
    // If the start date is not provided then assume today
    if ( empty($start) ) {
      $start = Carbon::now(static::$tz)->hour(0)->minute(0)->second(0);
    }
    if ( ! $start instanceof \Carbon\Carbon ) {
      $start = Carbon::parse($start, static::$tz)->hour(0)->minute(0)->second(0);
    }
    static::$start = $start;

    // Setup the from date
    if ( ! empty($options["from"]) ) {
      $from = $options["from"];

      if ( ! $from instanceof \Carbon\Carbon ) {
        $from = Carbon::parse($from, static::$tz)->hour(0)->minute(0)->second(0);
      }
      static::$from = $from;
    }

    // Setup the end date
    if ( ! empty($options["end"]) ) {
      $end = $options["end"];
    }
    if ( ! empty($end) ) {
      $end = Carbon::parse($end, static::$tz)->hour(0)->minute(0)->second(0);
      static::$end = $end;
    }

    static::$rules = ( ! empty($options['rules']) ) ? $options['rules'] : [];
    $exceptions = ( ! empty($options['exceptions']) ) ? $options['exceptions'] : [];
    foreach ( $exceptions as $exception ) {
      $this->except($exception);
    }
  }

  public static function now( $tz = null )
  {
    return new static(null, null, $tz);
  }

  public static function create( $start = null, $end = null, $tz = null )
  {
    return new static($start, $end, $tz);
  }

  public function __get($name)
  {
    switch (true) {
      case $name === 'start':
      case $name === 'startDate':
      case $name === 'start_date':
        return static::$start;

      case $name === 'end':
      case $name === 'endDate':
      case $name === 'end_date':
        return static::$end;

      case $name === 'from':
      case $name === 'fromDate':
      case $name === 'from_date':
        return static::$from;

      case $name === 'save':
        return $this->save();

      case $name === 'rules':
        return static::$rules;

      case $name === 'exceptions':
        return static::$exceptions;

      case $name === 'currentDate':
        return static::$from ?: static::$start;

      default:
        throw new InvalidArgumentException(sprintf("Unknown getter '%s'", $name));
    }
  }

  /**
   * Check if an attribute exists on the object
   *
   * @param string $name
   *
   * @return boolean
   */
  public function __isset($name)
  {
    try {
      $this->__get($name);
    } catch (InvalidArgumentException $e) {
      return false;
    }

    return true;
  }

  public function __set($name, $value)
  {
    switch ($name) {
      case 'end':
      case 'endDate':
      case 'end_date':
        $this->end($value);
        break;

      case 'start':
      case 'startDate':
      case 'start_date':
        $this->start($value);
        break;

      case 'from':
      case 'fromDate':
      case 'from_date':
        $this->from($value);
        break;

      case 'timezone':
      case 'tz':
        $this->timezone($value);
        break;

      default:
        throw new InvalidArgumentException(sprintf("Unknown setter '%s'", $name));
    }
  }

  // public function __call($measure, $arguements = false)
  // {
  //   // Create the measure functions (days(), months(), daysOfMonth(), monthsOfYear(), etc.)
  //   if ( in_array($measure, static::$measures) || in_array($measure, array_keys(static::$measures)) ) {
  //     return $this->createMeasure($measure);
  //   }
  // }

  public function timezone($value)
  {
    static::$tz = $value;

    // update set dates
    if ( static::$start && static::$start instanceof \Carbon\Carbon ) {
      static::$start->timezone = static::$tz;
    }
    if ( static::$end && static::$end instanceof \Carbon\Carbon ) {
      static::$end->timezone = static::$tz;
    }
    if ( static::$from && static::$from instanceof \Carbon\Carbon ) {
      static::$from->timezone = static::$tz;
    }

    return $this;
  }

  public function start($value)
  {
    static::$start = Carbon::parse($value, static::$tz)->hour(0)->minute(0)->second(0);

    return $this;
  }

  public function end($value)
  {
    static::$end = Carbon::parse($value, static::$tz)->hour(0)->minute(0)->second(0);

    return $this;
  }

  public function from($value)
  {
    static::$from = Carbon::parse($value, static::$tz)->hour(0)->minute(0)->second(0);

    return $this;
  }

  public function save()
  {
    $data = [];

    if ( static::$start && static::$start instanceof \Carbon\Carbon ) {
      $data['start'] = static::$start->toDateString();
    }

    if ( static::$end && static::$end instanceof \Carbon\Carbon ) {
      $data['end'] = static::$end->toDateString();
    }

    if ( static::$from && static::$from instanceof \Carbon\Carbon ) {
      $data['from'] = static::$from->toDateString();
    }

    $data['timezone'] = static::$tz;

    $data['exceptions'] = [];
    foreach ( static::$exceptions as $exception ) {
      if ( ! $exception instanceof \Carbon\Carbon ) {
        $exception = Carbon::parse($exception);
      }
      $data['exceptions'][] = $exception->toDateString();
    }

    $data['rules'] = static::$rules;

    return $data;
  }

  public function repeats()
  {
    if ( count(static::$rules) > 0) {
      return true;
    }

    return false;
  }

  public function every( $units, $measure = false )
  {
    if ( ! empty($units) ) {
      static::$units = $this->unitsToObject($units);
    }

    if ( ! empty($measure) && $measure !== "" ) {
      static::$measure = $this->pluralize($measure);
    }

    return $this->trigger();
  }

  public function except( $date )
  {
    if ( ! $date instanceof \Carbon\Carbon ) {
      $date = Carbon::parse( $date, static::$tz );
    }

    static::$exceptions[] = $date->format("Y-m-d");

    return $this;
  }

  public function forget( $dateOrRule )
  {
    // If valid date, try to remove it from exceptions
    if ( (bool)strtotime($dateOrRule) ) {

    }
    // if (whatMoment.isValid()) {
    //     whatMoment = whatMoment.dateOnly(); // change to date only for perfect comparison
    //     for (i = 0, len = this.exceptions.length; i < len; i++) {
    //         if (whatMoment.isSame(this.exceptions[i])) {
    //             this.exceptions.splice(i, 1);
    //             return this;
    //         }
    //     }
    //
    //     return this;
    // }

    // Otherwise, try to remove it from the rules
    foreach ( static::$rules as $idx => $rule ) {
      if ( $rule['measure'] === $this->pluralize($dateOrRule) ) {
        unset(static::$rules[$rule['measure']]);
      }
    }
  }

  public function matches( $date, $ignoreStartEnd = false )
  {
    if ( ! $date instanceof \Carbon\Carbon ) {
      $date = Carbon::parse( $date, static::$tz );
    }

    if ( ! $ignoreStartEnd && ! $this->inRange(static::$start, static::$end, $date)) {
      return false;
    }

    if ( $this->isException(static::$exceptions, $date)) { return false; }

    if ( ! $this->matchAllRules(static::$rules, $date, static::$start)) { return false; }

    // if we passed everything above, then this date matches
    return true;
  }

  private function isException($exceptions, $date)
  {
    foreach ( static::$exceptions as $exception ) {
      if ( ! $exception instanceof \Carbon\Carbon ) {
        $exception = Carbon::parse($exception);
      }
      if ( $exception->eq($date) ) {
        return true;
      }
    }
    return false;
  }

  private function matchAllRules($rules, $date, $start)
  {
    foreach( $rules as $rule ) {

      $type = static::$ruleTypes[$rule['measure']];

      if ( $type === "interval" ) {
        if ( ! Interval::match($rule['measure'], $rule['units'], $start, $date) ) {
          return false;
        }
      }
      else if ( $type === "calendar" ) {
        if ( ! Calendar::match($rule['measure'], $rule['units'], $date) ) {
          return false;
        }
      }
      else {
        return false;
      }
    }

    return true;
  }

  private function unitsToObject( $units )
  {
    $list = [];

    if ( is_array($units) ) {
      foreach($units as $unit) {
        $list[$unit] = true;
      }
    }
    else if ( is_object($units) ) {
      $list = $units;
    }
    else if ( ( is_integer($units) && $units >= 0 ) || ( is_string($units) && $units != "" ) ) {
      $list[$units] = true;
    }
    else {
      throw new InvalidArgumentException("Provide an array, object, string or number when passing units!");
    }

    return $list;
  }

  private function inRange($start, $end, $date)
  {
    if ( ! $date instanceof \Carbon\Carbon ) { throw new InvalidArgumentException("The provided date object is not of type 'Carbon\Carbon'"); }
    if ( ! empty($start) && $start instanceof \Carbon\Carbon && $date->format('Y-m-d') < $start->format('Y-m-d') ) { return false; }
    if ( ! empty($end) && $end instanceof \Carbon\Carbon && $date->format('Y-m-d') > $end->format('Y-m-d') ) { return false; }
    return true;
  }

  private function pluralize( $measure )
  {
    switch($measure) {
      case "day":
        return "days";

      case "week":
        return "weeks";

      case "month":
        return "months";

      case "year":
        return "years";

      case "dayOfWeek":
        return "daysOfWeek";

      case "dayOfMonth":
        return "daysOfMonth";

      case "weekOfMonth":
        return "weeksOfMonth";

      case "weekOfYear":
        return "weeksOfYear";

      case "monthOfYear":
        return "monthsOfYear";

      default:
        return $measure;
    }
  }

  // private function createMeasure( $measure ) {
  //   // return function() {
  //   var_dump(static::$units);
  //   return $this->every(static::$units, $measure);
  //   //   return $this;
  //   // };
  // }


  private function trigger()
  {
    if ( empty(static::$units) || empty(static::$measure) ) {
      return $this;
    }

    $rule = null;
    $ruleType = static::$ruleTypes[static::$measure];

    if ( $ruleType !== "calendar" && $ruleType !== "interval" ) {
      throw new InvalidArgumentException("Invalid measure provided: " . static::$measure);
    }

    if ( $ruleType === "interval") {
      if ( empty(static::$start) ) {
        throw new InvalidArgumentException("Must have a start date set to set an interval!");
      }

      $rule = Interval::create(static::$units, static::$measure);

      // weekly defaults to Sunday, so add a rule to set the day of the week if there is not already a rule
      if( static::$measure == 'weeks' && empty(static::$rules['daysOfWeek']) ) {
        $this->every(static::$start->dayOfWeek, 'daysOfWeek');
      }
    }

    if ( $ruleType === "calendar" ) {
      $rule = Calendar::create(static::$units, static::$measure);
    }

    static::$units = null;
    static::$measure = null;

    static::$rules[$rule['measure']] = $rule;

    return $this;
  }

  // Get next N occurances
  public function next( $num, $format = 'Y-m-d' )
  {
    return $this->getOccurances( $num, $format, "next" );
  }

  // Get previous N occurances
  public function previous( $num, $format = 'Y-m-d' )
  {
    return $this->getOccurances( $num, $format, "previous" );
  }

  // Get all occurances between start and end date
  public function all( $format = 'Y-m-d' )
  {
    return $this->getOccurances( null, $format, "all" );
  }

  // Private method to get next, previous or all occurances
  private function getOccurances( $num = false, $format = 'Y-m-d', $type )
  {
    $dates = [];

    if ( ! static::$start && ! static::$from ) {
      throw new InvalidArgumentException("Cannot get occurances without start or from date.");
    }

    if ( $type == "all" && ! static::$end ) {
      throw new InvalidArgumentException("Cannot get all occurances without an end date.");
    }

    if ( !! static::$end && (static::$start > static::$end) ) {
      throw new InvalidArgumentException("Start date cannot be later than end date.");
    }

    // Return empty set if the caller doesn't want any for next/prev
    if ( $type !== "all" && ! ($num > 0) ) {
      return $dates;
    }

    // Start from the from date, or the start date if from is not set
    $currentDate = $this->currentDate->copy();

    // Include the initial date to the results if wanting all dates
    if ( $type === "all" ) {
      if ( $this->matches($currentDate) ) {
        $dates[] = $currentDate->copy()->format($format);
      }
    }

    // Get the next N dates, if num is null then infinite
    $count = 0;
    while ( count($dates) < ( ! $num ? count($dates) + 1 : $num) ) {
      if ( $type === "next" || $type === "all" ) {
        $currentDate->addDay();
      }
      else {
        $currentDate->subDay();
      }

      // Don't match outside the date if generating all dates within start/end
      if ( $this->matches($currentDate, ( $type === "all" ? false : true )) ) {
        $dates[] = $currentDate->copy()->format($format);
      }
      if ( $type === "all" && $currentDate->gte(static::$end) ) {
        break;
      }
    }

    return $dates;
  }

}
