<?php
session_start();
require_once("Database.php");
$DB = new Database();

if (!isset($_SESSION['userid'])) {
   header("Location: login.php");
   exit();
}

$currentUserId = $_SESSION['userid'];

// Query to get the status of all users, excluding the logged-in user
$query = "SELECT users.id, users.state FROM users WHERE users.id != :currentUserId";
$users = $DB->read($query, ['currentUserId' => $currentUserId]);

// Return the updated status for all users
echo json_encode($users);
