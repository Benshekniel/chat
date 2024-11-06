<?php
// index.php
session_start();

if (!isset($_SESSION['userid'])) {
   header("Location: login.php");
   exit();
}

require_once("Database.php");
$DB = new Database();
$currentUserId = $_SESSION['userid'];

// Query to get users with the unseen status of the last message between them
$query = "SELECT users.*, 
          (SELECT seen FROM message WHERE 
           (sender = users.id AND receiver = :currentUserId) 
           OR (sender = :currentUserId AND receiver = users.id) 
           ORDER BY date DESC LIMIT 1) AS seen 
          FROM users";
$users = $DB->read($query, ['currentUserId' => $currentUserId]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="assets/css/message.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
   <div class="dashboard-container">
      <div class="main-content">
         <div class="dashboard-content">
            <div class="container">
               <div class="chat-list">
                  <div class="search-bar">
                     <input type="text" placeholder="Search">
                  </div>
                  <ul id="chat-list">
                     <?php foreach ($users as $user): ?>
                        <li>
                           <div class="chat-item <?php echo (isset($user['seen']) && $user['seen'] == 0) ? 'unseen' : ''; ?>"
                              data-receiver-id="<?php echo $user['id']; ?>"
                              onclick="selectChat(this, <?php echo $user['id']; ?>)">
                              <div class="avatar"></div>
                              <div class="chat-info">
                                 <h4><?php echo $user['username']; ?></h4>
                                 <p>Sent attachment</p>
                              </div>
                              <div class="chat-side">
                                 <span class="time">9:00am</span>
                                 <span class="circle"></span> <!-- Green circle for unseen messages -->
                              </div>
                           </div>
                        </li>
                     <?php endforeach; ?>
                  </ul>
               </div>

               <div class="chat-window" id="chat-window">
                  <div class="chat-header">
                     <div class="avatar"></div>
                     <div class="header-info">
                        <h4 id="chat-username">Select a user</h4>
                        <p>Offline</p>
                     </div>
                  </div>
                  <div class="chat-messages" id="chat-messages">
                     <!-- Messages will appear here -->
                  </div>
                  <div class="chat-input">
                     <input type="text" id="message-input" placeholder="Type a message">
                     <button onclick="sendMessage()">Send</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script src="assets/js/message.js"></script>
</body>

</html>