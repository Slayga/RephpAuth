<?php 

class ReDatabase {
    private $db;

    /**
     * Constructor of ReDatabase class, initializes and returns 
     * a database connection (OOP, not procedural)
     *
     * @param string $db_name
     * @param string $db_host
     * @param string $db_user
     * @param string $db_password
     * 
     * @return mysqli
     */
    public function __construct($db) {
        $this->db = new mysqli($db['db_host'], $db['db_user'], $db['db_pass'], $db['db_name']);
        if ($this->db->connect_errno) {
            die("Failed to connect to MySQLI: (" . $this->db->connect_errno . ")");
        }
    }

    public function get_connection(){
        // Returns the connection object
        return $this->db;
    }
}

?>