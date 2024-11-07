<?php
// get_messages.php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$sender = $_SESSION['userid'];
$receiver = $_GET['receiver'];
$DB = new Database();

// Mark messages as seen when the receiver opens the chat
$updateSeenQuery = "UPDATE message SET seen = 1 WHERE sender = :receiver AND receiver = :sender AND seen = 0";
$DB->write($updateSeenQuery, ['receiver' => $receiver, 'sender' => $sender]);

// Fetch messages between the sender and receiver
$query = "SELECT * FROM message WHERE (sender = :sender AND receiver = :receiver) OR (receiver = :sender AND sender = :receiver) ORDER BY date ASC";
$params = ['sender' => $sender, 'receiver' => $receiver];
$messages = $DB->read($query, $params);

// Fetch the receiver's username
$queryReceiver = "SELECT username FROM users WHERE id = :receiver";
$receiverData = $DB->read($queryReceiver, ['receiver' => $receiver]);

// Check if receiver exists and get the username
$receiverUsername = $receiverData ? $receiverData[0]['username'] : 'Unknown User';

// Prepare the response
echo json_encode([
   "status" => "success",
   "username" => $receiverUsername,
   "messages" => $messages,
]);
