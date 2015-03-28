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

class RulesTest extends BaseTest
{
  public function testOverrideDuplicate()
  {
    $recur = Recur::create( "01/01/2014" )->every( 1, "day" );
    $recur->every( 2, "days" );
    $this->assertEquals(count($recur->rules), 1);
  }

  public function testForgetRule()
  {
    $recur = Recur::create( "01/01/2014" )->every( 1, "day" );
    $recur->forget("days");
    $this->assertEquals(count($recur->rules), 0);
  }
}
