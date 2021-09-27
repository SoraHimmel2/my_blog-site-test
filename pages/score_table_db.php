<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


class ScoreTable
{
    private $databaseConfig = [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'sanctum',
        'db'   => 'OsuScoreTable',
        'port' => NULL,
        'socket' => NULL,
        'pconnect' => FALSE,
        'charset' => 'utf8',
        'errmode' => 'exception',
        'exception' => 'Exception'
        ];
    

    private $database = null;
    private $tableName = 'temporary_score_table';

    public function __construct()
    {
        $this->database = new SafeMySQL($this->databaseConfig);
        $this->createScoreTable();

    }

    public function __destruct()
    {
        $this->database = null;
    }
    private function createScoreTable()
    {
       
        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS  ?n
                (
                    place VARCHAR(4),
                    score   VARCHAR(30),
                    accuracy  VARCHAR(10),
                    name  VARCHAR(20),
                    pp   VARCHAR(5),
                    time  VARCHAR(30),
                    mods VARCHAR(30)
                );";
        $this->database->query($sql,$this->tableName);
    }
    public function insertRow($column, $columnValue)
    {

        $sql = "INSERT INTO ?n  (?n) VALUES (?s);";
        $this->database->query($sql,$this->tableName,$column,$columnValue);

    }
    public function showData()
    {
        $sql = 'SELECT *
                FROM ?n;';
    
        $rows = $this->database->getAll($sql,$this->tableName);
        foreach($rows as $row) {

            print_r($row['pp'] . '<br/>');

        }

    }
}
