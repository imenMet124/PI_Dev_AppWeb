// Handle 404 errors for common routes
function handle404Errors() {
    // Check if we're on a 404 page
    if (document.title.includes('404') || document.body.textContent.includes('404 Not Found')) {
        // Get the current URL
        const currentUrl = window.location.href;
        
        // Define redirects for common 404 errors
        const redirects = {
            '/back/quiz/': '/back/quiz',
            '/back/option/': '/back/option',
            '/back': '/back',
            '/back/question/': '/back/question'
        };
        
        // Check if the current URL matches any of the redirects
        for (const [path, redirect] of Object.entries(redirects)) {
            if (currentUrl.includes(path)) {
                // Redirect to the correct URL
                window.location.href = redirect;
                return;
            }
        }
    }
}

// Call the 404 handler when the page loads
window.addEventListener('load', handle404Errors);
