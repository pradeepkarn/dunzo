<?php
#.....................................
#DO NOT EDIT BELOW
#.....................................
class Dbh
{
    #Database Connection Area#####################################################################
    private $host = PK_DB_HOST;
    private $user = PK_DB_USER;
    private $pwd = PK_DB_PASS;
    protected $dbName = PK_DB_NAME;

    public function conn()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName . ';charset=utf8';
        $pdo = new PDO($dsn, $this->user, $this->pwd);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
