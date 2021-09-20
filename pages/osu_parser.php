<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
$webdriver_path = $_SERVER['DOCUMENT_ROOT'] . '/vendor/php-webdriver/webdriver/lib/';
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$chromeOptions = new ChromeOptions();
$chromeOptions->addArguments(['--headless']);

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);

$serverUrl = 'http://localhost:4444/wd/hub';

$driver = RemoteWebDriver::create($serverUrl, $capabilities);

//$driver->get('https://www.w3schools.com/html/html_tables.asp');
$driver->get('https://osu.ppy.sh/beatmapsets/1313256#osu/2721646');

$listOfScore = $driver->wait(10)->until(
    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::xpath('//a[@class = "beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--score"  ]'))
);

$listOfAccuracy = $driver->findElements(WebDriverBy::xpath('//descendant::td[@class="beatmap-scoreboard-table__cell"][4]'));
$listOfPlayerName = $driver->findElements(WebDriverBy::xpath('
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--user-link js-usercard"]'));
$listOfMaxCombo = $driver->findElements(WebDriverBy::xpath('//child::td[7]/a'));
$listOfPp = $driver->findElements(WebDriverBy::xpath('//td/a/span'));
$listOfTime = $driver->findElements(WebDriverBy::xpath('//td/a/time[@class="js-tooltip-time"]'));
$listOfMods = $driver->findElements(WebDriverBy::xpath('
       //td/a/div/div[@class="mod mod--HD"] |
       //td/a/div/div[@class="mod mod--HR"] |
       //td/a/div/div[@class="mod mod--DT"] |
       //td/a/div/div[@class="mod mod--NC"] |
       //td/a/div/div[@class="mod mod--FL"] |
       //td/a/div/div[@class="mod mod--NF"] |
       //td/a/div/div[@class="mod mod--HT"] |
       //a[@class="beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--rank"]'));

$scoreTable = [
    'score' => $listOfScore,
    'accuracy' => $listOfAccuracy,
    'playerName' => $listOfPlayerName,
    'maxCombo' => $listOfMaxCombo,
    'pp' => $listOfPp,
    'time' => $listOfTime,
    'mods' => $listOfMods];

foreach ($scoreTable as $tableCols => $col) {
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
//for ($i = 0; $i < count($scoreTable); $i++) {
//    if ($i == 6) {
//        foreach ($mods as $mod => $text) {
//            echo ($text->getText() . $text->getAttribute('title') . $text->getAttribute('data-orig-title') . '<br/>');
//
//        }
//    } elseif ($i == 3) {
//        foreach ($maxCombo as $combo => $text) {
//            if ($text->getAttribute('class') == 'beatmap-scoreboard-table__cell-content beatmap-scoreboard-table__cell-content--perfect') {
//                print_r($text->getText() . " perfectCombo " . "<br/>");
//            } else {
//                print_r($text->getText() . '<br/>');
//            }
//        }
//    } else {
//        foreach ($scoreTable[$i] as $data => $text) {
//            print_r($text->getText() . '<br/>');
//        }
//    }
//    echo '<br/>';
//  }

$driver->quit();
