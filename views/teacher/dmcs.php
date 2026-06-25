<?php
// views/teacher/dmcs.php
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
            <span>Manage Academic Records & DMCs</span>
            <span class="text-sm font-semibold text-slate-400">Update scores or upload student progress reports.</span>
        </div>

        <div class="mb-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" class="form-control table-search pl-10" data-table="student-dmc-table" placeholder="Filter student profiles by name, registration ID...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="student-dmc-table">
                <thead>
                    <tr>
                        <th>Registration No</th>
                        <th>Student Name</th>
                        <th>Class / Grade</th>
                        <th>Obtained Marks / Total</th>
                        <th>PDF Upload</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($studentsList)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-slate-400">No active students registered under active parents yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($studentsList as $stud): ?>
                            <tr>
                                <td class="font-mono text-indigo-300"><?php echo htmlspecialchars($stud['registration_no']); ?></td>
                                <td class="font-semibold text-white"><?php echo htmlspecialchars($stud['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($stud['class_name']); ?></td>
                                <td>
                                    <?php if ($stud['total_marks'] !== null): ?>
                                        <span class="font-semibold text-emerald-400"><?php echo $stud['obtained_marks']; ?></span> 
                                        / <span class="text-slate-400"><?php echo $stud['total_marks']; ?></span>
                                    <?php else: ?>
                                        <em class="text-slate-500">Not Graded</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($stud['pdf_file_path']): ?>
                                        <a href="<?php echo url($stud['pdf_file_path']); ?>" target="_blank" class="text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>View Uploaded DMC</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-500">No Document Uploaded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $stud['total_marks'] !== null ? 'Active Record' : 'Pending'; ?>
                                </td>
                                <td>
                                    <button class="btn btn-secondary btn-sm manage-dmc-btn" 
                                            data-id="<?php echo $stud['student_id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($stud['full_name']); ?>"
                                            data-obtained="<?php echo $stud['obtained_marks'] ?? ''; ?>"
                                            data-total="<?php echo $stud['total_marks'] ?? ''; ?>">
                                        <i class="fa-solid fa-file-signature text-indigo-400"></i>
                                        Grade / Upload
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Grade / Upload DMC -->
<div class="modal" id="manage-dmc-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Grade & Upload DMC</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=teacher_dmc_save'); ?>" method="POST" enctype="multipart/form-data" class="needs-validation">
            <input type="hidden" name="student_id" id="dmc_student_id">
            
            <div class="form-group">
                <label class="form-label">Student Name</label>
                <input type="text" id="dmc_student_name" class="form-control" style="background-color: rgba(255, 255, 255, 0.05); color: #fff;" readonly>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="obtained_marks" class="form-label">Obtained Marks</label>
                    <input type="number" name="obtained_marks" id="obtained_marks" class="form-control" min="0" placeholder="E.g. 420" required>
                </div>
                
                <div class="form-group">
                    <label for="total_marks" class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control" min="1" placeholder="E.g. 500" required>
                </div>
            </div>

            <div class="form-group">
                <label for="dmc_pdf" class="form-label">Upload Scanned DMC (PDF format)</label>
                <input type="file" name="dmc_pdf" id="dmc_pdf" class="form-control" accept=".pdf">
                <p class="text-xs text-slate-500 mt-1">Optional. Upload a PDF of the student's report card.</p>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Save Academic Record</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const manageBtns = document.querySelectorAll(".manage-dmc-btn");
    const dmcModal = document.getElementById("manage-dmc-modal");
    
    manageBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("dmc_student_id").value = btn.getAttribute("data-id");
            document.getElementById("dmc_student_name").value = btn.getAttribute("data-name");
            document.getElementById("obtained_marks").value = btn.getAttribute("data-obtained");
            document.getElementById("total_marks").value = btn.getAttribute("data-total");
            dmcModal.classList.add("active");
        });
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
