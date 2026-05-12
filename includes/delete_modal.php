<div id="universalDeleteModal" class="custom-modal hidden">
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal-content">
        <div class="modal-icon-wrapper text-danger">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <h3 id="deleteModalTitle">Confirm Deletion</h3>
        <p id="deleteModalMessage" class="text-muted">Are you sure you want to delete this item? This action cannot be undone.</p>
        
        <div class="modal-actions">
            <button class="btn btn-outline" id="cancelDeleteBtn">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">
                <span class="btn-text">Yes, Delete</span>
                <span class="loader hidden" id="deleteLoader"></span>
            </button>
        </div>
    </div>
</div>