<?php
// views/parent/register_student.php
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

    <div class="content-card max-w-xl mx-auto">
        <h3 class="card-title">
            <span>Register Student Details</span>
            <i class="fa-solid fa-child-reaching text-indigo-400"></i>
        </h3>
        
        <p class="text-slate-400 text-sm mb-6">
            Register your child to connect them to the Online Parent-Teacher System. Note: Each registration automatically initiates school dues invoice generation (Rs. 5,000.00).
        </p>

        <form action="<?php echo url('index.php?action=parent_register_student'); ?>" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="student_name" class="form-label">Student Full Name</label>
                <input type="text" name="student_name" id="student_name" class="form-control" placeholder="E.g. Alex Doe" required>
            </div>

            <div class="form-group">
                <label for="registration_no" class="form-label">Registration Number</label>
                <input type="text" name="registration_no" id="registration_no" class="form-control" placeholder="E.g. REG-2026-001" required>
            </div>

            <div class="form-group">
                <label for="class_name" class="form-label">Class Name</label>
                <input type="text" name="class_name" id="class_name" class="form-control" placeholder="E.g. Class 10-A" required>
            </div>

            <div class="form-group">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-8">
                <i class="fa-solid fa-circle-check"></i>
                Submit Student Profile
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
