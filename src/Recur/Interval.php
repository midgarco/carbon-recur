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

class Interval {

  static function diffInDays($d1, $d2, $abs) {
    return self::diff($d1, $d2, 'day', $abs);
  }

  static function diffInWeeks($d1, $d2, $abs) {
    return self::diff($d1, $d2, 'week', $abs);
  }

  static function diffInMonths($d1, $d2, $abs) {
    return self::diff($d1, $d2, 'month', $abs);
  }

  static function diffInYears($d1, $d2, $abs) {
    return self::diff($d1, $d2, 'year', $abs);
  }

  static function diff($d1, $d2, $type = '', $absValue = false)
  {
    $zoneDelta = ($d2->offset * 1000 - $d1->offset * 1000) * 6e4;
    $delta = 0;
    $output = 0;

    if( $type === 'year' || $type === 'month' || $type === 'quarter' ) {
      $output = self::monthDiff($d1, $d2);
      if( $type === 'quarter' ) {
        $output = $output / 3;
      }
      elseif( $type === 'year' ) {
        $output = $output / 12;
      }
    }
    else {
      $delta = $d2->format('U') * 1000 - $d1->format('U') * 1000;
      $output = self::calcDiff($type, $delta, $zoneDelta);
    }
    return $absValue ? self::absFloor($output) : $output;
  }

  static function calcDiff($type, $delta, $zoneDelta)
  {
    switch( $type ) {
      case 'second':
        return $delta / 1e3; // 1000
      case 'minute':
        return $delta / 6e4; // 1000 * 60
      case 'hour':
        return $delta / 36e5; // 1000 * 60 * 60
      case 'day':
        return ($delta - $zoneDelta) / 864e5; // 1000 * 60 * 60 * 24, negate dst
      case 'week':
        return ($delta - $zoneDelta) / 6048e5; // 1000 * 60 * 60 * 24 * 7, negate dst
      default:
        return $delta;
    }
  }

  static function absFloor($number)
  {
    if ($number < 0) {
      return ceil($number);
    } else {
      return floor($number);
    }
  }

  static function monthDiff($a, $b)
  {
    // difference in months
    $wholeMonthDiff = (($b->year - $a->year) * 12) + ($b->month - $a->month);
    // b is in (anchor - 1 month, anchor + 1 month)
    $anchor = ($wholeMonthDiff) ? $a->copy()->addMonths($wholeMonthDiff) : $a->copy();
    $anchor2 = 0;
    $adjust = 0;

    // print_r([ $b, $anchor, $b->format('U') * 1000 - $anchor->format('U') * 1000 ]);
    if( $b->format('U') * 1000 - $anchor->format('U') * 1000 < 0 ) {
      $anchor2 = $a->copy()->addMonths($wholeMonthDiff - 1);
      // linear across the month
      $adjust = ($b->format('U') * 1000 - $anchor->format('U') * 1000) / ($anchor->format('U') * 1000 - $anchor2->format('U') * 1000);
      // print_r([ 'if', $a, $b, $anchor, $anchor2, $adjust ]);
    }
    else {
      $anchor2 = $a->copy()->addMonths($wholeMonthDiff + 1);
      // linear across the month
      $adjust = ($b->format('U') * 1000 - $anchor->format('U') * 1000) / ($anchor2->format('U') * 1000 - $anchor->format('U') * 1000);
      // print_r([ 'else', $a, $b, $anchor, $anchor2, $adjust, $wholeMonthDiff + 1 ]);
    }

    return -($wholeMonthDiff + $adjust);
  }

  static function create( $units, $measure )
  {
    // Make sure all of the units are integers greater than 0.
    foreach ($units as $k => $unit) {
      if ( $unit <= 0 ) {
        throw new InvalidArgumentException("Intervals must be greater than zero");
      }
    }

    return [
      "measure" => strtolower($measure),
      "units" => $units
    ];
  }

  static function match( $type, $units, $start, $date, $abs = false )
  {
    // if( $type == 'weeks' && $start->dayOfWeek > 0 ) {
    //   $start->startOfWeek()->subDay(1); // Start of the week is a Sunday
    // }

    // Get the difference between the start date and the provided date,
    // using the required measure based on the type of rule'
    $diff = null;
    if( $date->lt($start) ) {
      $diff = self::{'diffIn' . ucfirst($type) }($date, $start, $abs);
    }
    else {
      $diff = self::{'diffIn' . ucfirst($type) }($start, $date, $abs);
    }
    if( $type == 'days' ) {
      // if we are dealing with days, we deal with whole days only.
      $diff = floor($diff);
    }
    // print_r([ $type, $units, $start, $date, $diff, $abs ]);

    // Check to see if any of the units provided match the date
    foreach ($units as $unit => $value) {
      // If the units divide evenly into the difference, we have a match
      // print_r([ 'type' => $type, 'unit' => $unit, 'diff' => $diff, 'fmod' => fmod($diff, $unit), 'fmod_eq' => (fmod($diff, $unit) === (float)0), 'mod' => ( $diff % $unit ), 'start' => $start, 'date' => $date ]);
      if( (fmod($diff, $unit) === (float)0) ) {
        return true;
      }
    }

    return false;
  }
}
