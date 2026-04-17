/**
 * Premium Portfolio Core Logic
 * Author: Fekadu Markos
 * Standard: Professional 30-Year Developer Grade
 */

function sendToGmail() {
    const name = document.getElementById('clientName').value;
    const message = document.getElementById('clientMessage').value;
    const myEmail = "fekemark6@gmail.com";
    
    if (!name || !message) {
        alert("Please provide your name and a brief message!");
        return;
    }

    const subject = encodeURIComponent(`Professional Inquiry from ${name}`);
    const body = encodeURIComponent(`Name: ${name}\n\nProject Details:\n${message}`);
    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${myEmail}&su=${subject}&body=${body}`;
    
    window.open(gmailUrl, '_blank');
}

function openModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'flex';
    modal.style.opacity = '0';
    
    // Smooth transition
    setTimeout(() => {
        modal.style.transition = 'opacity 0.5s ease';
        modal.style.opacity = '1';
    }, 10);
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 500);
}

// Close modal on background click
window.addEventListener('click', (event) => {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal(event.target.id);
    }
});

// Simple Entrance Animations
document.addEventListener('DOMContentLoaded', () => {
    const heroText = document.querySelector('.hero-text');
    const heroImage = document.querySelector('.hero-image-container');
    
    if (heroText) {
        heroText.style.opacity = '0';
        heroText.style.transform = 'translateX(-50px)';
        setTimeout(() => {
            heroText.style.transition = 'all 1s ease-out';
            heroText.style.opacity = '1';
            heroText.style.transform = 'translateX(0)';
        }, 100);
    }

    if (heroImage) {
        heroImage.style.opacity = '0';
        heroImage.style.transform = 'scale(0.9)';
        setTimeout(() => {
            heroImage.style.transition = 'all 1.2s ease-out';
            heroImage.style.opacity = '1';
            heroImage.style.transform = 'scale(1)';
        }, 300);
    }
});
