<?php
// views/auth/login.php
include __DIR__ . '/../layout/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card fade-in">
        <div class="auth-header">
            <div class="inline-flex items-center justify-center p-3 mb-4 rounded-2xl bg-indigo-600 bg-opacity-20 text-indigo-400">
                <i class="fa-solid fa-graduation-cap text-3xl"></i>
            </div>
            <h1>OPTMS Login</h1>
            <p>Parent-Teacher Meeting System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo url('index.php?action=login'); ?>" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="parent@optms.com or teacher@optms.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mb-4 mt-6">
                <span>Sign In</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>

            <div class="text-center text-sm text-slate-400">
                Don't have an account? 
                <a href="<?php echo url('index.php?action=register'); ?>" class="text-indigo-400 hover:text-indigo-300 font-semibold transition">Register here</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
