<?php
// views/auth/register.php
include __DIR__ . '/../layout/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card fade-in" style="max-width: 600px;">
        <div class="auth-header">
            <div class="inline-flex items-center justify-center p-3 mb-4 rounded-2xl bg-indigo-600 bg-opacity-20 text-indigo-400">
                <i class="fa-solid fa-user-plus text-3xl"></i>
            </div>
            <h1>Create Account</h1>
            <p>Join the Parent-Teacher Meeting System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo url('index.php?action=register'); ?>" method="POST" class="needs-validation">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Account Role</label>
                <select name="role" id="role" class="form-control form-select" required>
                    <option value="Parent" selected>Parent</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <!-- Parent Specific Student Details (Toggled via JS) -->
            <div id="student-details-section" class="mt-6 p-4 rounded-xl border border-dashed border-slate-700 bg-slate-900 bg-opacity-40">
                <h3 class="text-sm font-bold text-indigo-400 mb-4 uppercase tracking-wider">
                    <i class="fa-solid fa-child mr-2"></i>Student Details (For Admin Approval)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="student_name" class="form-label">Student Full Name</label>
                        <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Alex Doe">
                    </div>

                    <div class="form-group">
                        <label for="registration_no" class="form-label">Registration Number</label>
                        <input type="text" name="registration_no" id="registration_no" class="form-control" placeholder="REG-2026-XYZ">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="class_name" class="form-label">Class / Grade</label>
                        <input type="text" name="class_name" id="class_name" class="form-control" placeholder="Class 10-A">
                    </div>

                    <div class="form-group">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mb-4 mt-6">
                <span>Sign Up</span>
                <i class="fa-solid fa-user-plus"></i>
            </button>

            <div class="text-center text-sm text-slate-400">
                Already have an account? 
                <a href="<?php echo url('index.php?action=login'); ?>" class="text-indigo-400 hover:text-indigo-300 font-semibold transition">Log in here</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const roleSelect = document.getElementById("role");
    const studentSection = document.getElementById("student-details-section");
    const studentInputs = studentSection.querySelectorAll("input");

    function toggleStudentSection() {
        if (roleSelect.value === "Parent") {
            studentSection.style.display = "block";
            studentInputs.forEach(input => input.setAttribute("required", "required"));
        } else {
            studentSection.style.display = "none";
            studentInputs.forEach(input => input.removeAttribute("required"));
        }
    }

    roleSelect.addEventListener("change", toggleStudentSection);
    toggleStudentSection(); // Initial run
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
