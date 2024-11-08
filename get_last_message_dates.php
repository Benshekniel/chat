<?php
session_start();
require_once("Database.php");

if (!isset($_SESSION['userid'])) {
   echo json_encode([]);
   exit();
}

$DB = new Database();
$currentUserId = $_SESSION['userid'];

$query = "SELECT users.id, 
                 DATE_FORMAT((SELECT date FROM message 
                              WHERE (sender = users.id AND receiver = :currentUserId) 
                              OR (sender = :currentUserId AND receiver = users.id) 
                              ORDER BY date DESC LIMIT 1), '%d/%m/%Y') AS date
          FROM users
          WHERE users.id != :currentUserId";

$users = $DB->read($query, ['currentUserId' => $currentUserId]);

echo json_encode($users);
