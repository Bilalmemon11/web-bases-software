function deleteModal(modalId, deleteUrl, title = "Are you sure to delete this item?", description = "This action cannot be undone!", cancelBtn = "Cancel", confirmBtn = "Delete") {
    // Set the modal title and description
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDescription').innerText = description;
    document.getElementById('cancelBtn').innerText = cancelBtn;
    document.getElementById('confirmBtn').innerText = confirmBtn;

    // Update the form's action URL
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = deleteUrl;

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}


// Check URL for openpopup parameter and open modal if present
const urlParams = new URLSearchParams(window.location.search);
const popupModal = urlParams.get('openpopup');
if (popupModal) {
    const modalElement = document.getElementById(popupModal);
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}