let activeReceiverId = null; // Store the active receiver ID

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


// Set interval to poll unseen message counts every 5 seconds
setInterval(refreshUnseenCounts, 1000);
