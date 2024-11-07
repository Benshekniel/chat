<?php
// mark_messages_seen.php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$receiver = $_POST['receiver'];  // Receiver ID passed from JS
$sender = $_SESSION['userid'];  // Current logged-in user

// Update the "seen" status of messages
$DB = new Database();
$query = "UPDATE message SET seen = 1 WHERE sender = :receiver AND receiver = :sender AND seen = 0";
$params = ['receiver' => $receiver, 'sender' => $sender];

if ($DB->write($query, $params)) {
   echo json_encode(["status" => "success", "message" => "Messages marked as seen."]);
} else {
   echo json_encode(["status" => "error", "message" => "Error marking messages as seen."]);
}
