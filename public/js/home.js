document.addEventListener('DOMContentLoaded', function() {
    // Reels slider functionality
    const reelsSlider = document.querySelector('.reels-slider');
    const prevButton = document.querySelector('.reels-prev');
    const nextButton = document.querySelector('.reels-next');
    
    if (reelsSlider && prevButton && nextButton) {
        prevButton.addEventListener('click', function() {
            reelsSlider.scrollBy({
                left: -200,
                behavior: 'smooth'
            });
        });
        
        nextButton.addEventListener('click', function() {
            reelsSlider.scrollBy({
                left: 200,
                behavior: 'smooth'
            });
        });
    }
    
    // Create directory structure for reels and livestream images if they don't exist
    function createDirectories() {
        const directories = [
            '/img/reels',
            '/img/streams',
            '/img/avatars'
        ];
        
        // This is just a placeholder function since we can't directly create directories from JavaScript
        // In a real application, you would handle this server-side
        console.log('Directories that should exist:', directories);
    }
    
    // Initialize the page
    function init() {
        createDirectories();
    }
    
    init();
}); 