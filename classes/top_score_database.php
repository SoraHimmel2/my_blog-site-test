<?php

require_once __DIR__ . '/../vendor/autoload.php';

class TopScoreDatabase
{
    public function __construct()
    {
        $this->modRegex['diff_decrease'] = 'Half Time|No Fail';
        $this->modRegex['one'] = '^_([^_]+_){1}$|^_$';
        $this->modRegex['two'] = '^_([^_]+_){2}$| ^_(Double Time|Flashlight|Nightcore)_$';
        $this->modRegex['three'] = '^_([^_]+_){3}$';
        $this->modRegex['four'] = '^_([^_]+_){4}$';
        $this->modRegex['diff_decrease_condition'] = '^_$|^Hidden$|^Hard Rock$';

        $this->modPlaceNumber['diff_decrease'] = 12;
        $this->modPlaceNumber['one'] = 15;
        $this->modPlaceNumber['two'] = 8;
        $this->modPlaceNumber['three'] = 8;
        $this->modPlaceNumber['four'] = 8;

        $this->modCountCondition['diff_decrease'] = 25;
        $this->modCountCondition['one'] = 30;
        $this->modCountCondition['two'] = 8;
        $this->modCountCondition['three'] = 5;
        $this->modCountCondition['four'] = 4;

        $this->database = new SafeMySQL($this->databaseConfig);
        $this->createScoreTable();

    }

    public function __destruct()
    {
        $this->database = null;
    }

    private $database = null;
    private $tableName = 'space';
    private $modRegex = [];
    private $modPlaceNumber = [];
    private $modCountCondition = [];
    private $placeMainCondition = 8;

    private $databaseConfig = [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'sanctum',
        'db' => 'OsuScoreTable',
        'port' => null,
        'socket' => null,
        'pconnect' => false,
        'charset' => 'utf8',
        'errmode' => 'exception',
        'exception' => 'Exception',
    ];

    private function createScoreTable()
    {

        $sql = "CREATE  TABLE IF NOT EXISTS  ?n
                (
                    place       VARCHAR(4),
                    score       VARCHAR(30),
                    accuracy    VARCHAR(10),
                    player_name VARCHAR(20),
                    max_combo   VARCHAR(20),
                    pp          VARCHAR(5),
                    time        VARCHAR(30),
                    mods        VARCHAR(100)
                );";
        $this->database->query($sql, $this->tableName);
    }
    private function showAllRow($rows)
    {
        foreach ($rows as $row) {

            print_r($row['place'] . " ");
            print_r($row['score'] . " ");
            print_r($row['accuracy'] . " ");
            print_r($row['player_name'] . " ");
            print_r($row['max_combo'] . " ");
            print_r($row['pp'] . " ");
            print_r($row['time'] . " ");
            print_r($row['mods'] . " ");
            echo " " . "<br/>";

        }
    }
    private function getModCount($modRegExpression)
    {
        $sql = "
            SELECT
                SUM(total.result)
            FROM (
                SELECT
                    COUNT(*) as result
                FROM
                    ?n
                WHERE mods REGEXP  ?s
                GROUP BY mods)  total
            ;";
        $result = $this->database->getOne($sql, $this->tableName, $modRegExpression);
        return $result;
    }
    private function getTopScoreRows($regex, $place)
    {
        $sql = "SELECT *
                FROM
                    ?n
                WHERE
                    time  REGEXP '^[1-9][0-9]m$|^[1-4]d$' AND
                    (( mods REGEXP ?s AND place <= ?i) OR place <=?i)
                ;"
        ;
        $result = $this->database->getAll($sql, $this->tableName,$regex, $place, $this->placeMainCondition);
        $this->showAllRow($result);

    }
    public function insertRow(array $column, array $columnValues)
    {

        $sql = "INSERT INTO ?n  (?n,?n,?n,?n,?n,?n,?n,?n) VALUES (?a);";
        $this->database->query($sql, $this->tableName,
            $column[0],
            $column[1],
            $column[2],
            $column[3],
            $column[4],
            $column[5],
            $column[6],
            $column[7],
            $columnValues);

    }
    public function showData()
    {
        $sql = 'SELECT *
                FROM ?n;';

        $rows = $this->database->getAll($sql, $this->tableName);
        foreach ($rows as $row) {

            print_r($row['place'] . " ");
            print_r($row['score'] . " ");
            print_r($row['accuracy'] . " ");
            print_r($row['player_name'] . " ");
            print_r($row['max_combo'] . " ");
            print_r($row['pp'] . " ");
            print_r($row['time'] . " ");
            print_r($row['mods'] . " ");
            echo " " . "<br/>";

        }

    }
    public function getTopScores()
    {

        if ($this->getModCount($this->modRegex['diff_decrease']) > $this->modCountConditon['diff_decrease']) {
            $this->getTopScoreRows($this->modRegex['diff_decrease_condition'], $this->modPlaceNumber['diff_decrease']);
        }
        if ($this->getModCount($this->modRegex['one']) > $this->modCountConditon['one']) {
            $this->getTopScoreRows($this->modRegex['two'].'|'.$this->modRegex['three'], $this->modPlaceNumber['one']);
        }
        if ($this->getModCount($this->modRegex['two']) > $this->modCountConditon['two']) {
            $this->getTopScoreRows($this->modRegex['three'].'|'.$this->modRegex['four'], $this->modPlaceNumber['two']);
        }
        if ($this->getModCount($this->modRegex['three']) > $this->modCountConditon['three']) {
            $this->getTopScoreRows($this->modRegex['four'], $this->modPlaceNumber['three']);
        }
        if ($this->getModCount($this->modRegex['four']) > $this->modCountConditon['four']) {
            $this->getTopScoreRows($this->modRegex['four'], $this->modPlaceNumber['four']);
        }

    }
}
