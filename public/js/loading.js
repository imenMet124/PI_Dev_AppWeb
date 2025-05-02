class LoadingOverlay {
    constructor(buttonSelector) {
        this.overlay = null;
        this.buttonSelector = buttonSelector;
    }

    // Create and display the loading overlay
    show() {
        if (!this.overlay) {
            // Create the overlay element if it doesn't exist
            this.overlay = $('<div>', { class: 'loading-overlay' });

            // Create the spinner
            const spinner = $('<div>', { class: 'spinner' });

            // Create the message
            const message = $('<p>').text('Veuillez patienter...');

            // Append spinner and message to the overlay
            this.overlay.append(spinner).append(message);

            // Append overlay to the body
            $('body').append(this.overlay);
        }

        // Ensure overlay is visible
        this.overlay.removeClass('hidden');
    }

    // Hide the loading overlay
    hide() {
        if (this.overlay) {
            this.overlay.addClass('hidden');
        }
    }
}