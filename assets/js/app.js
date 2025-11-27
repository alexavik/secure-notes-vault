/**
 * Secure Notes Vault - Main Application JavaScript
 * Developed by Avik - https://aviksec.xo.je
 */

// Theme Management
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Update theme icon
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
    }
    
    // Update server-side theme preference
    fetch('update_theme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ theme: newTheme })
    }).catch(error => console.error('Theme update failed:', error));
}

// Initialize theme
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            themeIcon.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
        }
    }
});

// Note Form Handling (Add/Edit)
const noteForm = document.getElementById('noteForm');
if (noteForm) {
    noteForm.addEventListener('submit', function(e) {
        const contentField = document.getElementById('content');
        const encryptedField = document.getElementById('encryptedContent');
        
        if (contentField && encryptedField) {
            const plaintext = contentField.value.trim();
            
            if (!plaintext) {
                alert('Note content cannot be empty.');
                e.preventDefault();
                return false;
            }
            
            try {
                // Encrypt content before submission
                const encrypted = encryptData(plaintext);
                encryptedField.value = encrypted;
            } catch (error) {
                console.error('Encryption error:', error);
                alert('Failed to encrypt note. Please try again.');
                e.preventDefault();
                return false;
            }
        }
    });
}

// Dashboard: Decrypt note previews on click
document.addEventListener('DOMContentLoaded', function() {
    const noteCards = document.querySelectorAll('.note-card');
    
    noteCards.forEach(card => {
        const previewDiv = card.querySelector('.note-content-preview');
        const encryptedData = card.getAttribute('data-encrypted');
        
        if (previewDiv && encryptedData) {
            // Decrypt on click
            card.addEventListener('click', function(e) {
                // Don't decrypt if clicking action buttons
                if (e.target.closest('.note-actions')) {
                    return;
                }
                
                if (previewDiv.classList.contains('loading')) {
                    try {
                        const decrypted = decryptData(encryptedData);
                        const preview = decrypted.length > 150 
                            ? decrypted.substring(0, 150) + '...' 
                            : decrypted;
                        previewDiv.innerHTML = '<p>' + escapeHtml(preview) + '</p>';
                        previewDiv.classList.remove('loading');
                    } catch (error) {
                        console.error('Decryption error:', error);
                        previewDiv.innerHTML = '<p class="error">⚠️ Failed to decrypt note</p>';
                    }
                }
            });
        }
    });
});

// Delete Note Function
function deleteNote(noteId) {
    if (!confirm('Are you sure you want to delete this note? This action cannot be undone.')) {
        return;
    }
    
    fetch('delete_note.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            note_id: noteId,
            csrf_token: CSRF_TOKEN
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('Failed to delete note. Please try again.');
    });
}

// HTML Escape Utility
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-logout on inactivity
let inactivityTimer;
const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        alert('You have been logged out due to inactivity.');
        window.location.href = 'logout.php';
    }, INACTIVITY_TIMEOUT);
}

// Track user activity
if (window.location.pathname.includes('dashboard') || 
    window.location.pathname.includes('note')) {
    ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetInactivityTimer, true);
    });
    resetInactivityTimer();
}

// Password strength indicator (optional enhancement)
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}