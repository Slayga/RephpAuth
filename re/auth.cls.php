<?php 
// Re-rewrite of the auth class again(again)
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


class ReAuth {
    private $db;
    private $config;
    
    private $user_id;
    private $username;
    private $user_alias;
    
    private $is_admin;
    private $is_logged_in;
    private $is_authenticated_session;

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
        $this->is_authenticated_session = false;

        $this->session_start();
    }

    public function session_start() {
        $return["error"] = true;
        $return["message"] = "Unknown error";
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_start();
            
            $return["error"] = false;
            $return["message"] = "Session started";
        } else {
            $return["message"] = "Session already started";
        }
        return $return;
    }

    public function session_destroy() {
        session_destroy();
    }

    public function session_regenerate() {
        session_regenerate_id(true);
    }

    public function session_restart() {
        $this->session_destroy();
        $this->session_start();
        $this->session_regenerate_id();
    }

    public function is_logged_in() {
        // Check again if user is logged in.
        // If not, check if there is a session
        if(!$this->is_logged_in) {
            if(isset($_SESSION["user_id"]) && isset($_SESSION["username"]) && isset($_SESSION["user_alias"])) {
                $this->user_id = $_SESSION["user_id"];
                $this->username = $_SESSION["username"];
                $this->user_alias = $_SESSION["user_alias"];
                $this->is_logged_in = true;
            } else {
                $this->is_logged_in = false;
            }
        }
        return $this->is_logged_in;
    }

    public function is_authenticated_session() {
        if ($this->is_logged_in()) {
            return $this->is_authenticated_session;
        } else {
            return false;
        }
        return $this->is_authenticated_session;
    }

    public function is_admin() {
        return $this->is_admin;
    }

    public function get_user_id() {
        return $this->user_id;
    }

    public function get_username() {
        return $this->username;
    }

    public function get_user_alias() {
        return $this->user_alias;
    }

    public function login($username, $password) {
        $return["error"] = true;
        $return["message"] = "Unknown error when logging in";

        if ($this->is_logged_in) {
            $return["message"] = "Already logged in";
            return $return;
        }
        // Prepared statement, for best security practice.
        // Get the username from db, and grab the password to verify against input.
        $stmt = $this->db->prepare("SELECT id, username, password, alias, is_admin FROM users WHERE username = ?"); 
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $db_username, $password_hash, $alias, $is_admin);
        $stmt->fetch();
        $stmt->close();
        
        // If the username exists, compare the password.
        if($db_username !== null) {
            if(password_verify($password, $password_hash)) {
                // If the password is correct, set the session variables.
                $_SESSION['user_id'] = $this->user_id = $id;
                $_SESSION['username'] = $this->username = $db_username;
                $_SESSION['user_alias'] = $this->user_alias = $alias;
                $_SESSION['is_logged_in'] = $this->is_logged_in = true;
                $_SESSION['is_authenticated_session'] = $this->is_authenticated_session = true;
                $_SESSION['is_admin'] = $this->is_admin = $is_admin;

                $return["error"] = false;
                $return["message"] = "Logged in";
                return $return;
            } else {
                // If the password is incorrect, return false.
                $this->session_restart();
                $return["message"] = "Incorrect password";
                return $return;
            }
        } else {
            // If the username doesn't exist, return false.
            $this->session_restart();
            $return["message"] = "Username doesn't exist";
            return $return;
        }
        return $return;
    }

    public function logout() {
        $return["error"] = true;
        $return["message"] = "Unknown error when logging out";
        // Unset all of the session variables.
        $_SESSION = array();

        // Unset all object variables.
        $this->user_id = "";
        $this->username = "";
        $this->user_alias = "";

        $this->is_admin = false;
        $this->is_logged_in = false;
        $this->is_authenticated_session = false;

        $this->session_restart();

        $return["error"] = false;
        $return["message"] = "Logged out";
        return $return;
    }

    public function signup($username, $password, $alias, $auto_login) {
        $return["error"] = true;
        $return["message"] = "Unknown error when signing up";

        # Check if username is 3-40 characters long, Characters, number and ". ! _ -" are allowed.
        if (!preg_match("/^[a-zA-Z0-9 .!_-]{3,40}+$/", $username)) {
            $return["message"] = "Invalid username";
            return $return;
        }
        if (!preg_match("/^[a-zA-Z0-9 .!_-]{7,255}+$/", $password)) {
            $return["message"] = "Invalid password";
            return $return;
        }
        
        // Prepared statement, for best security practice.
        // Check for username availability.
        $stmt = $this->db->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($db_username);
        $stmt->fetch();
        $stmt->close();

        // If the username is available, create the user.
        if($db_username === null) {
            // Hash the password.
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database.
            $stmt = $this->db->prepare("INSERT INTO users (username, password, alias) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password_hash, $alias);
            $stmt->execute();
            $stmt->close();

            $return["error"] = false;
            $return["message"] = "Signed up";

            // If the user is to be auto-logged in, log them in.
            if($auto_login) {
                $auto_login_result = $this->login($username, $password);
                if($auto_login_result["error"]) {
                    // Append the error message to current message if any.
                    $return["message"] .= " | " . $auto_login_result["message"];
                    $return["error"] = true;
                    return $return;
                }
            }

            
            return $return;
        } else {
            // If the username is not available, return false.
            $return["message"] = "Username already exists";
            $return["error"] = true;
            return $return;
        }

    }
}


class ReGallery {
    private $db;
    private $username;
    
    public function __construct($db, $username) {
        $this->db = $db;
        $this->username = $username;
    }

    public function create_post($name, $description, $is_public, $posted_by, $filename) {
        $return["error"] = true;
        $return["message"] = "Unknown error";

        // $_FILE global variable.
        $file_path = $_FILES[$filename]["tmp_name"];
        $file_size = filesize($file_path);
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        if (empty($file_info)) {
            $return["message"] = "Could not get file info";
            return $return;
        }
        $file_type = finfo_file($file_info, $file_path);

        // Check if the file is an image.
        if(!in_array($file_type, array("image/jpeg", "image/png", "image/gif"))) {
            $return["message"] = "File is not an image";
            return $return;
        }

        // The max file size is 10MB.
        if($file_size > 10000000) {
            $return["message"] = "File is too large";
            return $return;
        }

        // Rename the file, take into account: date, time, username, random md5 hash.
        $file_name = date("Y-m-d-H-i-s") . "-" . $this->username . "-" . md5(uniqid(rand(), true)) . "." . pathinfo($_FILES[$filename]["name"], PATHINFO_EXTENSION);

        // Move the file to the gallery folder and subdirectory of the username
        move_uploaded_file($file_path, "../gallery/" . $posted_by . "/" . $file_name);

        // New path to the post.
        $path = "gallery/" . $posted_by . "/" . $file_name;

        // Insert the post into the database.
        $stmt = $this->db->prepare("INSERT INTO gallery (name, description, is_public, posted_by, path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $description, $is_public, $posted_by, $file_name);
        $stmt->execute();
        $stmt->close();

        $return["error"] = false;
        $return["message"] = "Created post";
        return $return;
    }

}