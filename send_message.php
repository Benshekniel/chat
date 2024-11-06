<?php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$sender = $_SESSION['userid'];
$receiver = $_POST['receiver'];
$message = $_POST['message'];
date_default_timezone_set('Asia/Colombo');
$date = date("Y-m-d H:i:s");

$DB = new Database();
$query = "INSERT INTO message (sender, receiver, message, date) VALUES (:sender, :receiver, :message, :date)";
$params = [
   'sender' => $sender,
   'receiver' => $receiver,
   'message' => $message,
   'date' => $date
];

if ($DB->write($query, $params)) {
   echo json_encode(["status" => "success", "message" => "Message sent."]);
} else {
   echo json_encode(["status" => "error", "message" => "Error sending message."]);
}
