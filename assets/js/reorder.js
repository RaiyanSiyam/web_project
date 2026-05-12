// assets/js/reorder.js
document.addEventListener('DOMContentLoaded', () => {
    const btnRunEngine = document.getElementById('btnRunEngine');
    
    if (btnRunEngine) {
        btnRunEngine.addEventListener('click', () => {
            // UI Loading state
            btnRunEngine.disabled = true;
            document.querySelector('.btn-text').classList.add('hidden');
            document.getElementById('engineLoader').classList.remove('hidden');

            fetch('../../app/controllers/ReorderController.php?action=run_engine', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccessModal("Engine Execution Complete", data.message);
                } else {
                    alert("Engine failed: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Network error occurred.");
            })
            .finally(() => {
                btnRunEngine.disabled = false;
                document.querySelector('.btn-text').classList.remove('hidden');
                document.getElementById('engineLoader').classList.add('hidden');
            });
        });
    }
});

function updateOrderStatus(id, status) {
    let confirmMsg = status === 'ordered' ? 
        "Mark this PO as ordered from supplier?" : 
        "Mark as received? This will physically add the quantities to your active inventory stock.";

    if (confirm(confirmMsg)) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch('../../app/controllers/ReorderController.php?action=update_status', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showSuccessModal("Status Updated", "The inventory pipeline has been updated successfully.");
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => console.error(err));
    }
}

function showSuccessModal(title, message) {
    const modal = document.getElementById('universalSuccessModal');
    if (modal) {
        document.getElementById('successModalTitle').innerText = title;
        document.getElementById('successModalMessage').innerText = message;
        modal.classList.remove('hidden');

        document.getElementById('successModalCloseBtn').addEventListener('click', () => {
            location.reload(); 
        });
    } else {
        // Fallback if modal isn't included
        alert(title + "\n" + message);
        location.reload();
    }
}