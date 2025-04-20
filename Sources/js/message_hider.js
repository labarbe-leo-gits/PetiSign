document.addEventListener("DOMContentLoaded", function() {

    const successMessages = document.querySelectorAll('.message > .success');
    
    successMessages.forEach(successMessage => {
        setTimeout(() => {
            successMessage.classList.add('hidden');
        }, 5000);
    });

    const observeNewMessages = () => {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1 && node.classList.contains('success')) {
                        setTimeout(() => {
                            node.classList.add('hidden');
                        }, 5000);
                    }
                });
            });
        });
        
        const messageContainers = document.querySelectorAll('.message');
        messageContainers.forEach(container => {
            observer.observe(container, { childList: true });
        });
    };
    
    observeNewMessages();
});