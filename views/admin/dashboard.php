<?php
// views/admin/dashboard.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <!-- Quick Analytics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $teachersCount; ?></h3>
                <p>Teachers Registered</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-chalkboard-user text-indigo-500"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $pendingParents; ?></h3>
                <p>Pending Parents</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-user-clock text-amber-500"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $meetingsCount; ?></h3>
                <p>Scheduled PTMs</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-calendar-check text-cyan-500"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>Rs. <?php echo number_format($earnings, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-wallet text-emerald-500"></i>
            </div>
        </div>
    </div>

    <!-- Administrative Quick Access Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="content-card">
            <h3 class="card-title">
                <span>Recent Event Logs</span>
                <i class="fa-solid fa-circle-info text-slate-400 text-sm"></i>
            </h3>
            <p class="text-slate-400 text-sm mb-4">Quick overview of system and active connections.</p>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 bg-opacity-40 border border-slate-800">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-database text-emerald-400"></i>
                        <div>
                            <p class="text-sm font-semibold">Database Connection</p>
                            <p class="text-xs text-slate-500">Active - localhost (PDO)</p>
                        </div>
                    </div>
                    <span class="badge badge-success">Online</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 bg-opacity-40 border border-slate-800">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-network-wired text-cyan-400"></i>
                        <div>
                            <p class="text-sm font-semibold">External Integration</p>
                            <p class="text-xs text-slate-500">Mock Sandbox Enabled</p>
                        </div>
                    </div>
                    <span class="badge badge-info">Sandbox</span>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h3 class="card-title">
                <span>System Policy Settings</span>
                <i class="fa-solid fa-sliders text-indigo-400 text-sm"></i>
            </h3>
            <p class="text-slate-400 text-sm mb-4">Toggle system-wide policy rules and parents dues gatekeeping.</p>
            
            <form action="<?php echo url('index.php?action=admin_settings_toggle'); ?>" method="POST" class="space-y-4">
                <?php 
                require_once __DIR__ . '/../../models/Settings.php';
                $restrict = Settings::get('restrict_dues_booking', '1') === '1';
                ?>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 bg-opacity-40 border border-slate-800">
                    <div class="pr-2">
                        <p class="text-sm font-semibold text-white">Restrict Dues-Pending Parents</p>
                        <p class="text-xs text-slate-500 mt-1">Block booking & meeting joining if dues are unpaid.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="restrict_dues_booking" value="1" class="sr-only peer" <?php echo $restrict ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <div class="w-11 h-6 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </form>
            <?php if (isset($_SESSION['settings_success'])): ?>
                <div class="alert alert-success mt-3 py-2 text-xs">
                    <i class="fa-solid fa-circle-check mr-1"></i>
                    <?php echo $_SESSION['settings_success']; unset($_SESSION['settings_success']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h3 class="card-title">
                <span>Key Guidelines</span>
                <i class="fa-solid fa-circle-question text-slate-400 text-sm"></i>
            </h3>
            <ul class="space-y-3 text-sm text-slate-300">
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-chevron-right text-indigo-400 mt-1 text-xs"></i>
                    <span>Verify Parent registrations matching correct registration numbers and approved class name.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-chevron-right text-indigo-400 mt-1 text-xs"></i>
                    <span>Schedule official PTM events specifying durations to activate parent slot booking.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-chevron-right text-indigo-400 mt-1 text-xs"></i>
                    <span>Generate Financial profit/loss summaries to reconcile invoices paid via JazzCash sandbox.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
