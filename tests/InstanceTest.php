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

class InstanceTest extends BaseTest
{
  public function testRecurWithOptions()
  {
    $recur = new Recur([ "start" => $this->start_date, "end" => $this->end_date ]);
    $this->assertEquals($recur->start->toDateString(), $this->start_date);
    $this->assertEquals($recur->end->toDateString(), $this->end_date);
  }

  public function testRecurWithStart()
  {
    $recur = new Recur( $this->start_date );
    $this->assertEquals($recur->start->toDateString(), $this->start_date);
  }

  public function testRecurWithStartAndEnd()
  {
    $recur = new Recur( $this->start_date, $this->end_date );
    $this->assertEquals($recur->start->toDateString(), $this->start_date);
    $this->assertEquals($recur->end->toDateString(), $this->end_date);
  }

  public function testRecurNow()
  {
    $recur = new Recur();
    $this->assertEquals($recur->start->toDateString(), $this->now_date);
  }

  public function testImportable()
  {
    $recur = Recur::create([
      'start' => '01/01/2014',
      'end' => '12/31/2014',
      'rules' => [
        [ 'units' => [ 2 => true ], 'measure' => 'days' ]
      ],
      'exceptions' => [ '01/05/2014' ]
    ]);
    $this->assertEquals($recur->startDate->format('m/d/Y'), '01/01/2014');
    $this->assertEquals($recur->endDate->format('m/d/Y'), '12/31/2014');
    $this->assertEquals(1, count($recur->rules));
    $this->assertEquals(1, count($recur->exceptions));
    $this->assertTrue($recur->matches("01/03/2014"));
    $this->assertFalse($recur->matches("01/05/2014"));
  }

  public function testExportable()
  {
    $recur = Recur::create("01/01/2014")->end("12/31/2014")->every( 2, "days" )->except("01/05/2014");
    $data = $recur->save();
    $this->assertEquals("2014-01-01", $data['start']);
    $this->assertEquals("2014-12-31", $data['end']);
    $this->assertEquals("2014-01-05", $data['exceptions'][0]);
    $this->assertTrue($data['rules']['days']['units']['2']);
    $this->assertEquals("days", $data['rules']['days']['measure']);
  }

  public function testRepeatsWithRules()
  {
    $recur = Recur::now()->every( 1, "days" );
    $this->assertTrue($recur->repeats());
  }

  public function testNoRepeatWithNoRules()
  {
    $recur = Recur::now();
    $this->assertFalse($recur->repeats());
  }
}
