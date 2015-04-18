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

  static function match( $type, $units, $start, $date )
  {
    if( $type == 'weeks' && $start->dayOfWeek > 0 ) {
      $start->startOfWeek()->subDay(1); // Start of the week is a Sunday
    }

    // Get the difference between the start date and the provided date,
    // using the required measure based on the type of rule'
    $diff = null;
    if( $date->lt($start) ) {
      $diff = $start->{'diffIn' . ucfirst($type) }($date);
      $check = $date->copy()->{'add' . ucfirst($type)}($diff);
    }
    else {
      $diff = $date->{'diffIn' . ucfirst($type) }($start);
      $check = $start->copy()->{'add' . ucfirst($type)}($diff);
    }
    // print_r([ $diff, $start, $date, $check ]);

    // Check to see if any of the units provided match the date
    foreach ($units as $unit => $value) {
      // If the units divide evenly into the difference, we have a match
      // print_r([ 'type' => $type, 'unit' => $unit, 'diff' => $diff, 'fmod' => fmod($diff, $unit), 'fmod_eq' => (fmod($diff, $unit) === (float)0), 'mod' => ( $diff % $unit ), 'start' => $start, 'date' => $date ]);
      // print_r([ $unit, $diff, ( $diff > 0 && ($diff % $unit) === 0 ), $date ]);
      if( ($diff > 0 && ($diff % $unit) === 0) || $start->eq($date) ) {
        return true;
      }
    }

    return false;
  }
}
