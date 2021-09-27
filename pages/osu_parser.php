<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Parser
{
    private $scoreTable = [];
    private $driver;

    public function __construct($mapUrl)
    {
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(['--headless']);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);

        $serverUrl = 'http://localhost:4444/wd/hub';

        $this->$driver = RemoteWebDriver::create($serverUrl, $capabilities);

        $this->$driver->get($mapUrl);
    }

    public function getTableData()
    {
        $listOfScore = $this->$driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::xpath('//a[@class = "beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--score"  ]'))
        );

        $listOfAccuracy = $this->$driver->findElements(WebDriverBy::xpath('//descendant::td[@class="beatmap-scoreboard-table__cell"][4]'));
        $listOfPlayerName = $this->$driver->findElements(WebDriverBy::xpath('
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--user-link js-usercard"]'));
        $listOfMaxCombo = $this->$driver->findElements(WebDriverBy::xpath('//child::td[7]/a'));
        $listOfPp = $this->$driver->findElements(WebDriverBy::xpath('//td/a/span'));
        $listOfTime = $this->$driver->findElements(WebDriverBy::xpath('//td/a/time[@class="js-tooltip-time"]'));
        $listOfMods = $this->$driver->findElements(WebDriverBy::xpath('
       //td/a/div/div[@class="mod mod--HD"] |
       //td/a/div/div[@class="mod mod--HR"] |
       //td/a/div/div[@class="mod mod--DT"] |
       //td/a/div/div[@class="mod mod--NC"] |
       //td/a/div/div[@class="mod mod--FL"] |
       //td/a/div/div[@class="mod mod--NF"] |
       //td/a/div/div[@class="mod mod--HT"] |
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--rank"]'));

        $this->$scoreTable = [
            'score' => $listOfScore,
            'accuracy' => $listOfAccuracy,
            'playerName' => $listOfPlayerName,
            'maxCombo' => $listOfMaxCombo,
            'pp' => $listOfPp,
            'time' => $listOfTime,
            'mods' => $listOfMods,
        ];
    }

    public function sendDataToDatabase()
    {
        foreach ($this->$scoreTable as $tableCols => $col) {
            if ($tableCols == 'mods') {
                foreach ($col as $rowList => $rowData) {

                    echo ($rowData->getText() . $rowData->getAttribute('title') . $rowData->getAttribute('data-orig-title') . '<br/>');

                }
            } elseif ($tableCols == 'maxCombo') {
                foreach ($col as $rowList => $rowData) {
                    if ($rowData->getAttribute('class') == 'beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--perfect') {
                        print_r($rowData->getText() . " perfectCombo " . "<br/>");
                    } else {
                        print_r($rowData->getText() . '<br/>');
                    }

                }
            } else {
                foreach ($col as $rowList => $rowData) {
                    echo ($rowData->getText() . '<br/>');
                }

            }

            echo ('<br/>');
        }

        $this->$driver->quit();
    }

}
//$osuParser = new Parser('https://osu.ppy.sh/beatmapsets/1313256#osu/2721646');
//$osuParser->getTableData();
//$osuParser->sendDataToDatabase();

include_once 'score_table_db.php';
$testDb = new ScoreTable();
$testDb->insertRow('pp','832');
$testDb->insertRow('pp','988');
$testDb->showData();

