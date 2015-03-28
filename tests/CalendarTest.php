<?php

/*
 * This file is part of the Carbon-Recur package.
 *
 * (c) Jeff Dupont <jeff.dupont@phxis.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Recur\Recur;

class CalendarTest extends BaseTest
{
  public function testDoNotMatchBeforeStart()
  {
    $start = Carbon::now();
    $recur = Recur::create($start)->every( [ "Sunday", 1 ], "daysOfWeek" );
    $this->assertTrue($recur->matches(new Carbon("Sunday")));
    $this->assertTrue($recur->matches(new Carbon("Sunday +1 day")));
    $this->assertFalse($recur->matches(new Carbon("Sunday +3 day")));
  }

  public function testTimezones()
  {
    $start = Carbon::parse("2015-01-25", "America/Vancouver");
    $check = Carbon::parse("2015-02-01", "Asia/Hong_Kong");
    $recur = Recur::create($start)->every( [ "Sunday", 1 ], "daysOfWeek" );
    $this->assertTrue($recur->matches($check));
  }

  public function testDaysOfMonth()
  {
    $recur = Recur::create("2015-01-01")->every( [ 1, 10 ], "daysOfMonth" );
    $this->assertTrue($recur->matches(Carbon::parse("2015-01-01")));
    $this->assertFalse($recur->matches(Carbon::parse("2015-01-02")));
    $this->assertTrue($recur->matches(Carbon::parse("2015-01-10")));
    $this->assertFalse($recur->matches(Carbon::parse("2015-01-15")));
    $this->assertTrue($recur->matches(Carbon::parse("2015-02-01")));
    $this->assertFalse($recur->matches(Carbon::parse("2015-02-02")));
    $this->assertTrue($recur->matches(Carbon::parse("2015-02-10")));
    $this->assertFalse($recur->matches(Carbon::parse("2015-02-15")));
  }

  public function testWeeksOfMonth()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( [ 1, 3 ], "weeksOfMonth" );
    $this->assertTrue($recur->matches($start->copy()->day(6)));
    $this->assertTrue($recur->matches($start->copy()->day(26)));
    $this->assertFalse($recur->matches($start->copy()->day(27)));
  }

  public function testWeeksOfYear()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( 20, "weekOfYear" );
    $this->assertTrue($recur->matches(Carbon::parse("05/14/2014")));
    $this->assertFalse($recur->matches($start));
  }

  public function testMonthsOfYear()
  {
    $start = Carbon::now();
    $recur = Recur::create($start)->every( "January", "monthsOfYear" );
    $this->assertTrue($recur->matches(Carbon::parse("January")->addYear(1)));
    $this->assertFalse($recur->matches(Carbon::parse("February")->addYear(1)));
  }

  public function testCombinedRules()
  {
    $start = Carbon::now();
    $recur = Recur::create($start)->every( 14, "daysOfMonth" )
                                  ->every( "February", "monthsOfYear" );
    $this->assertTrue($recur->matches(Carbon::parse("February 14")->addYear(1)));
    $this->assertFalse($recur->matches($start));
  }
}
