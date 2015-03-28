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

class ExceptionTest extends BaseTest
{
  public function testNoMatchException()
  {
    $start = Carbon::parse( $this->start_date );
    $exception = $start->copy()->addDays(7);
    $recur = Recur::create($start)->every( 1, "weeks" );
    $this->assertTrue($recur->matches($exception));
    $recur->except($exception);
    $this->assertFalse($recur->matches($exception));
  }
}
