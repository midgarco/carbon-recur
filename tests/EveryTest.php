<?php

/*
 * This file is part of the Carbon-Recur package.
 *
 * (c) Jeff Dupont <jeff.dupont@phxis.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Recur\Recur;

class EveryTest extends BaseTest
{
  public function testUnitAndMeasure()
  {
    $recur = Recur::now()->every( 1, "day" );
    $this->assertEquals(1, count($recur->save['rules']));
  }

  public function testOnlyUnit()
  {
    $recur = Recur::now()->every( 1 );
    $this->assertEquals(0, count($recur->save['rules']));
  }

  public function testUnitsProperty()
  {
    $recur = Recur::now()->every( 1 );
    $this->assertNotNull($recur::$units);
  }

  public function testUnitsPropertyArray()
  {
    $recur = Recur::now()->every( [1, 2] );
    $this->assertNotNull($recur::$units);
  }

}
