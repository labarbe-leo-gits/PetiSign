document.addEventListener('DOMContentLoaded', function() {
    const discussionId = document.getElementById('discussion_id').value;
    const chatMessages = document.getElementById('chat-messages');
    let lastMessageId = 0;
    
    function fetchMessages() {
        fetch(`fetch_messages.php?discussion_id=${discussionId}&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'update') {
                    chatMessages.innerHTML = data.html;
                    lastMessageId = data.last_id;
                    
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } else if (data.status === 'error') {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
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