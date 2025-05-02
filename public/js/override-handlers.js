/**
 * Override Handlers - Overrides existing functions to use our standardized handlers
 * 
 * This script overrides existing functions in the global scope to use our standardized handlers.
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Override handlers initialized');
    
    // Override the global initDeleteButtons function
    if (typeof window.initDeleteButtons === 'function') {
        console.log('Overriding initDeleteButtons');
        window.originalInitDeleteButtons = window.initDeleteButtons;
        window.initDeleteButtons = function() {
            console.log('Using custom delete handler');
            if (typeof initializeDeleteButtons === 'function') {
                initializeDeleteButtons();
            }
        };
    }
    
    // Override the global confirmDelete function
    if (typeof window.confirmDelete === 'function') {
        console.log('Overriding confirmDelete');
        window.originalConfirmDelete = window.confirmDelete;
        window.confirmDelete = function(url, token, entityName) {
            console.log('Using custom confirm delete handler');
            // Make sure the URL includes /delete
            if (!url.includes('/delete')) {
                url = url.replace(/\/([^\/]+)$/, '/$1/delete');
                console.log('URL modified to:', url);
            }
            
            // Use SweetAlert if available
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
        };
    }
    
    // Override the global showToast function
    if (typeof window.showToast === 'function') {
        console.log('Overriding showToast');
        window.originalShowToast = window.showToast;
        window.showToast = function(message, type = 'info') {
            console.log('Using custom toast handler');
            // Use our custom notification handler
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            }
        };
    }
});
