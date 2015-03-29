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

class Calendar {

  // Dictionary of unit types based on measures
  protected static $unitTypes = [
    // "daysOfMonth"  => "date",
    // "daysOfWeek"   => "day",
    // "weeksOfMonth" => "monthWeek",
    // "weeksOfYear"  => "weeks",
    // "monthsOfYear" => "months"
    "daysOfMonth"  => "day",
    "daysOfWeek"   => "dayOfWeek",
    "weeksOfMonth" => "weekOfMonth",
    "weeksOfYear"  => "weekOfYear",
    "monthsOfYear" => "month"
  ];

  // Dictionary of ranges based on measures
  protected static $ranges = [
    "daysOfMonth"  => [ "low" => 1, "high" => 31 ],
    "daysOfWeek"   => [ "low" => 0, "high" => 6 ],
    "weeksOfMonth" => [ "low" => 0, "high" => 4 ],
    "weeksOfYear"  => [ "low" => 0, "high" => 52 ],
    "monthsOfYear" => [ "low" => 0, "high" => 11 ]
  ];

  // Private function for cehcking the range of calendar values
  private static function checkRange($low, $high, $list) {
    foreach ( $list as $v ) {
      if ( $v < $low || $v > $high ) {
        throw new InvalidArgumentException('Value should be in range ' . $low . ' to ' . $high);
      }
    }
  }

  // Private function to convert day and month names to numbers
  private static function namesToNumbers($list, $nameType) {
      // var unit, unitInt, unitNum;
      $newList = [];

      foreach ( $list as $unit => $v ) {
        if( is_string($unit) && static::$unitTypes[$nameType] == 'month' ) {
          $unit = Carbon::parse($unit . " 1")->{ static::$unitTypes[$nameType] };
        }
        elseif( is_string($unit) ) {
          $unit = Carbon::parse($unit)->{ static::$unitTypes[$nameType] };
        }
        $newList[$unit] = $v;
      }

      return $newList;
  }

  static function create($list, $measure)
  {
    $keys = [];

    // Convert day/month names to numbers, if needed
    $list = self::namesToNumbers($list, $measure);

    foreach ($list as $key => $v) {
      $keys[] = $key;
    }

    // Make sure the listed units are in the measure's range
    self::checkRange( static::$ranges[$measure]['low'],
                static::$ranges[$measure]['high'],
                $keys );

    return [
      "measure" => $measure,
      "units" => $list
    ];
  }

  static function match($measure, $list, $date)
  {
    // Get the unit type (i.e. date, day, week, monthWeek, weeks, months)
    $unitType = static::$unitTypes[$measure];

    // Get the unit based on the required measure of the date
    $unit = $date->{$unitType};
    if ( $unitType == "weekOfMonth" && $date->dayOfWeek !== 0 ) {
      $unit--;
    }

    // var_dump([ $unitType, $list, $date, $date->format('W'), $unit ]);

    // If the unit is in our list, return true, else return false
    if ( in_array($unit, array_keys($list)) ) {
      return true;
    }

    // match on end of month days
    if ( $unitType === 'day' && $unit == $date->addMonth(1)->day(0)->day && $unit < 31) {
      while ( $unit <= 31 ) {
        if ( $list[$unit] ) {
          return true;
        }
        $unit++;
      }
    }

    return false;
  }
}
