/**
 * Universal AJAX Form Handler
 * Handles form submissions with validation, toasts, redirects, and modal support
 * 
 * Usage:
 * 1. Add 'ajax-form' class to any form
 * 2. Optional attributes:
 *    - data-redirect="url" - Redirect after success
 *    - data-modal="#modalId" - Close modal after success
 *    - data-callback="functionName" - Call function after success
 *    - data-reset="true" - Reset form after success
 */

class AjaxFormHandler {
    constructor() {
        this.forms = [];
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.attachFormListeners();
        });

        // Support for dynamically added forms
        document.addEventListener('formAdded', () => {
            this.attachFormListeners();
        });
    }

    attachFormListeners() {
        const forms = document.querySelectorAll('form.ajax-form');
        
        forms.forEach(form => {
            // Prevent duplicate listeners
            if (form.dataset.ajaxInitialized === 'true') return;
            
            form.dataset.ajaxInitialized = 'true';
            form.addEventListener('submit', (e) => this.handleSubmit(e, form));
        });
    }

    async handleSubmit(e, form) {
        e.preventDefault();

        // Clear previous errors
        this.clearErrors(form);

        // Get form data
        const formData = new FormData(form);
        const method = form.method.toUpperCase();
        const action = form.action;

        // Add loading state
        const submitBtn = form.querySelector('[type="submit"]');
        const originalBtnText = submitBtn?.innerHTML;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        }

        try {
            const response = await fetch(action, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });

            const contentType = response.headers.get('content-type');
            let data;

            // Check if response is JSON
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                // Handle HTML response (redirect or error page)
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                throw new Error('Unexpected response format');
            }

            if (response.ok) {
                // Success handler
                this.handleSuccess(form, data);
            } else {
                // Error handler (validation errors)
                this.handleErrors(form, data);
            }

        } catch (error) {
            console.error('Form submission error:', error);
            this.showToast('error', 'An unexpected error occurred. Please try again.');
        } finally {
            // Restore button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    }

    handleSuccess(form, data) {
        // Show success message
        if (data.message || data.success) {
            this.showToast('success', data.message || data.success);
        }

        // Close modal if specified
        const modalId = form.dataset.modal;
        if (modalId) {
            const modal = document.querySelector(modalId);
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
        }

        // Reset form if specified
        if (form.dataset.reset === 'true') {
            form.reset();
        }

        // Call custom callback if specified
        const callbackName = form.dataset.callback;
        if (callbackName && typeof window[callbackName] === 'function') {
            window[callbackName](data);
        }

        // Redirect if specified
        const redirectUrl = form.dataset.redirect || data.redirect;
        if (redirectUrl) {
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 1500); // Delay to show success message
        }
    }

    handleErrors(form, data) {
        // Handle validation errors
        if (data.errors) {
            Object.keys(data.errors).forEach(fieldName => {
                const errors = data.errors[fieldName];
                const errorMessage = Array.isArray(errors) ? errors[0] : errors;
                
                // Find the input field (handle array notation like existing_members[0][name])
                let input = form.querySelector(`[name="${fieldName}"]`);
                
                // Try with brackets escaped for array fields
                if (!input) {
                    const escapedName = fieldName.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                    input = form.querySelector(`[name="${escapedName}"]`);
                }

                // Try to find by partial match for array fields
                if (!input) {
                    input = form.querySelector(`[name*="${fieldName.split('[')[0]}"]`);
                }

                if (input) {
                    // Add error class
                    input.classList.add('is-invalid');
                    
                    // Create or update error message
                    let errorDiv = input.parentElement.querySelector('.invalid-feedback');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        input.parentElement.appendChild(errorDiv);
                    }
                    errorDiv.textContent = errorMessage;
                    errorDiv.style.display = 'block';
                } else {
                    // If field not found, show in toast
                    this.showToast('error', `${fieldName}: ${errorMessage}`);
                }
            });

            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }

        // Show general error message
        if (data.message) {
            this.showToast('error', data.message);
        }
    }

    clearErrors(form) {
        // Remove all error states
        form.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });

        // Remove all error messages
        form.querySelectorAll('.invalid-feedback').forEach(errorDiv => {
            errorDiv.remove();
        });
    }

    showToast(type, message) {
        // Map types to Bootstrap classes
        const typeMap = {
            'success': 'text-bg-success',
            'error': 'text-bg-danger',
            'warning': 'text-bg-warning',
            'info': 'text-bg-info'
        };

        const iconMap = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };

        // Create toast container if it doesn't exist
        let container = document.getElementById('ajax-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'ajax-toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
        }

        // Create toast element
        const toastId = `toast-${Date.now()}`;
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center ${typeMap[type]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${iconMap[type]} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);

        // Initialize and show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 8000 : 5000
        });
        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Public method to programmatically submit a form
    submitForm(formSelector) {
        const form = document.querySelector(formSelector);
        if (form && form.classList.contains('ajax-form')) {
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    }

    // Public method to clear form errors
    clearFormErrors(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            this.clearErrors(form);
        }
    }
}

// Initialize the handler
const ajaxFormHandler = new AjaxFormHandler();

// Make it globally accessible
window.ajaxFormHandler = ajaxFormHandler;