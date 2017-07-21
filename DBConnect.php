<?php

namespace csvimport;
use csvimport\DBConnectInterface;

include_once './DBConnectInterface.php';

class DBConnect implements DBConnectInterface {
    
    private $host;
    private $user;
    private $password;
    private $database;
    
    private $conn;
    
    public function __construct($host, $user, $password, $database) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        
        $this->connect();
    }
    
    public function connect() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        mysqli_query($this->conn, 'SET NAMES utf8');
        mysqli_query($this->conn, 'SET CHARACTER_SET utf8_unicode_ci');
    }

    public function execute($sql) {
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }
    
    public function closeConnection(){
        mysqli_close($this->conn);
        return null;
    }
}
