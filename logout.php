<?php
require_once 'config.php';

// Destroy session
session_destroy();

// Redirect to login
redirect('login.php');
?>
