document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth >= 1200 && Notification.permission !== 'granted') {
        Notification.requestPermission();
    }
    
    const discussionId = document.getElementById('discussion_id').value;
    const chatMessages = document.getElementById('chat-messages');
    let lastMessageId = 0;
    let isFirstLoad = true;
   
    function fetchMessages() {
        fetch(`fetch_messages.php?discussion_id=${discussionId}&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'update') {
                    const hasNewMessages = !isFirstLoad && data.last_id > lastMessageId;
                   
                    chatMessages.innerHTML = data.html;
                    lastMessageId = data.last_id;
                   
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                   
                    if (hasNewMessages && window.innerWidth >= 1200 && Notification.permission === 'granted') {
                        if (document.hidden || !document.hasFocus()) {
                            showNotification();
                        }
                    }
                   
                    isFirstLoad = false;
                } else if (data.status === 'error') {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }
   
    function showNotification() {
        const notification = new Notification('Nouveau message', {
            body: 'Vous avez reçu un nouveau message !',
            icon: '/Resources/img/logo/logo min rounded.png',
            tag: 'chat-message',
            requireInteraction: false
        });
       
        setTimeout(() => {
            notification.close();
        }, 5000);
       
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    }
   
    fetchMessages();
   
    setInterval(fetchMessages, 1000);
   
    const chatForm = document.getElementById('chat-form');
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
       
        const formData = new FormData(this);
       
        fetch('Processus/chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(() => {
            document.getElementById('message').value = '';
           
            lastMessageId = 0;
            fetchMessages();
        })
        .catch(error => console.error('Error sending message:', error));
    });
});