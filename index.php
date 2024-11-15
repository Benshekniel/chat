<?php
session_start();

if (!isset($_SESSION['userid'])) {
   header("Location: login.php");
   exit();
}

require_once("Database.php");
$DB = new Database();
$currentUserId = $_SESSION['userid'];

// Query to get users with unseen messages first
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
             last_message_date DESC";  // Order by last message date if unseen count is the same

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
                                 <p class="chat-status"><?php echo $user['state'] ? 'Online' : 'Offline'; ?></p>
                              </div>
                              <div class="chat-side">
                                 <span class="time" id="time-<?php echo $user['id']; ?>">
                                    <?php
                                    echo !empty($user['last_message_date'])
                                       ? date('d/m/Y', strtotime($user['last_message_date']))
                                       : '';
                                    ?>
                                 </span>
                                 <span class="circle"></span>
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
                        <p id="chat-status">Offline</p>
                     </div>
                  </div>
                  <div class="chat-messages" id="chat-messages">
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

   <!-- Popup Menu for Message Options -->
   <div id="popup-menu" style="display: none; position: absolute; background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.2); border-radius: 8px; padding: 10px;">
      <ul style="list-style: none; padding: 0; margin: 0;">
         <li onclick="deleteMessage()" style="padding: 8px; cursor: pointer;">
            <i class="fas fa-trash-alt"></i> Delete
         </li>
      </ul>
   </div>

   <script src="assets/js/message.js"></script>
   <script>
      let selectedUserId = null;
      let selectedMessage = null;

      document.getElementById('chat-messages').addEventListener('contextmenu', function(event) {
         event.preventDefault();
         const target = event.target.closest('.message');
         if (target) {
            selectedMessage = target;
            showPopupMenu(event.pageX, event.pageY);
         }
      });

      function showPopupMenu(x, y) {
         const popupMenu = document.getElementById('popup-menu');
         popupMenu.style.left = `${x}px`;
         popupMenu.style.top = `${y}px`;
         popupMenu.style.display = 'block';
         document.addEventListener('click', hidePopupMenu);
      }

      function hidePopupMenu() {
         document.getElementById('popup-menu').style.display = 'none';
         document.removeEventListener('click', hidePopupMenu);
      }

      function deleteMessage() {
         if (selectedMessage) {
            const confirmDelete = window.confirm("Are you sure you want to delete this message?");
            if (confirmDelete) {
               const messageId = selectedMessage.getAttribute('data-message-id');
               const isSender = selectedMessage.classList.contains('sent');

               fetch('delete_message.php', {
                     method: 'POST',
                     headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                     },
                     body: new URLSearchParams({
                        message_id: messageId,
                        is_sender: isSender ? 1 : 0
                     })
                  })
                  .then(response => response.json())
                  .then(data => {
                     if (data.status === "success") {
                        pollMessages();
                        hidePopupMenu();
                     } else {
                        alert('Error deleting message');
                     }
                  });
            }
         }
      }

      function selectChat(chatItem, userId) {
         selectedUserId = userId;
         const username = chatItem.querySelector('.chat-info h4').textContent;
         const userStatus = chatItem.querySelector('.chat-status').textContent;

         document.getElementById('chat-username').textContent = username;
         document.getElementById('chat-status').textContent = userStatus;

         startChat(userId);
      }

      function startChat(receiverId) {
         fetch(`get_messages.php?receiver=${receiverId}`)
            .then(response => response.json())
            .then(data => {
               const chatMessages = document.getElementById("chat-messages");
               chatMessages.innerHTML = '';
               data.messages.forEach(message => {
                  const div = document.createElement('div');
                  div.classList.add('message', message.sender === receiverId ? 'received' : 'sent');
                  div.setAttribute('data-message-id', message.id);

                  div.innerHTML = `
                     <p>${message.message}</p>
                     <span class="time">${message.date}</span>
                  `;
                  chatMessages.appendChild(div);
               });
               chatMessages.scrollTop = chatMessages.scrollHeight;
            });
      }

      function sendMessage() {
         const message = document.getElementById('message-input').value;
         if (!selectedUserId) {
            alert("Please select a chat.");
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
                  pollMessages();
                  document.getElementById('message-input').value = '';
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
                     div.setAttribute('data-message-id', message.id);

                     div.innerHTML = `
                        <p>${message.message}</p>
                        <span class="time">${message.date}</span>
                     `;
                     chatMessages.appendChild(div);
                  });
                  chatMessages.scrollTop = chatMessages.scrollHeight;
               });
         }
      }

      setInterval(pollMessages, 3000);
      setInterval(refreshUserStatuses, 3000);

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

      function updateChatTimestamps() {
         fetch('get_last_message_dates.php')
            .then(response => response.json())
            .then(dates => {
               dates.forEach(item => {
                  const timeElement = document.getElementById(`time-${item.id}`);
                  if (timeElement) {
                     timeElement.textContent = item.date;
                  }
               });
            })
            .catch(error => console.error("Error updating timestamps:", error));
      }

      // Call the update function every 3 seconds
      setInterval(updateChatTimestamps, 3000);

      function updateReceivedState() {
         fetch('update_received_state.php')
            .catch(error => console.error("Error updating timestamps:", error));
      }

      // Call the update function every 3 seconds
      setInterval(updateReceivedState, 3000);


      function refreshUnseenCounts() {
         fetch('get_unseen_counts.php')
            .then(response => response.json())
            .then(users => {
               console.log("Fetched users:", users); // Debug: check fetched data

               const chatList = document.getElementById('chat-list');
               chatList.innerHTML = ''; // Clear current list

               users.forEach(user => {
                  // Create the HTML for each user item
                  const unseenClass = user.unseen_count > 0 ? 'unseen' : '';
                  const lastMessageDate = user.last_message_date ?
                     new Date(user.last_message_date).toLocaleDateString('en-GB') :
                     '';

                  const chatItemHTML = `
                  <li>
                     <div class="chat-item ${unseenClass}" 
                        data-receiver-id="${user.id}" 
                        onclick="selectChat(this, ${user.id})">
                        <div class="avatar"></div>
                        <div class="chat-info">
                           <h4>${user.username}</h4>
                           <p class="chat-status">${user.state ? 'Online' : 'Offline'}</p>
                        </div>
                        <div class="chat-side">
                           <span class="time" id="time-${user.id}">${lastMessageDate}</span>
                           <span class="circle"></span>
                        </div>
                     </div>
                  </li>
               `;

                  // Append each user item to the chat list
                  chatList.insertAdjacentHTML('beforeend', chatItemHTML);
               });
            })
            .catch(error => console.error("Error fetching unseen counts:", error));
      }

      // Set interval to poll unseen message counts every 3 seconds
      setInterval(refreshUnseenCounts, 3000);



      // Mark messages as seen when chat is opened
      function markMessagesAsSeen(receiverId) {
         fetch('mark_messages_seen.php', {
            method: 'POST',
            body: new URLSearchParams({
               receiver: receiverId
            })
         }).then(() => {
            const chatItem = document.querySelector(`.chat-item[data-receiver-id="${receiverId}"]`);
            if (chatItem) chatItem.classList.remove('unseen'); // Remove unseen indicator
         });
      }
   </script>
</body>

</html>