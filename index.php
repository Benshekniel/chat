<?php
session_start();

if (!isset($_SESSION['userid'])) {
   header("Location: login.php");
   exit();
}

require_once("Database.php");
$DB = new Database();
$currentUserId = $_SESSION['userid'];

// Query to get users and unseen status of the last message, excluding the logged-in user
$query = "SELECT users.*, 
          (SELECT seen FROM message 
           WHERE (sender = users.id AND receiver = :currentUserId) 
           OR (sender = :currentUserId AND receiver = users.id) 
           ORDER BY date DESC LIMIT 1) AS seen,
          (SELECT COUNT(*) FROM message WHERE sender = users.id AND receiver = :currentUserId AND seen = 0) AS unseen_count
          FROM users
          WHERE users.id != :currentUserId";  // Exclude the logged-in user
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
                           <div class="chat-item <?php echo ($user['unseen_count'] > 0) ? 'unseen' : ''; ?>"
                              data-receiver-id="<?php echo $user['id']; ?>"
                              onclick="selectChat(this, <?php echo $user['id']; ?>)">
                              <div class="avatar"></div>
                              <div class="chat-info">
                                 <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                 <p class="chat-status"><?php echo $user['state'] ? 'Online' : 'Offline'; ?></p> <!-- Initial status -->
                              </div>
                              <div class="chat-side">
                                 <span class="time">9:00am</span>
                                 <span class="circle"></span> <!-- Indicator for unseen messages -->
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
                        <p id="chat-status">Offline</p> <!-- Status will be updated dynamically -->
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
   <script>
      let selectedUserId = null; // Track the selected user ID globally

      function selectChat(chatItem, userId) {
         // Set selected user ID for status updates in the header
         selectedUserId = userId;

         // Update chat header with selected user's info
         const username = chatItem.querySelector('.chat-info h4').textContent;
         const userStatus = chatItem.querySelector('.chat-status').textContent;

         document.getElementById('chat-username').textContent = username;
         document.getElementById('chat-status').textContent = userStatus;

         // Load messages when a user is selected (this could be done by fetching messages for the user)
         startChat(userId);
      }

      function startChat(receiverId) {
         // Load chat messages for the selected user
         fetch(`get_messages.php?receiver=${receiverId}`)
            .then(response => response.json())
            .then(data => {
               const chatMessages = document.getElementById("chat-messages");
               chatMessages.innerHTML = '';
               data.messages.forEach(message => {
                  const div = document.createElement('div');
                  div.classList.add('message', message.sender === receiverId ? 'received' : 'sent');
                  div.innerHTML = `
                     <p>${message.message}</p>
                     <span class="time">${message.date}</span>
                  `;
                  chatMessages.appendChild(div);
               });

               chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the latest message

               const chatUsername = document.getElementById("chat-username");
               chatUsername.innerText = data.username;
            });
      }

      // Send message to the server
      function sendMessage() {
         const message = document.getElementById('message-input').value;

         if (!selectedUserId) {
            alert("Receiver ID is not set. Please select a chat.");
            return;
         }

         fetch('send_message.php', {
               method: 'POST',
               body: new URLSearchParams({
                  receiver: selectedUserId,
                  message: message,
               }),
            })
            .then(response => response.json())
            .then(data => {
               if (data.status === "success") {
                  pollMessages(); // Fetch new messages immediately after sending
                  document.getElementById('message-input').value = ''; // Clear the input
               } else {
                  alert('Error sending message');
               }
            });
      }

      function pollMessages() {
         if (selectedUserId) {
            fetch(`get_messages.php?receiver=${selectedUserId}`)
               .then(response => response.json())
               .then(data => {
                  const chatMessages = document.getElementById("chat-messages");
                  chatMessages.innerHTML = '';
                  data.messages.forEach(message => {
                     const div = document.createElement('div');
                     div.classList.add('message', message.sender === selectedUserId ? 'received' : 'sent');
                     div.innerHTML = `
                        <p>${message.message}</p>
                        <span class="time">${message.date}</span>
                     `;
                     chatMessages.appendChild(div);
                  });

                  chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to the latest message
               });
         }
      }

      // Set interval to poll unseen message counts every 5 seconds
      setInterval(pollMessages, 5000);

      // Refresh user statuses every 5 seconds
      setInterval(refreshUserStatuses, 5000);

      function refreshUserStatuses() {
         fetch('get_user_status.php')
            .then(response => response.json())
            .then(users => {
               users.forEach(user => {
                  const chatItem = document.querySelector(`.chat-item[data-receiver-id="${user.id}"]`);
                  if (chatItem) {
                     const statusElement = chatItem.querySelector('.chat-status');
                     statusElement.textContent = user.state ? 'Online' : 'Offline';
                  }
               });
            });
      }
   </script>
</body>

</html>