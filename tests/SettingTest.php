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

class SettingTest extends BaseTest
{
  public function testRecurAssignStartParameter()
  {
    $recur = new Recur();
    $recur->start($this->start_date);
    $this->assertEquals($recur->start->toDateString(), $this->start_date);
  }

  public function testRecurAssignEndParameter()
  {
    $recur = new Recur();
    $recur->end($this->end_date);
    $this->assertEquals($recur->end->toDateString(), $this->end_date);
  }
}
