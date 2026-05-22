let deleteEventId = null;

function showDeleteModal(eventId, eventTitle) {
    deleteEventId = eventId;
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    message.innerHTML = `Möchten Sie das Event "<strong>${eventTitle}</strong>" wirklich löschen?`;
    modal.classList.add('active');
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    deleteEventId = null;
}

function confirmDelete() {
    if (deleteEventId) {
        window.location.href = `dashboard.php?delete=${deleteEventId}`;
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        hideDeleteModal();
    }
}


setTimeout(function() {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.opacity = '0';
        setTimeout(function() {
            if (alert.parentNode) alert.remove();
        }, 500);
    });
}, 4000);
