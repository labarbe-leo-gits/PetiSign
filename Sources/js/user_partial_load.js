document.addEventListener("DOMContentLoaded", function() {
    const userItems = document.querySelectorAll('.user_item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            } else {
                entry.target.classList.remove('visible');
            }
        });
    }, { 
        threshold: 0.1,
        rootMargin: '50px 0px -50px 0px'
    });

    userItems.forEach(item => {
        observer.observe(item);
    });
});