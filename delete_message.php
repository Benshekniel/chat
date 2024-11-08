<?php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$currentUserId = $_SESSION['userid'];
$messageId = $_POST['message_id'];
$isSender = $_POST['is_sender'];

$DB = new Database();

if ($isSender) {
   $query = "UPDATE message SET deleted_sender = 1 WHERE id = :messageId AND sender = :currentUserId";
} else {
   $query = "UPDATE message SET deleted_receiver = 1 WHERE id = :messageId AND receiver = :currentUserId";
}

$params = ['messageId' => $messageId, 'currentUserId' => $currentUserId];
$result = $DB->write($query, $params);

if ($result) {
   echo json_encode(["status" => "success"]);
} else {
   echo json_encode(["status" => "error", "message" => "Could not delete the message."]);
}
