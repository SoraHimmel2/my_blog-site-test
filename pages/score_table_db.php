<?php

require __DIR__ . 'vendor/autoload.php';


class ScoreTable
{
    const DB_HOST = 'localhost';
    const DB_NAME = 'OsuScoreTable';
    const DB_USER = 'root';
    const DB_PASSWORD = 'sanctum';

    private $pdo = null;
    private $tableName = 'temporary_score_table';
    
    public function __construct()
    {
        $conStr = sprintf("mysql:host=%s; dbname=%s", self::DB_HOST, self::DB_NAME);
        try {
            $this->pdo = new PDO($conStr, self::DB_USER, self::DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        } catch (PDOException $e) {
            echo ($e->getMessage());
        }
        $this->createScoreTable();

    }

    public function __destruct()
    {
        $this->pdo = null;
    }
    private function createScoreTable()
    {
       
        $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS  `$this->tableName`
                (
                    place VARCHAR(4),
                    score   VARCHAR(30),
                    accuracy  VARCHAR(10),
                    name  VARCHAR(20),
                    pp   VARCHAR(5),
                    time  VARCHAR(30),
                    mods VARCHAR(30)
                );";
        $query = $this->pdo->prepare($sql);
        $query->execute();
    }
    public function insertRow($column, $columnValue)
    {

        $sql = "INSERT INTO `$this->tableName`  (`$column`) VALUES (:columnValue);";
        $query = $this->pdo->prepare($sql);

        $query->execute(['columnValue' => $columnValue]);

    }
    public function showData()
    {
        $sql = 'SELECT *
                FROM `$this->tableName`;';
        $result = $this->pdo->prepare($sql);
        $result->execute();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            print_r($row['pp'] . '<br/>');

        }

    }
}
