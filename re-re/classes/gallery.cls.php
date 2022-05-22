<?php

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