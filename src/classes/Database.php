<?php

class Database {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $dbname;
    private $pdo;
    private $stmt;

    public function __construct() {
    $url = getenv('DATABASE_URL');
    if (!$url) {
        die("DATABASE_URL not set.");
    }

    $dbopts = parse_url($url);
    $dsn = "pgsql:host={$dbopts['host']};port={$dbopts['port']};dbname=" . ltrim($dbopts['path'], '/');
    $this->user = $dbopts['user'];
    $this->pass = $dbopts['pass'];
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            echo "PostgreSQL connection failed: " . $e->getMessage();
            die();
        }
    }

    public function query($sql) {
        $this->stmt = $this->pdo->prepare($sql);
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
