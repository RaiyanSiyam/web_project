<?php
// pages/users/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/controllers/UserController.php';

$controller = new UserController();
$employees = $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .split-layout { display: grid; grid-template-columns: 350px 1fr; gap: 24px; align-items: start; }
        
        /* Modern Card Styling */
        .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border: 1px solid var(--border); }
        .card-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        
        /* Form Improvements */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 6px; font-size: 0.85rem; color: #4b5563; }
        .form-control, select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; transition: border-color 0.2s; box-sizing: border-box; }
        .form-control:focus, select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .input-group { display: flex; gap: 12px; }
        .input-group .form-group { flex: 1; }
        
        /* Table Enhancements */
        .table-responsive { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8fafc; padding: 14px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .data-table td { padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background-color: #f8fafc; }
        
        /* Badges & Actions */
        .badge-role { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .role-admin { background: #fee2e2; color: #991b1b; }
        .role-manager { background: #fef3c7; color: #92400e; }
        .role-cashier { background: #e0f2fe; color: #075985; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-icon { padding: 6px; border-radius: 6px; border: 1px solid transparent; background: transparent; cursor: pointer; color: #64748b; transition: all 0.2s; }
        .btn-icon:hover { background: #f1f5f9; color: var(--primary); }
        .btn-icon.delete:hover { background: #fef2f2; color: #dc2626; }
        .btn-icon svg { width: 16px; height: 16px; }
        
        /* --- MODAL BASE CSS --- */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 1000; visibility: hidden; opacity: 0; transition: all 0.2s; }
        .modal-overlay.active { visibility: visible; opacity: 1; }
        .modal-content { background: #fff; width: 100%; max-width: 500px; border-radius: 12px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); transform: translateY(20px); transition: all 0.3s; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; font-size: 1.2rem; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b; }

        /* --- TOAST NOTIFICATIONS --- */
        .toast-container { position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; }
        .toast { min-width: 280px; padding: 16px 20px; border-radius: 6px; color: #fff; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: space-between; animation: slideIn 0.3s ease forwards; transition: opacity 0.3s ease; }
        .toast.success { background-color: #10b981; border-left: 5px solid #059669; }
        .toast.error { background-color: #ef4444; border-left: 5px solid #dc2626; }
        @keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* --- CUSTOM DELETE MODAL --- */
        .delete-modal-box { text-align: center; }
        .modal-icon { font-size: 40px; margin-bottom: 12px; }
        .delete-modal-box h3 { margin: 0 0 8px 0; color: #1e293b; font-size: 20px; }
        .delete-modal-box p { color: #64748b; font-size: 14px; margin: 0 0 24px 0; line-height: 1.5; }
        .modal-actions { display: flex; gap: 12px; justify-content: center; }
        .btn-modal-secondary { background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; }
        .btn-modal-secondary:hover { background: #e2e8f0; }
        .btn-modal-danger { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; }
        .btn-modal-danger:hover { background: #dc2626; }
    </style>
</head>
<body class="app-layout">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>Employee Directory</h1>
                    <p class="text-muted">Manage staff access, contact info, and payroll details.</p>
                </div>
            </div>

            <div class="split-layout">
                <div class="card">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                        Onboard New Staff
                    </div>
                    <form id="createEmployeeForm">
                        <div class="input-group">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="input-group">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+880...">
                            </div>
                            <div class="form-group">
                                <label>Salary (BDT)</label>
                                <input type="number" step="0.01" name="salary" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>System Privilege Role</label>
                            <select name="role" required>
                                <option value="cashier">Cashier (POS System Only)</option>
                                <option value="manager">Store Manager (Inventory & Analytics)</option>
                                <option value="admin">System Administrator (Full Access)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Temporary Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Active Roster
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Staff Member</th>
                                    <th>Contact Info</th>
                                    <th>Salary</th>
                                    <th>Role</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($employees as $emp): 
                                    $fullName = trim($emp['first_name'] . ' ' . $emp['last_name']);
                                    $isOnline = !empty($emp['last_activity']) && (strtotime($emp['last_activity']) > strtotime('-15 minutes'));
                                ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($fullName) ?></div>
                                        <?php if($isOnline): ?>
                                            <span style="font-size: 0.75rem; color: #16a34a;">● Online</span>
                                        <?php else: ?>
                                            <span style="font-size: 0.75rem; color: #94a3b8;">Offline</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="color: #334155;"><?= htmlspecialchars($emp['email']) ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b;"><?= !empty($emp['phone']) ? htmlspecialchars($emp['phone']) : 'No phone' ?></div>
                                    </td>
                                    <td style="font-weight: 500;">
                                        <?= !empty($emp['salary']) ? '৳' . number_format($emp['salary'], 2) : '--' ?>
                                    </td>
                                    <td>
                                        <span class="badge-role role-<?= strtolower($emp['role']) ?>">
                                            <?= htmlspecialchars($emp['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <button type="button" class="btn-icon" onclick='openEditModal(<?= json_encode($emp) ?>)' title="Edit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </button>
                                            <?php if($emp['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn-icon delete" onclick="openDeleteModal(<?= $emp['id'] ?>)" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Employee</h3>
                <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editEmployeeForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="input-group">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="input-group">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Salary (BDT)</label>
                        <input type="number" step="0.01" name="salary" id="edit_salary" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" required>
                        <option value="cashier">Cashier</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>New Password <span style="font-size: 0.75rem; color: #94a3b8;">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <button type="button" class="btn" style="flex: 1; background: #f1f5f9; color: #475569;" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal" style="display: none;">
        <div class="modal-content delete-modal-box">
            <div class="modal-icon">⚠️</div>
            <h3>Remove Employee Profile?</h3>
            <p>Are you sure you want to delete this employee? This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" id="cancelDeleteBtn" class="btn-modal-secondary">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn-modal-danger">Yes, Delete</button>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="toast-container"></div>

    <script>
        // --- TOAST NOTIFICATION FUNCTION ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${message}</span>`;
            
            container.appendChild(toast);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // --- 1. CREATE EMPLOYEE ---
        document.getElementById('createEmployeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../app/controllers/UserController.php?action=create', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500); // Wait 1.5s then reload
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => showToast("Network error. Please try again.", 'error'));
        });

        // --- 2. EDIT EMPLOYEE MODAL CONTROLS ---
        function openEditModal(userData) {
            document.getElementById('edit_id').value = userData.id;
            document.getElementById('edit_first_name').value = userData.first_name;
            document.getElementById('edit_last_name').value = userData.last_name;
            document.getElementById('edit_email').value = userData.email;
            document.getElementById('edit_phone').value = userData.phone || '';
            document.getElementById('edit_salary').value = userData.salary || '';
            document.getElementById('edit_role').value = userData.role;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.getElementById('editEmployeeForm').reset();
        }

        // --- 3. UPDATE EMPLOYEE ---
        document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../app/controllers/UserController.php?action=update', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    closeEditModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => showToast("Network error. Please try again.", 'error'));
        });

        // --- 4. CUSTOM DELETE MODAL CONTROLS ---
        let userIdToDelete = null;

        function openDeleteModal(userId) {
            userIdToDelete = userId;
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeDeleteModal() {
            userIdToDelete = null;
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 200);
        }

        document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!userIdToDelete) return;

            const formData = new FormData();
            formData.append('id', userIdToDelete);

            fetch('../../app/controllers/UserController.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                closeDeleteModal();
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => {
                closeDeleteModal();
                showToast("An error occurred while deleting.", 'error');
            });
        });
    </script>
</body>
</html>