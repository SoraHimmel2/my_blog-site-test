class ScoreTable {
    const DB_HOST = 'localhost';
    const DB_NAME = 'OsuScoreTable';
    const DB_USER = 'root';
    const DB_PASSWORD = 'root';
    
    private $pdo = null;

    public function __construct(){
        $conStr = sprintf("mysql:host=%s; dbname=%s",self::DB_HOST, self::DB::NAME);
        try{
            $this->pdo = new PDO($conStr, self::DB_USER,self::DB_PASSWORD);
        } catch (PDOException $e){
            echo($e->getMessage());
        }
        }
    }
}