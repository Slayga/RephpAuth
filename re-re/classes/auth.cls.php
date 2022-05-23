<?php 
// Re-Re-rewrite of the auth class again(again(again))
include_once __DIR__ . "/../config.php";
include_once __DIR__ . "/database.cls.php";

class ReAuthentication {
    // What will authenticate do?
    // (0.) Constructor will be used to set up the database connection
    // 1. Handle login
    // 2. Handle logout
    // 3. Handle signup
    // 4. Handle session ($_SESSION)
    // 5. Handle class variables (is_logged in, is_admin, etc)
    // 6. Handle user data (user_id, username, etc)
    // 7. Handle user permissions (can_edit, can_delete, etc)
    
    // Constructor
    public function __construct($config) {
        $this->db = new ReDatabase($config['db']);
        $this->dbConn = $this->db->get_connection();
        $this->config = $config;
        
        // User data
        $this->user_id = null;
        $this->username = null;
        $this->is_logged = false;
        $this->is_admin = false;
        $this->is_banned = false;
        $this->is_guest = true;

        // User permissions, mainly for gallery.. Example: can_edit & can_delete.
        $this->permissions = array();

        // Session
        $this->check_session();
    }
    
    // 1. Handle login
    public function login($username, $password) {
        $return['error'] = true;
        $return['message'] = "Login: Unknown error";

        // Check if user exists
        $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 1) {
            $user_data = $result->fetch_assoc();

            // Check if password is correct
            if (password_verify($password, $user_data['password'])) {
                // Pop the password from user_data for security
                unset($user_data['password']);
                
                $this->set_session($user_data);
                $this->set_class($user_data);
                
                $return['error'] = false;
                $return['message'] = "Login: Success";
            } else {
                $return['message'] = "Login: Wrong password";
            }
        } else {
            $return['message'] = "Login: User not found";
        }
        return $return;
    }

    // 2. Handle logout
    public function logout() {
        $this->unset_session();
        $this->unset_class();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $return['error'] = false;
        $return['message'] = "Logout: Success";
        return $return;
        
    }

    // 3. Handle signup
    public function signup($username, $password, $log_me_in) {
        // Create return array, with "error" and "message" keys
        $return['error'] = true;
        $return['message'] = "Signup: Unknown error";

        // Check if username is already taken, stmt
        $stmt = $this->dbConn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($stmt_result);
        $stmt->fetch();
        $stmt->close();

        // Check if username is already taken, if so, return error
        if ($stmt_result) {
            $return['message'] = "Signup: Username already taken";
            return $return;
        }

        // Preg match username, password (use Regex from ReBranded-Gallery)
        if (!preg_match("/^[a-zA-Z0-9 .!_-]{3,20}+$/", $username)) {
            $return['message'] = "Signup: Username must be between 3 and 20 characters and can only contain letters, numbers and the following symbols: . ! _ -";
            return $return;
        }

        if (!preg_match("/^[a-zA-Z0-9 .!_-]{6,255}+$/", $password)) {
            $return['message'] = "Signup: Password must be between 6 and 255 characters and can only contain letters, numbers and the following symbols: . ! _ -";
            return $return;
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $this->dbConn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password_hash);
        $stmt->execute();
        $stmt->close();

        // Auto login
        if ($log_me_in) {
            $login_result = $this->login($username, $password);
            unset($password);
            if ($login_result['error']) {
                $return['message'] = "Signup: User signed up, but auto login failed" . " | " . $login_result['message'];
                return $return;
            } else{
                $return['error'] = false;
                $return['message'] = "Signup: User signed up and auto logged in";
                return $return;
            }
        } else {
            unset($password);
            $return['error'] = false;
            $return['message'] = "Signup: User signed up";
            return $return;
        }
    }

    private function unset_session() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['is_logged']);
        unset($_SESSION['is_admin']);
        unset($_SESSION['is_banned']);
        unset($_SESSION['is_guest']);

        unset($_SESSION["permissions"]);
    }

    private function unset_class() {
        $this->user_id = null;
        $this->username = null;
        $this->is_logged = false;
        $this->is_admin = false;
        $this->is_banned = false;
        $this->is_guest = true;

        $this->permissions = array();
    }
    
    private function check_session() {
        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->username = $_SESSION['username'];
            $this->is_logged = $_SESSION['is_logged'];
            $this->is_admin = $_SESSION['is_admin'];
            $this->is_banned = $_SESSION['is_banned'];
            $this->is_guest = $_SESSION['is_guest'];

            $this->permissions = $_SESSION["permissions"];
        }
    }

    private function set_session($user_data) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['is_logged'] = true;
        $_SESSION['is_admin'] = $user_data['is_admin'];
        $_SESSION['is_banned'] = $user_data['is_banned'];
        $_SESSION['is_guest'] = false;

        $_SESSION["permissions"] = $user_data["permissions"];
    }

    private function set_class($user_data) {
        $this->user_id = $user_data['id'];
        $this->username = $user_data['username'];
        $this->is_logged = true;
        $this->is_admin = $user_data['is_admin'];
        $this->is_banned = $user_data['is_banned'];
        $this->is_guest = false;

        $this->permissions = $user_data["permissions"];
    }
}