<?php 
// Rewrite of the auth class again(again)
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
        return $this->db;
    }
}


class ReAuth {
    private $db;
    private $config;
    
    private $user_id;
    private $username;
    private $user_alias;
    
    private $is_admin;
    private $is_logged_in;
    private $is_valid_session;

    private $session_hash;

    /**
     * Constructor of ReAuth class, initializes database connection and starts
     * session if not already started
     *
     * @param array $config Configuration array of db options: name, user, password, host.
     * With the keys 'db_name', 'db_user', 'db_pass', 'db_host'.
     * 
     * @return void
     */
    public function __construct($config) {
        $this->db = new ReDatabase($config['db_name'], $config['db_host'], $config['db_user'], $config['db_pass']);
        $this->config = $config;

        $this->user_id = "";
        $this->username = "";
        $this->user_alias = "";

        $this->is_admin = false;
        $this->is_logged_in = false;
        $this->is_valid_session = false;

        $this->session_hash = "";

        $this->session_start();
    }

    public function session_start() {
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    private function generate_session_hash() {
        // Generate a random string, taking into account the current time, username and a random number
        $session_hash = md5(time() . $this->username . rand());
        return $session_hash;
    }

    private function set_session_hash() {
        $this->session_hash = $this->generate_session_hash();
        $_SESSION['session_hash'] = $this->session_hash;

        // Hash again for the client side
        $cookie_hash = md5($this->session_hash);
        // Set domain to null if db_host is localhost else set it to the domain. (This is to get cookies working on localhost)
        $domain = ($this->config["db_host"] === 'localhost') ? null : $this->config["db_domain"];
        setcookie("session_hash", $cookie_hash, time() + (7200), "/", $domain);
    }

    public function test_session_hash() {
        $this->set_session_hash();
        echo "Cookie: " . $_COOKIE['session_hash'];
        echo "<br>";
        echo "Session: " . $_SESSION['session_hash'];
        echo "<br>";
        echo "This: " . $this->session_hash;
        echo "<br>";
        echo "This md5: " . md5($this->session_hash);
        
    }
}