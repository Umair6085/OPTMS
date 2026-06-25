<?php
// views/layout/footer.php
require_once __DIR__ . '/../../config.php';
?>

<?php if (isset($_SESSION['user_id'])): ?>
        </main>
    </div> <!-- Close dashboard-container -->
<?php endif; ?>

<!-- App JS script link -->
<script src="<?php echo url('assets/js/app.js'); ?>"></script>
</body>
</html>
