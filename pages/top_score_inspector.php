<?php
require_once __DIR__ . '/../classes/osu_parser.php';
require_once __DIR__ . '/../classes/top_score_database.php';

$osuParser->getTableData();
$result = $osuParser->getScoreTable();

//function getSliceOfArray(array $data, $counter)
//{
//    $input = [];
//    $input[] = $result['place'][$counter];
//    $input[] = $result['score'][$counter];
//    $input[] = $result['accuracy'][$counter];
//    $input[] = $result['player_name'][$counter];
//    $input[] = $result['max_combo'][$counter];
//    $input[] = $result['pp'][$counter];
//    $input[] = $result['time'][$counter];
//    $input[] = $result['mods'][$counter];
//
//   
//    return $input;
//}

$score = new TopScoreDatabase;


for ($counter = 0; $counter < sizeof($result['place']); $counter++) {
   
    $input = [];
    $input[] = $result['place'][$counter];
    $input[] = $result['score'][$counter];
    $input[] = $result['accuracy'][$counter];
    $input[] = $result['player_name'][$counter];
    $input[] = $result['max_combo'][$counter];
    $input[] = $result['pp'][$counter];
    $input[] = $result['time'][$counter];
    $input[] = $result['mods'][$counter];
    $score->insertRow(array_keys($result),$input);
}
echo $score->getTopScores();
