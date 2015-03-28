<?php

/*
 * This file is part of the Carbon-Recur package.
 *
 * (c) Jeff Dupont <jeff.dupont@phxis.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

use Recur\Recur;
use Carbon\Carbon;

class BaseTest extends \PHPUnit_Framework_TestCase
{
  protected $start_date = "2013-01-01";
  protected $end_date = "2014-01-01";
  protected $now_date;

  private $saveTz;


  protected function setUp()
  {
    //save current timezone
    // $this->saveTz = date_default_timezone_get();
    date_default_timezone_set('UTC');

    $this->now_date = Carbon::now()->toDateString();
  }

  protected function tearDown()
  {
    // date_default_timezone_set($this->saveTz);
  }

  // protected function assertCarbon(Carbon $d, $year, $month, $day, $hour = null, $minute = null, $second = null)
  // {
  //     $this->assertSame($year, $d->year, 'Carbon->year');
  //     $this->assertSame($month, $d->month, 'Carbon->month');
  //     $this->assertSame($day, $d->day, 'Carbon->day');
  //
  //     if ($hour !== null) {
  //         $this->assertSame($hour, $d->hour, 'Carbon->hour');
  //     }
  //
  //     if ($minute !== null) {
  //         $this->assertSame($minute, $d->minute, 'Carbon->minute');
  //     }
  //
  //     if ($second !== null) {
  //         $this->assertSame($second, $d->second, 'Carbon->second');
  //     }
  // }

  protected function assertInstanceOfRecur($d)
  {
    $this->assertInstanceOf('Recur\Recur', $d);
  }
}
