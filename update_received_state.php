<?php
// login.php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

// Update messages as received
$DB = new Database();
$updateQuery = "UPDATE message SET received = 1 WHERE receiver = :receiver AND received = 0";
$DB->write($updateQuery, ['receiver' => $_SESSION['userid']]);
