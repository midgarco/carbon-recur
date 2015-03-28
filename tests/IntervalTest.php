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

class IntervalTest extends BaseTest
{
  public function testDoNotMatchBeforeStart()
  {
    $start = Carbon::parse( $this->start_date );
    $before = $start->copy()->subDay();
    $recur = Recur::create($start)->every( 1, "day" );
    $this->assertFalse($recur->matches($before));
  }

  public function testDoNotMatchAfterEnd()
  {
    $start = Carbon::parse( $this->start_date );
    $after = Carbon::parse( $this->end_date )->addDay();
    $recur = Recur::create($start, $this->end_date)->every( 1, "day" );
    $this->assertFalse($recur->matches($after));
  }

  public function testDaily()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( 2, "days" );
    $this->assertTrue($recur->matches($start->copy()->addDays(2)));
    $this->assertFalse($recur->matches($start->copy()->addDays(3)));
  }

  public function testWeekly()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( 2, "weeks" );
    $this->assertTrue($recur->matches($start->copy()->addWeeks(2)));
    $this->assertFalse($recur->matches($start->copy()->addDays(2)));
    $this->assertFalse($recur->matches($start->copy()->addWeeks(3)));
  }

  public function testMonthly()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( 3, "months" );
    $this->assertTrue($recur->matches($start->copy()->addMonths(3)));
    $this->assertFalse($recur->matches($start->copy()->addMonths(2)));
    $this->assertFalse($recur->matches($start->copy()->addDays(2)));
  }

  public function testYearly()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( 2, "years" );
    $this->assertTrue($recur->matches($start->copy()->addYears(2)));
    $this->assertFalse($recur->matches($start->copy()->addYears(3)));
    $this->assertFalse($recur->matches($start->copy()->addDays(2)));
  }

  public function testMultipleIntervals()
  {
    $start = Carbon::parse( $this->start_date );
    $recur = Recur::create($start)->every( [3,5], "days" );
    $this->assertTrue($recur->matches($start->copy()->addDays(3)));
    $this->assertTrue($recur->matches($start->copy()->addDays(5)));
    $this->assertTrue($recur->matches($start->copy()->addDays(10)));
    $this->assertFalse($recur->matches($start->copy()->addDays(4)));
    $this->assertFalse($recur->matches($start->copy()->addDays(8)));
  }

  // attempt to match an exception
}
