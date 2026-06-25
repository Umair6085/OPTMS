<?php
// views/teacher/profile.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in max-w-xl mx-auto">
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
        <h3 class="card-title">
            <span>Manage Profile Settings</span>
            <i class="fa-solid fa-user-gear text-indigo-400 text-sm"></i>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Modify your name, active email registration, or update credentials.</p>

        <form action="<?php echo url('index.php?action=teacher_profile_update'); ?>" method="POST" class="needs-validation space-y-4">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                <p class="text-xs text-slate-500 mt-1">Leave blank to keep your current password.</p>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">
                <i class="fa-solid fa-floppy-disk"></i>
                Save Profile Changes
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
