let activeReceiverId = null; // Store the active receiver ID

function selectChat(element, receiverId) {
   activeReceiverId = receiverId;
   document.querySelectorAll('#chat-list .chat-item').forEach(item => item.classList.remove('active'));
   element.classList.add('active');
   startChat(receiverId);
   markMessagesAsSeen(receiverId); // Mark messages as seen when opening the chat
}

// Load messages for the selected user
function startChat(receiverId) {
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

function refreshUnseenCounts() {
   fetch('get_unseen_counts.php')
      .then(response => response.json())
      .then(users => {
         users.forEach(user => {
            const chatItem = document.querySelector(`.chat-item[data-receiver-id="${user.id}"]`);
            if (chatItem) {
               if (user.unseen_count > 0) {
                  chatItem.classList.add('unseen'); // Add unseen class if there are unseen messages
               } else {
                  chatItem.classList.remove('unseen'); // Remove unseen class if no unseen messages
               }
            }
         });
      })
      .catch(error => console.error("Error fetching unseen counts:", error));
}

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

// Send message to the server
function sendMessage() {
   const message = document.getElementById('message-input').value;

   if (!activeReceiverId) {
      alert("Receiver ID is not set. Please select a chat.");
      return;
   }

   fetch('send_message.php', {
      method: 'POST',
      body: new URLSearchParams({
         receiver: activeReceiverId,
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

// Poll messages every 5 seconds to check for new messages
function pollMessages() {
   if (activeReceiverId) {
      fetch(`get_messages.php?receiver=${activeReceiverId}`)
         .then(response => response.json())
         .then(data => {
            const chatMessages = document.getElementById("chat-messages");
            chatMessages.innerHTML = '';
            data.messages.forEach(message => {
               const div = document.createElement('div');
               div.classList.add('message', message.sender === activeReceiverId ? 'received' : 'sent');
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
setInterval(refreshUnseenCounts, 5000);
setInterval(pollMessages, 5000);
