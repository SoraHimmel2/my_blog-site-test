<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;


class Parser
{
    private $scoreboardTable = [];
    private $driver;

    private function insertDataToArray(array $list)
    {
        $result = [];
        foreach ($list as $data) {
            $result[] = $data->getText();
        }
        return $result;
    }
    private function insertMaxComboToArray(array $maxCombo)
    {
        $result = [];
        foreach ($maxCombo as $data) {
            if ($data->getAttribute('class') == 'beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--perfect') {
                $result[] = $data->getText() . ' ' . 'perfectCombo';
            } else {
                $result[] = $data->getText();
            }
        }
        return $result;
    }
    private function insertPlaceOrModsToArray($type, array $placeAndListOfMods)
    {

        $mods = [];
        $place = [];

        for ($counter = 0; $counter < sizeof($placeAndListOfMods); $counter++) {

            if (!isset($placeAndListOfMods[$counter])) {

                break;
            }
            if (ctype_digit($placeAndListOfMods[$counter]->getText()[1])) {

                $place[] = trim($placeAndListOfMods[$counter]->getText(),'#');
            }

            if (!ctype_digit($placeAndListOfMods[$counter]->getText()[1]) || empty($placeAndListOfMods[$counter]->getAttribute('title') . $placeAndListOfMods[$counter]->getAttribute('data-orig-title'))) {
                if (!isset($placeAndListOfMods[$counter + 1])) {
                    $isReady = true;
                } else if (ctype_digit($placeAndListOfMods[$counter + 1]->getText()[1])) {
                    $isReady = true;
                }

                $modResult .= $placeAndListOfMods[$counter]->getAttribute('title') . $placeAndListOfMods[$counter]->getAttribute('data-orig-title') . "_";

            }
            if ($isReady) {

                $mods[] = $modResult;
                unset($modResult);
                $isReady = false;

            }
        }
        if ($type == "place") {
            return $place;
        } else {
            return $mods;
        }
    }

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
       //td/a/div/div[@class="mod mod--EZ"] |
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--rank"]'));

        $this->scoreTable['place'] = $this->insertPlaceOrModsToArray('place', $placeAndListOfMods);
        $this->scoreTable['score'] = $this->insertDataToArray($listOfScore);
        $this->scoreTable['accuracy'] = $this->insertDataToArray($listOfAccuracy);
        $this->scoreTable['player_name'] = $this->insertDataToArray($listOfPlayerName);
        $this->scoreTable['max_combo'] = $this->insertMaxComboToArray($listOfMaxCombo);
        $this->scoreTable['pp'] = $this->insertDataToArray($listOfPp);
        $this->scoreTable['time'] = $this->insertDataToArray($listOfTime);
        $this->scoreTable['mods'] = $this->insertPlaceOrModsToArray('mods', $placeAndListOfMods);

       
    }

    public function getScoreTable()
    {
        return $this->scoreTable;
    }
    
    public function __destruct()
    {
        $this->$driver->quit();
    }

}

