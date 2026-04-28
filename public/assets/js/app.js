/**
 * SIAKAD Gallery — Global Application JavaScript
 * Handles common functionality across all pages
 */

// ============================================================================
// DOM Ready & Initialization
// ============================================================================

document.addEventListener('DOMContentLoaded', function () {
  initFlashMessages();
  initSidebarToggle();
  initPasswordToggle();
  initConfirmDelete();
  initFormValidation();
});

// ============================================================================
// Flash Message Auto-Dismiss
// ============================================================================

function initFlashMessages() {
  const alerts = document.querySelectorAll('.alert, .auth-alert');
  alerts.forEach(function (alert) {
    // Auto dismiss after 4 seconds (unless it's an error)
    if (!alert.classList.contains('alert-error') && !alert.classList.contains('auth-alert-error')) {
      setTimeout(function () {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease';
        setTimeout(function () {
          alert.style.display = 'none';
        }, 300);
      }, 4000);
    }

    // Add close button functionality
    const closeBtn = alert.querySelector('.alert-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease';
        setTimeout(function () {
          alert.style.display = 'none';
        }, 300);
      });
    }
  });
}

// ============================================================================
// Sidebar Toggle for Mobile
// ============================================================================

function initSidebarToggle() {
  const toggleBtn = document.querySelector('[data-sidebar-toggle]');
  const sidebar = document.querySelector('.sidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });

    // Close sidebar on link click
    const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 1024) {
          sidebar.classList.remove('open');
        }
      });
    });
  }

  // Mark active page in sidebar
  const currentPath = window.location.pathname;
  const sidebarItems = document.querySelectorAll('.sidebar-link');
  sidebarItems.forEach(function (item) {
    const href = item.getAttribute('href');
    if (href && currentPath.includes(href)) {
      item.classList.add('active');
    }
  });
}

// ============================================================================
// Password Show/Hide Toggle
// ============================================================================

function initPasswordToggle() {
  const toggleBtns = document.querySelectorAll('.password-toggle');
  toggleBtns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const input = btn.parentElement.querySelector('input[type="password"], input[type="text"]');
      if (input) {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      }
    });
  });
}

// ============================================================================
// Confirm Delete Dialog
// ============================================================================

function initConfirmDelete() {
  const deleteLinks = document.querySelectorAll('[data-confirm-delete]');
  deleteLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const message = link.getAttribute('data-confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';
      if (confirm(message)) {
        window.location.href = link.href;
      }
    });
  });
}

// ============================================================================
// Form Validation
// ============================================================================

function initFormValidation() {
  const forms = document.querySelectorAll('[data-validate]');
  forms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      const isValid = validateForm(form);
      if (!isValid) {
        e.preventDefault();
      }
    });
  });
}

function validateForm(form) {
  let isValid = true;
  const requiredFields = form.querySelectorAll('[required]');

  requiredFields.forEach(function (field) {
    if (!field.value.trim()) {
      showFieldError(field, 'Field ini harus diisi');
      isValid = false;
    } else {
      clearFieldError(field);
    }
  });

  // Validate email fields
  const emailFields = form.querySelectorAll('input[type="email"]');
  emailFields.forEach(function (field) {
    if (field.value && !validateEmail(field.value)) {
      showFieldError(field, 'Format email tidak valid');
      isValid = false;
    }
  });

  return isValid;
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function showFieldError(field, message) {
  field.classList.add('error');
  let errorDiv = field.parentElement.querySelector('.form-error');
  if (!errorDiv) {
    errorDiv = document.createElement('div');
    errorDiv.className = 'form-error';
    field.parentElement.appendChild(errorDiv);
  }
  errorDiv.textContent = message;
}

function clearFieldError(field) {
  field.classList.remove('error');
  const errorDiv = field.parentElement.querySelector('.form-error');
  if (errorDiv) {
    errorDiv.remove();
  }
}

// ============================================================================
// CSRF Token Helper
// ============================================================================

function getCSRFToken() {
  const token = document.querySelector('input[name="_csrf"]');
  return token ? token.value : '';
}

// ============================================================================
// Generic AJAX Helper
// ============================================================================

function ajaxRequest(url, options = {}) {
  const defaultOptions = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: {},
  };

  // Add CSRF token
  const csrfToken = getCSRFToken();
  if (csrfToken) {
    defaultOptions.headers['X-CSRF-Token'] = csrfToken;
  }

  const finalOptions = Object.assign({}, defaultOptions, options);

  // Convert body to JSON if it's an object
  if (options.body && typeof options.body === 'object') {
    finalOptions.body = JSON.stringify(options.body);
  }

  return fetch(url, finalOptions)
    .then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .catch(function (error) {
      console.error('AJAX Error:', error);
      throw error;
    });
}

// ============================================================================
// URL Helper
// ============================================================================

function getAppUrl() {
  if (typeof window.APP_URL !== 'undefined') {
    return window.APP_URL;
  }
  const baseUrl = document.querySelector('meta[name="app-url"]');
  return baseUrl ? baseUrl.getAttribute('content') : '';
}

// ============================================================================
// Utility Functions
// ============================================================================

/**
 * Format date to Indonesian locale
 */
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('id-ID', options);
}

/**
 * Format time
 */
function formatTime(timeString) {
  const [hour, minute] = timeString.split(':');
  return hour + ':' + minute;
}

/**
 * Show a temporary notification
 */
function showNotification(message, type = 'info', duration = 3000) {
  const alert = document.createElement('div');
  alert.className = 'alert alert-' + type;
  alert.innerHTML = message;
  document.body.appendChild(alert);

  setTimeout(function () {
    alert.style.opacity = '0';
    alert.style.transition = 'opacity 0.3s ease';
    setTimeout(function () {
      alert.remove();
    }, 300);
  }, duration);
}

/**
 * Debounce function for input handlers
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = function () {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle function for event handlers
 */
function throttle(func, limit) {
  let inThrottle;
  return function (...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(function () {
        inThrottle = false;
      }, limit);
    }
  };
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(function () {
      showNotification('Teks disalin ke clipboard', 'success', 2000);
    });
  } else {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showNotification('Teks disalin ke clipboard', 'success', 2000);
  }
}

/**
 * Toggle element visibility
 */
function toggleElement(selector) {
  const element = document.querySelector(selector);
  if (element) {
    element.style.display = element.style.display === 'none' ? '' : 'none';
  }
}

/**
 * Add class to element
 */
function addClass(selector, className) {
  const element = document.querySelector(selector);
  if (element) {
    element.classList.add(className);
  }
}

/**
 * Remove class from element
 */
function removeClass(selector, className) {
  const element = document.querySelector(selector);
  if (element) {
    element.classList.remove(className);
  }
}

/**
 * Toggle class on element
 */
function toggleClass(selector, className) {
  const element = document.querySelector(selector);
  if (element) {
    element.classList.toggle(className);
  }
}

// ============================================================================
// Export functions for use in other scripts
// ============================================================================

window.SIAKAD = {
  ajaxRequest,
  getAppUrl,
  getCSRFToken,
  formatDate,
  formatTime,
  showNotification,
  debounce,
  throttle,
  copyToClipboard,
  toggleElement,
  addClass,
  removeClass,
  toggleClass,
  validateEmail,
  validateForm,
};
