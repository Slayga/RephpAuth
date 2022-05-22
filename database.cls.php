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
    public function __construct($db_name, $db_host, $db_user, $db_password) {
        $this->db = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($this->db->connect_errno) {
            die("Failed to connect to MySQLI: (" . $this->db->connect_errno . ")");
        } else {
            return $this->db;
        }
    }

    public function get_connection(){
        // Returns the connection object
        return $this->db;
    }
}

?>