<?php 
// require dbConnection
require_once __DIR__.'./dbConnection.cls.php';
// require config
require_once __DIR__.'/../includes/config.php';

session_start();

class Auth {
    /**
     * object of class dbConnection
     *
     * @var object
     */
    private $db_conn;
    private $isLoggedIn;
    private $isValid;
    private $admin;
    private $sessionHash;
    private $user;
    private $uid;
    private $username;

    public function __construct($db_conn) {
        $this->db_conn = $db_conn;
        $this->isLoggedIn = 0;
        $this->isValid = 0;
        $this->admin = 0;
        $this->sessionHash = "";
        $this->user = "";
        $this->uid = "";
        $this->startSession();
        $this->checkSession();
    }

    public function startSession() {
        // If no session has been started yet, start one
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function isLoggedIn() {
        
    }

    public function checkSession() {
        
    }

    public function isValidSession() {
    }
    
    public function generateSessionHash() {
        // generate a unique session hash for the user
        $hash = $this->username . $this->uid . time();
        $hash = $hash . rand(0, 99999);
        // hash the session hash
        $hash = hash('sha256', $hash);
        return $hash;
    }

    public function getSession() {
        return session_status();
    }
    
    public function isAdmin() {
        // check session vars
        if (isset($_SESSION['admin'])) {
            $this->admin = $_SESSION['admin'];
        } else {
            $this->admin = 0;
        }
        return $this->admin;
    }

    public function isValid() {
        $this->isValid = $this->isValidSession();
        return $this->isValid;
    }

    public function getUser() {
        return $this->user;
    }

    public function getUid() {
        return $this->uid;
    }

    public function getUsername() {
        return $this->username;
    }

    public function login($username, $password) {
        // check if user, password combination exists prepared statement
        $stmt = $this->db_conn->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        // check if user, password combination exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // check if password is correct
            if(password_verify($password, $user['password'])){
                
                $_SESSION['isLoggedIn'] = 1;
                $_SESSION['uid'] = $user['uid'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['admin'] = $user['admin'];
                $uHash = $this->generateSessionHash();
                $_SESSION['sessionHash'] = $uHash;
                
                $this->isLoggedIn = 1;
                $this->uid = $user['uid'];
                $this->username = $user['username'];
                $this->admin = $user['admin'];
                $this->sessionHash = $_SESSION['sessionHash'];

                $cookieHash = $this->sessionHash;
                // hash the $cookieHash
                $cookieHash = hash('sha256', $cookieHash);
                
                // set cookie for 2 hours
                $rawHash = $this->sessionHash;
                setcookie('rawHash', "", time() + 7200, "../../");
                setcookie('sessionHash', $cookieHash, time() + 7200, "../../");
                
                // return true
                return true;
            } else {
                die("Password is incorrect <a href='index.php'>Try again</a>");
            }
        } else {
        // return false
        return false;
        }
    }

    public function signup($username, $password, $alias) {
        // check if user exists
        $stmt = $this->db_conn->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        // check if user exists
        if ($result->num_rows > 0) {
            // user exists
            die("User already exists <a href='index.php'>Homepage</a>");
        } else {
            // user does not exist
            // insert user
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db_conn->conn->prepare("INSERT INTO users (username, password, alias) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $alias);
            $stmt->execute();
            $stmt->close();
            
            // login
            if ($this->login($username, $password)) {
                return true;
            } else {
                die("Signup successful, but failed to login, <a href='index.php'>Homepage</a>");

                return false;
            };
        }
    }

    public function logout() {
        // destroy all Auth & Session variables
        $this->isLoggedIn = 0;
        $this->isValid = 0;
        $this->admin = 0;
        $this->sessionHash = "";
        $this->user = "";
        $this->uid = "";
        $this->username = "";
        // // destroy cookie
        unset($_COOKIE['rawHash']);
        unset($_COOKIE['sessionHash']);
        // setcookie('rawHash', "", time() - 36000);
        // setcookie('sessionHash', "", time() - 36000);
        
        // Destroy if session is running
        if (session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
        }
        // send to homepage
        // header("Location: ");
    } 

    public function getSessionHash() {
        return $this->sessionHash;
    }

    public function getCookieHash() {
        return $_COOKIE['sessionHash'] ?? null;
    }
    
}


?>