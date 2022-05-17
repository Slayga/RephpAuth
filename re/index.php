<?php 
require_once 'config.php';
require_once "auth.cls.php";

$auth = new ReAuth($config["db"]);

$auth->test_session_hash();