<?php
//get_unseen_counts.php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$currentUserId = $_SESSION['userid'];
$DB = new Database();

// Query to get users with unseen message counts and last message date, prioritized by unseen messages
$query = "SELECT users.*, 
          (SELECT seen FROM message 
           WHERE (sender = users.id AND receiver = :currentUserId) 
           OR (sender = :currentUserId AND receiver = users.id) 
           ORDER BY date DESC LIMIT 1) AS seen,
          (SELECT date FROM message 
           WHERE (sender = users.id AND receiver = :currentUserId) 
           OR (sender = :currentUserId AND receiver = users.id) 
           ORDER BY date DESC LIMIT 1) AS last_message_date,
          (SELECT COUNT(*) FROM message 
           WHERE sender = users.id AND receiver = :currentUserId AND seen = 0) AS unseen_count
          FROM users
          WHERE users.id != :currentUserId
          ORDER BY 
             unseen_count DESC,  -- Users with unseen messages come first
             last_message_date DESC";  // Order by last message date if unseen count is the same"; // Prioritize unseen count, then last message date

$unseenCounts = $DB->read($query, ['currentUserId' => $currentUserId]);

header('Content-Type: application/json');
echo json_encode($unseenCounts);
