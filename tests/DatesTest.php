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

class DatesTest extends BaseTest
{
  public function testFutureGenerated()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create($start)->every( 2, "days" );
    $nextDates = $recur->next( 3, "m/d/Y" );
    $this->assertEquals(count($nextDates), 3);
    $this->assertEquals($nextDates[0], "01/03/2014");
    $this->assertEquals($nextDates[1], "01/05/2014");
    $this->assertEquals($nextDates[2], "01/07/2014");
  }

  public function testFromDate()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create($start)->every( 2, "days" );
    $recur->from("02/05/2014");
    $nextDates = $recur->next( 3, "m/d/Y" );
    $this->assertEquals(count($nextDates), 3);
    $this->assertEquals($nextDates[0], "02/06/2014");
    $this->assertEquals($nextDates[1], "02/08/2014");
    $this->assertEquals($nextDates[2], "02/10/2014");
  }

  public function testFromDateChain()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create()->start($start)->from("02/05/2014")->every( 2, "days" );
    $nextDates = $recur->next( 3, "m/d/Y" );
    $this->assertEquals(count($nextDates), 3);
    $this->assertEquals($nextDates[0], "02/06/2014");
    $this->assertEquals($nextDates[1], "02/08/2014");
    $this->assertEquals($nextDates[2], "02/10/2014");
  }

  public function testPastGenerated()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create($start)->every( 2, "days" );
    $nextDates = $recur->previous( 3, "m/d/Y" );
    $this->assertEquals(count($nextDates), 3);
    $this->assertEquals($nextDates[0], "12/30/2013");
    $this->assertEquals($nextDates[1], "12/28/2013");
    $this->assertEquals($nextDates[2], "12/26/2013");
  }

  public function testGenerated()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create($start)->end("01/07/2014")->every( 2, "days" );
    $allDates = $recur->all( "m/d/Y" );
    $this->assertEquals(count($allDates), 4);
    $this->assertEquals($allDates[0], "01/01/2014");
    $this->assertEquals($allDates[1], "01/03/2014");
    $this->assertEquals($allDates[2], "01/05/2014");
    $this->assertEquals($allDates[3], "01/07/2014");
  }

  public function testGeneratedFromDate()
  {
    $start = Carbon::parse("01/01/2014");
    $recur = Recur::create($start)->end("01/08/2014")->every( 2, "days" );
    $recur->from("01/05/2014");
    $allDates = $recur->all( "m/d/Y" );
    $this->assertEquals(count($allDates), 2);
    $this->assertEquals($allDates[0], "01/05/2014");
    $this->assertEquals($allDates[1], "01/07/2014");
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Start date cannot be later than end date.
   */
  public function testThrowErrorEndBeforeStart()
  {
    $recur = Recur::create("07/26/2017", "08/01/2013")->every( 2, "days" );
    $allDates = $recur->all( "m/d/Y" );
  }

  public function testCreateSingle()
  {
    $recur = Recur::create("01/01/2014", "01/01/2014")->every( 1, "days" );
    $allDates = $recur->all( "m/d/Y" );
    $this->assertEquals(count($allDates), 1);
    $this->assertEquals($allDates[0], "01/01/2014");
  }

  public function testStartBeforeFromDate()
  {
    $config = [
      'start' => '2015-04-23',
      'from' => '2015-04-12',
      'rules' => [
        'years' => [
          'measure' => 'years',
          'units' => [
            1 => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(count($nextDates), 3);
    $this->assertEquals($nextDates[0], "2016-04-23");
    $this->assertEquals($nextDates[1], "2017-04-23");
  }
}
