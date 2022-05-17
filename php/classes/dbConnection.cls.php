<?php
class dbConnection {
    public $conn;
    
    public function __construct($db_config) {
        // connect to database mysqli
        $this->conn = new mysqli($db_config['db_host'], $db_config['db_user'], $db_config['db_password'], $db_config['db_name']);
        // check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}

?>