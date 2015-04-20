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

class ScenarioTest extends BaseTest
{
  public function testDaily()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "days" => [
          "measure" => "days",
          "units" => [
            "1" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-02");
    $this->assertEquals($nextDates[1], "2015-01-03");
  }

  public function testDailyWeekdays()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "daysOfWeek" => [
          "measure" => "daysOfWeek",
          "units" => [
            "1" => true
          ]
        ],
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-05");
    $this->assertEquals($nextDates[1], "2015-01-12");
  }

  public function testWeekly()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "daysOfWeek" => [
          "measure" => "daysOfWeek",
          "units" => [
            "5" => true
          ]
        ],
        "weeks" => [
          "measure" => "weeks",
          "units" => [
            "1" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-02");
    $this->assertEquals($nextDates[1], "2015-01-09");
  }

  public function testBiWeekly()
  {
    $config = [
      "start" => "2014-12-28",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "daysOfWeek" => [
          "measure" => "daysOfWeek",
          "units" => [
            "0" => true,
            "5" => true
          ]
        ],
        "weeks" => [
          "measure" => "weeks",
          "units" => [
            "2" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(4);
    $this->assertEquals(4, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-02");
    $this->assertEquals($nextDates[1], "2015-01-11");
    $this->assertEquals($nextDates[2], "2015-01-16");
  }

  public function testMonthly()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "months" => [
          "measure" => "months",
          "units" => [
            "1" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-02-01");
    $this->assertEquals($nextDates[1], "2015-03-01");
    $this->assertEquals($nextDates[2], "2015-04-01");
  }

  public function testMonthlyByDay()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "daysOfMonth" => [
          "measure" => "daysOfMonth",
          "units" => [
            "10" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-10");
    $this->assertEquals($nextDates[1], "2015-02-10");
    $this->assertEquals($nextDates[2], "2015-03-10");
  }

  public function testQuarterly()
  {
    $config = [
      "start" => "2015-01-03",
      "from" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "months" => [
          "measure" => "months",
          "units" => [
            "3" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2015-01-03");
    $this->assertEquals($nextDates[1], "2015-04-03");
    $this->assertEquals($nextDates[2], "2015-07-03");
  }

  public function testYearly()
  {
    $config = [
      "start" => "2015-01-01",
      "timezone" => "America/Phoenix",
      "exceptions" => [],
      "rules" => [
        "years" => [
          "measure" => "years",
          "units" => [
            "1" => true
          ]
        ]
      ]
    ];

    $recur = Recur::create($config);
    $nextDates = $recur->next(3);
    $this->assertEquals(3, count($nextDates));
    $this->assertEquals($nextDates[0], "2016-01-01");
    $this->assertEquals($nextDates[1], "2017-01-01");
    $this->assertEquals($nextDates[2], "2018-01-01");
  }

}
