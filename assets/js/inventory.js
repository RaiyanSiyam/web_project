// assets/js/inventory.js
document.addEventListener('DOMContentLoaded', () => {
    
    // File Upload Preview Logic
    const fileUploadBox = document.getElementById('fileUploadBox');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const uploadContent = document.querySelector('.upload-content');

    if (fileUploadBox && imageInput) {
        fileUploadBox.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    uploadContent.classList.add('hidden');
                    fileUploadBox.style.padding = '12px';
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and Drop
        fileUploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadBox.style.borderColor = 'var(--primary)';
        });
        fileUploadBox.addEventListener('dragleave', () => {
            fileUploadBox.style.borderColor = 'var(--border)';
        });
        fileUploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadBox.style.borderColor = 'var(--border)';
            if (e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                imageInput.dispatchEvent(new Event('change'));
            }
        });
    }
});


let itemToDeleteId = null;

function deleteProduct(productId) {
    const modal = document.getElementById('universalDeleteModal');
    const title = document.getElementById('deleteModalTitle');
    const msg = document.getElementById('deleteModalMessage');
    
    // Set custom text for inventory
    title.innerText = "Delete Product";
    msg.innerText = "Are you sure you want to remove this product? It will be marked as discontinued and removed from the active inventory.";
    
    // Show Modal
    modal.classList.remove('hidden');
    itemToDeleteId = productId;
}

// Modal Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const overlay = document.getElementById('modalOverlay');
    const modal = document.getElementById('universalDeleteModal');

    const closeModal = () => {
        modal.classList.add('hidden');
        itemToDeleteId = null;
    };

    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (!itemToDeleteId) return;

            // UI Loading state
            const btnText = confirmBtn.querySelector('.btn-text');
            const loader = confirmBtn.querySelector('.loader');
            btnText.classList.add('hidden');
            loader.classList.remove('hidden');
            confirmBtn.disabled = true;

            // Process AJAX Delete
            const formData = new FormData();
            formData.append('id', itemToDeleteId);

            fetch('../../app/controllers/InventoryController.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById(`row-${itemToDeleteId}`);
                    if(row) {
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        setTimeout(() => row.remove(), 400);
                    }
                    closeModal();
                } else {
                    alert(data.message); // Fallback for actual server errors
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Reset Button state
                btnText.classList.remove('hidden');
                loader.classList.add('hidden');
                confirmBtn.disabled = false;
            });
        });
    }
    
    // ... [Keep your existing File Upload JS code below this] ...
});