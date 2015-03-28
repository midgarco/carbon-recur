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
    // Get the difference between the start date and the provided date,
    // using the required measure based on the type of rule'
    $diff = null;
    if( $date->lt($start) ) {
      $diff = $start->{'diffIn' . ucfirst($type) }($date);
      $check = $date->copy()->{'add' . ucfirst($type)}($diff);
      if ( ! $start->eq($check) ) $diff += 0.1;
    } else {
      $diff = $date->{'diffIn' . ucfirst($type) }($start);
      $check = $start->copy()->{'add' . ucfirst($type)}($diff);
      if ( ! $date->eq($check) ) $diff += 0.1;
    }
    // var_dump([ $diff, $start, $date, $check ]);
    if( $type == 'days') {
      // if we are dealing with days, we deal with whole days only.
      $diff = floor($diff);
    }

    // Check to see if any of the units provided match the date
    foreach ($units as $unit => $value) {
      // If the units divide evenly into the difference, we have a match
      // var_dump([ $type, $unit, $diff, fmod($diff, $unit), (fmod($diff, $unit) === (float)0), (fmod($diff, $unit) == 0), $start, $date ]);
      if (fmod($diff, $unit) === (float)0) {
        return true;
      }
    }

    return false;
  }
}
