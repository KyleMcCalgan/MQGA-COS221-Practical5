function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function openNameModal() {
    openModal('nameModal');
}

function openEmailModal() {
    openModal('emailModal');
}

function openPasswordModal() {
    openModal('passwordModal');
}

document.addEventListener('click', function (event) {
    const modals = document.getElementsByClassName('modal');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
});

// Prevent closing when clicking inside modal content
document.querySelectorAll('.modal-content').forEach(content => {
    content.addEventListener('click', function (event) {
        event.stopPropagation();
    });
});