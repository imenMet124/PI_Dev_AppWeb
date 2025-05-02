/**
 * Notification Handler - Manages notifications across the application
 * 
 * This script provides a standardized way to display notifications
 * using either Bootstrap toasts or alerts.
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-dismissible');
        alerts.forEach(alert => {
            if (bootstrap && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.display = 'none';
            }
        });
    }, 5000);
});

/**
 * Show a toast notification
 * 
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, danger, warning, info)
 */
function showToast(message, type = 'info') {
    const iconMap = {
        'success': 'fa-check-circle',
        'danger': 'fa-times-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle',
        'error': 'fa-times-circle' // Alias for danger
    };

    // If type is error, convert to danger for Bootstrap compatibility
    if (type === 'error') {
        type = 'danger';
    }

    // Check if we have a toast container, if not create one
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div class="toast bg-${type} text-white" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="toast-header bg-${type} text-white">
                <i class="fas ${iconMap[type]} me-2"></i>
                <strong class="me-auto">${type === 'success' ? 'Succès' : type === 'danger' ? 'Erreur' : type === 'warning' ? 'Attention' : 'Information'}</strong>
                <small>maintenant</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = document.getElementById(toastId);
    
    if (bootstrap && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();
    }

    // Auto remove toast from DOM after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Show a notification alert in the alert container
 * 
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, danger, warning, info)
 */
function showAlert(message, type = 'info') {
    // If type is error, convert to danger for Bootstrap compatibility
    if (type === 'error') {
        type = 'danger';
    }

    // Check if we have an alert container, if not create one
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.className = 'container-fluid mt-3';
        
        // Try to insert it at the beginning of the content area
        const contentArea = document.querySelector('.content');
        if (contentArea) {
            contentArea.insertBefore(alertContainer, contentArea.firstChild);
        } else {
            document.body.appendChild(alertContainer);
        }
    }

    const alertId = 'alert-' + Date.now();
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            if (bootstrap && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            } else {
                alertElement.style.display = 'none';
            }
        }
    }, 5000);
}

// For backward compatibility
function showNotification(message, type = 'info') {
    showToast(message, type);
}
