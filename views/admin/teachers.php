<?php
// views/admin/teachers.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="content-card">
        <div class="card-title">
            <span>Teacher Records Directory</span>
            <button class="btn btn-primary btn-sm" data-open-modal="add-teacher-modal">
                <i class="fa-solid fa-user-plus"></i>
                Add New Teacher
            </button>
        </div>

        <div class="mb-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" class="form-control table-search pl-10" data-table="teachers-list-table" placeholder="Search teachers by name or email in real-time...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="teachers-list-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-slate-400">No teachers registered yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td>#<?php echo $teacher['user_id']; ?></td>
                                <td class="font-semibold text-white"><?php echo htmlspecialchars($teacher['name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td>
                                    <span class="badge badge-success"><?php echo $teacher['status']; ?></span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <button class="btn btn-secondary btn-sm edit-teacher-btn" 
                                                data-id="<?php echo $teacher['user_id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($teacher['name']); ?>" 
                                                data-email="<?php echo htmlspecialchars($teacher['email']); ?>">
                                            <i class="fa-solid fa-pen-to-square text-indigo-400"></i>
                                            Edit
                                        </button>
                                        <a href="<?php echo url('index.php?action=admin_teacher_delete&id=' . $teacher['user_id']); ?>" 
                                           class="btn btn-secondary btn-sm hover:bg-red-950" 
                                           onclick="return confirm('Are you sure you want to delete this teacher?');">
                                            <i class="fa-solid fa-trash text-red-500"></i>
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Teacher -->
<div class="modal" id="add-teacher-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Teacher Profile</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_teacher_create'); ?>" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="new_name" class="form-label">Full Name</label>
                <input type="text" name="name" id="new_name" class="form-control" placeholder="E.g. Sir Ahmed" required>
            </div>
            <div class="form-group">
                <label for="new_email" class="form-label">Email Address</label>
                <input type="email" name="email" id="new_email" class="form-control" placeholder="teacher@optms.com" required>
            </div>
            <div class="form-group">
                <label for="new_password" class="form-label">Password</label>
                <input type="password" name="password" id="new_password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-6">Create Teacher Account</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Teacher -->
<div class="modal" id="edit-teacher-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Teacher Profile</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=admin_teacher_edit'); ?>" method="POST" class="needs-validation">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label for="edit_name" class="form-label">Full Name</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_email" class="form-label">Email Address</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit_password" class="form-label">New Password (Leave blank to keep current)</label>
                <input type="password" name="password" id="edit_password" class="form-control" placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-6">Save Changes</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtns = document.querySelectorAll(".edit-teacher-btn");
    const editModal = document.getElementById("edit-teacher-modal");
    
    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("edit_user_id").value = btn.getAttribute("data-id");
            document.getElementById("edit_name").value = btn.getAttribute("data-name");
            document.getElementById("edit_email").value = btn.getAttribute("data-email");
            document.getElementById("edit_password").value = "";
            editModal.classList.add("active");
        });
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
