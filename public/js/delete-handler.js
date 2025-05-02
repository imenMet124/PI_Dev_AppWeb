/**
 * Delete Handler - Manages delete operations across the application
 *
 * This script provides a standardized way to handle delete operations
 * with proper confirmation dialogs and feedback.
 */

// Initialize delete buttons when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeDeleteButtons();
});

/**
 * Initialize all delete buttons in the document
 */
function initializeDeleteButtons() {
    // Find all delete buttons with data attributes
    const deleteButtons = document.querySelectorAll('[data-delete-url]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const url = this.getAttribute('data-delete-url');
            const token = this.getAttribute('data-delete-token');
            const entityName = this.getAttribute('data-entity-name') || 'Élément';

            confirmDelete(url, token, entityName);
        });
    });
}

/**
 * Show a confirmation dialog and handle the delete operation
 *
 * @param {string} url - The URL to send the delete request to
 * @param {string} token - The CSRF token
 * @param {string} entityName - The name of the entity being deleted
 */
function confirmDelete(url, token, entityName) {
    // Use SweetAlert if available, otherwise fallback to browser confirm
    if (typeof swal !== 'undefined') {
        swal({
            title: `Supprimer ${entityName}`,
            text: `Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.`,
            icon: 'warning',
            buttons: {
                cancel: {
                    text: "Annuler",
                    value: null,
                    visible: true,
                    className: "btn btn-secondary",
                },
                confirm: {
                    text: "Oui, supprimer",
                    value: true,
                    visible: true,
                    className: "btn btn-danger",
                }
            },
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                submitDeleteForm(url, token);
            }
        });
    } else {
        // Fallback to browser confirm
        if (confirm(`Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.`)) {
            submitDeleteForm(url, token);
        }
    }
}

/**
 * Create and submit a form for the delete operation
 *
 * @param {string} url - The URL to send the delete request to
 * @param {string} token - The CSRF token
 */
function submitDeleteForm(url, token) {
    // Make sure the URL includes /delete
    if (!url.includes('/delete')) {
        url = url.replace(/\/([^\/]+)$/, '/$1/delete');
        console.log('URL modified to:', url);
    }

    // Log for debugging
    console.log('Submitting delete form to URL:', url);
    console.log('With token:', token);

    // Create a form element
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.style.display = 'none';
    form.setAttribute('enctype', 'application/x-www-form-urlencoded');

    // Add CSRF token
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = token;
    form.appendChild(tokenInput);

    // Log the form details
    console.log('Form created:', {
        method: form.method,
        action: form.action,
        enctype: form.getAttribute('enctype'),
        token: token
    });

    // Add the form to the document
    document.body.appendChild(form);

    // Show loading notification if the function exists
    if (typeof showToast === 'function') {
        showToast('Suppression en cours...', 'info');
    }

    // Submit the form with a slight delay to ensure the toast is shown
    setTimeout(() => {
        console.log('Form submitted to:', url);
        try {
            form.submit();
            console.log('Form submission successful');
        } catch (error) {
            console.error('Error submitting form:', error);
            if (typeof showToast === 'function') {
                showToast('Erreur lors de la suppression: ' + error.message, 'danger');
            }
        }
    }, 100);
}
