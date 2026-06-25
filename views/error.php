<?php
// views/error.php
include __DIR__ . '/layout/header.php';
?>

<div class="auth-wrapper" style="min-height: 70vh;">
    <div class="auth-card text-center fade-in">
        <div class="inline-flex items-center justify-center p-4 rounded-full bg-red-950 border border-red-500 border-opacity-30 text-red-400 mb-6">
            <i class="fa-solid fa-circle-exclamation text-4xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold mb-2 text-white">404 - Not Found</h1>
        <p class="text-slate-400 text-sm mb-6">The action controller endpoint or file path you requested does not exist or has been relocated.</p>
        <a href="<?php echo url('index.php?action=dashboard'); ?>" class="btn btn-primary">
            <i class="fa-solid fa-house mr-1"></i>
            Return to Dashboard
        </a>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
