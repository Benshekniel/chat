<?php
// get_unseen_counts.php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$currentUserId = $_SESSION['userid'];
$DB = new Database();

$query = "SELECT users.id, 
                 (SELECT COUNT(*) FROM message WHERE sender = users.id AND receiver = :currentUserId AND seen = 0) AS unseen_count 
          FROM users";
$unseenCounts = $DB->read($query, ['currentUserId' => $currentUserId]);

header('Content-Type: application/json');
echo json_encode($unseenCounts);
