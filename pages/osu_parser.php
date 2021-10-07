<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

include 'top_score_database.php';

class Parser
{
    private $beatmapScoreboardTable = [];
    private $driver;
    private $osuScoreDatabase;

    public function __construct($mapUrl)
    {
        //$chromeOptions = new ChromeOptions();
        //$chromeOptions->addArguments(['--headless']);

        //$capabilities = DesiredCapabilities::chrome();
        //$capabilities->setCapability(ChromeOptions::CAPABILITY_W3C,$chromeOptions);

        $serverUrl = 'http://localhost:4444/wd/hub';

        $this->$driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());

        $this->$driver->get($mapUrl);
    }

    public function getTableData()
    {
        $listOfScore = $this->$driver->wait(8)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::xpath('//a[@class = "beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--score"  ]'))
        );

        $listOfAccuracy = $this->$driver->findElements(WebDriverBy::xpath('//descendant::td[@class="beatmap-scoreboard-table__cell"][4]'));
        $listOfPlayerName = $this->$driver->findElements(WebDriverBy::xpath('
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--user-link js-usercard"]'));
        $listOfMaxCombo = $this->$driver->findElements(WebDriverBy::xpath('//child::td[7]/a'));
        $listOfPp = $this->$driver->findElements(WebDriverBy::xpath('//td/a/span'));
        $listOfTime = $this->$driver->findElements(WebDriverBy::xpath('//td/a/time[@class="js-tooltip-time"]'));
        $placeAndListOfMods = $this->$driver->findElements(WebDriverBy::xpath('
       //td/a/div/div[@class="mod mod--HD"] |
       //td/a/div/div[@class="mod mod--HR"] |
       //td/a/div/div[@class="mod mod--DT"] |
       //td/a/div/div[@class="mod mod--NC"] |
       //td/a/div/div[@class="mod mod--FL"] |
       //td/a/div/div[@class="mod mod--NF"] |
       //td/a/div/div[@class="mod mod--HT"] |
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--rank"]'));

        //$this->$scoreTable = [
        //    'score' => $listOfScore,
        //    'accuracy' => $listOfAccuracy,
        //    'player_name' => $listOfPlayerName,
        //    'max_combo' => $listOfMaxCombo,
        //    'pp' => $listOfPp,
        //    'time' => $listOfTime,
        //    'mods' => $listOfMods,
        //];

        /*foreach ($this->$scoreTable as $tableCols => $col) {

        if ($tableCols == 'mods') {
        foreach ($col as $rowData) {

        //echo ($rowData->getText() . $rowData->getAttribute('title') . $rowData->getAttribute('data-orig-title') . '<br/>');
        $sliceOfData['place'] = $rowData->getText();
        $sliceOfData[$tableCols] = $rowData->getAttribute('title') . $rowData->getAttribute('data-orig-title');
        //$this->osuScoreDatabase->insertRow('place', $rowData->getText());
        //$this->osuScoreDatabase->insertRow('mods', $rowData->getAttribute('title') . $rowData->getAttribute('data-orig-title'));

        }
        } elseif ($tableCols == 'maxCombo') {
        foreach ($col as $rowData) {
        if ($rowData->getAttribute('class') == 'beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--perfect') {
        //print_r($rowData->getText() . " perfectCombo " . "<br/>");
        $sliceOfData[$tableCols] =  $rowData->getText() . 'perfectCombo';
        } else {
        $sliceOfData [$tableCols] =  $rowData->getText();
        }

        }
        } else {
        foreach ($col as $rowData) {
        //echo ($rowData->getText() . '<br/>');

        }

        }

        echo ('<br/>');
        }*/
        $mods = [];
        $results = [];
        $currentState = [];
        foreach ($placeAndListOfMods as $data) {
            // print_r($data->getText() . '<br/>');
            // print_r($data->getAttribute('title') . $data->getAttribute('data-orig-title') . '<br/>');

        }
        for ($counter = 0; $counter < sizeof($placeAndListOfMods); $counter++) {

            if (!isset($placeAndListOfMods[$counter])) {
                $currentState[] = $placeAndListOfMods[$counter]->getAttribute('title') . $placeAndListOfMods[$counter]->getAttribute('data-orig-title') .
                $placeAndListOfMods[$counter]->getText();
                break;
            }
            if (ctype_digit($placeAndListOfMods[$counter]->getText()[1])) {
                $currentState[] = $placeAndListOfMods[$counter]->getAttribute('title') . $placeAndListOfMods[$counter]->getAttribute('data-orig-title') .
                $placeAndListOfMods[$counter]->getText();
                $results[] = $placeAndListOfMods[$counter]->getText();
            }
            if (!ctype_digit($placeAndListOfMods[$counter]->getText()[1])) {
                if (!isset($placeAndListOfMods[$counter + 1])) {
                    $isReady = true;
                } else if (ctype_digit($placeAndListOfMods[$counter + 1]->getText()[1])) {
                    $isReady = true;
                }
                $modResult .= $placeAndListOfMods[$counter]->getAttribute('title') . $placeAndListOfMods[$counter]->getAttribute('data-orig-title');
                $currentState[] = $placeAndListOfMods[$counter]->getAttribute('title') .
                $placeAndListOfMods[$counter]->getAttribute('data-orig-title') .
                $placeAndListOfMods[$counter]->getText();

            }
            if ($isReady) {
                $mods[] = $modResult;
                $modResult = " ";
                $isReady = false;
            }
        }

        print_r($mods);
        echo "<br/>";
        print_r($results);

    }
    public function __destruct()
    {
        $this->$driver->quit();
    }

}
$osuParser = new Parser('https://osu.ppy.sh/beatmapsets/441668#osu/949948');
$osuParser->getTableData();
