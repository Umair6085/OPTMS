<?php
// views/teacher/academic_records.php
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Input Form -->
        <div class="content-card xl:col-span-1">
            <h3 class="card-title" id="form-card-title">Log Academic & Behaviour Progress</h3>
            <p class="text-xs text-slate-500 mb-6">Select a student and post test scores, assignments, behavior remarks, or custom progress reports.</p>

            <form action="<?php echo url('index.php?action=teacher_record_save'); ?>" method="POST" enctype="multipart/form-data" class="needs-validation space-y-4" id="record-form">
                <input type="hidden" name="record_id" id="record_id">

                <div class="form-group">
                    <label for="student_id" class="form-label">Select Student</label>
                    <select name="student_id" id="student_id" class="form-control form-select" required>
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($studentsList as $stud): ?>
                            <option value="<?php echo $stud['student_id']; ?>">
                                <?php echo htmlspecialchars($stud['full_name']); ?> (<?php echo htmlspecialchars($stud['registration_no']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="record_type" class="form-label">Record Type</label>
                    <select name="record_type" id="record_type" class="form-control form-select" required onchange="toggleMarksFields()">
                        <option value="Test">Class Test</option>
                        <option value="Exam">Final Exam</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Behaviour">Behaviour Review</option>
                        <option value="Participation">Class Participation</option>
                        <option value="Document">Other Progress Document</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="record_title" class="form-label">Title / Heading</label>
                    <input type="text" name="title" id="record_title" class="form-control" placeholder="E.g., Math Quiz 1, Weekly Behavior Report" required>
                </div>

                <!-- Marks (hidden for behavior/participation/document by default) -->
                <div class="grid grid-cols-2 gap-4" id="marks-fields-container">
                    <div class="form-group">
                        <label for="obtained_marks" class="form-label">Obtained Marks</label>
                        <input type="number" name="obtained_marks" id="obtained_marks" class="form-control" min="0" placeholder="E.g. 45">
                    </div>
                    <div class="form-group">
                        <label for="total_marks" class="form-label">Total Marks</label>
                        <input type="number" name="total_marks" id="total_marks" class="form-control" min="1" placeholder="E.g. 50">
                    </div>
                </div>

                <div class="form-group">
                    <label for="record_desc" class="form-label">Description / Remarks / Feedback</label>
                    <textarea name="description" id="record_desc" class="form-control" rows="3" placeholder="Provide notes or feedback on student progress..."></textarea>
                </div>

                <div class="form-group">
                    <label for="record_file" class="form-label">Upload PDF / Image File</label>
                    <input type="file" name="record_file" id="record_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <p class="text-xs text-slate-550 mt-1">Optional. Upload marked test sheet scan or behavior certificate (PDF, JPG, PNG).</p>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" class="btn btn-secondary flex-1" id="cancel-edit-btn" style="display: none;" onclick="resetForm()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="submit-btn-text">Log Record</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- History Ledger -->
        <div class="content-card xl:col-span-2">
            <div class="card-title">
                <span>Logged Progress Records Ledger</span>
                <input type="text" class="form-control table-search max-w-xs" data-table="records-log-table" placeholder="Filter records list...">
            </div>

            <div class="table-responsive">
                <table class="table" id="records-log-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Record Type</th>
                            <th>Title & Description</th>
                            <th>Marks</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-slate-400">No student records logged by your account yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $rec): ?>
                                <tr>
                                    <td>
                                        <div class="font-semibold text-white"><?php echo htmlspecialchars($rec['student_name']); ?></div>
                                        <div class="text-xs text-slate-400"><?php echo htmlspecialchars($rec['registration_no']); ?></div>
                                    </td>
                                    <td>
                                        <?php if (in_array($rec['record_type'], ['Test', 'Exam', 'Assignment'])): ?>
                                            <span class="badge badge-info"><?php echo $rec['record_type']; ?></span>
                                        <?php elseif ($rec['record_type'] === 'Behaviour'): ?>
                                            <span class="badge badge-warning"><?php echo $rec['record_type']; ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><?php echo $rec['record_type']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="font-medium text-slate-200"><?php echo htmlspecialchars($rec['title']); ?></div>
                                        <div class="text-xs text-slate-500 max-w-xs truncate" title="<?php echo htmlspecialchars($rec['description']); ?>">
                                            <?php echo htmlspecialchars($rec['description'] ?: 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($rec['total_marks'] !== null): ?>
                                            <strong class="text-emerald-400"><?php echo $rec['obtained_marks']; ?></strong> / <span class="text-slate-400"><?php echo $rec['total_marks']; ?></span>
                                            <span class="text-[10px] text-slate-550 block"><?php echo number_format(($rec['obtained_marks'] / $rec['total_marks']) * 100, 1); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-slate-550">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($rec['file_path']): ?>
                                            <a href="<?php echo url($rec['file_path']); ?>" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                                                <i class="fa-solid fa-file-arrow-down"></i>
                                                <span>View File</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-600 text-xs">No scan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button class="btn btn-secondary btn-sm edit-record-btn"
                                                    data-id="<?php echo $rec['record_id']; ?>"
                                                    data-student="<?php echo $rec['student_id']; ?>"
                                                    data-type="<?php echo $rec['record_type']; ?>"
                                                    data-title="<?php echo htmlspecialchars($rec['title']); ?>"
                                                    data-obtained="<?php echo $rec['obtained_marks']; ?>"
                                                    data-total="<?php echo $rec['total_marks']; ?>"
                                                    data-desc="<?php echo htmlspecialchars($rec['description']); ?>">
                                                <i class="fa-solid fa-pen text-indigo-400"></i>
                                            </button>
                                            <a href="<?php echo url('index.php?action=teacher_record_delete&id=' . $rec['record_id']); ?>"
                                               class="btn btn-secondary btn-sm text-red-500 hover:bg-red-950"
                                               onclick="return confirm('Are you sure you want to delete this progress record?');">
                                                <i class="fa-solid fa-trash"></i>
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
</div>

<script>
function toggleMarksFields() {
    const typeSelect = document.getElementById("record_type");
    const marksContainer = document.getElementById("marks-fields-container");
    const type = typeSelect.value;
    
    // Hide marks for Behaviour, Participation and Documents
    if (type === 'Behaviour' || type === 'Document') {
        marksContainer.style.display = "none";
        document.getElementById("obtained_marks").required = false;
        document.getElementById("total_marks").required = false;
    } else {
        marksContainer.style.display = "grid";
        // Optionally make it optional/required
    }
}

function resetForm() {
    document.getElementById("record_id").value = "";
    document.getElementById("student_id").value = "";
    document.getElementById("record_type").value = "Test";
    document.getElementById("record_title").value = "";
    document.getElementById("obtained_marks").value = "";
    document.getElementById("total_marks").value = "";
    document.getElementById("record_desc").value = "";
    document.getElementById("record_file").value = "";
    
    document.getElementById("form-card-title").textContent = "Log Academic & Behaviour Progress";
    document.getElementById("submit-btn-text").textContent = "Log Record";
    document.getElementById("cancel-edit-btn").style.display = "none";
    document.getElementById("student_id").disabled = false;
    
    toggleMarksFields();
}

document.addEventListener("DOMContentLoaded", () => {
    toggleMarksFields();

    const editBtns = document.querySelectorAll(".edit-record-btn");
    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("record_id").value = btn.getAttribute("data-id");
            document.getElementById("student_id").value = btn.getAttribute("data-student");
            // Disable changing student on edit to keep log logic consistency
            document.getElementById("student_id").disabled = true;
            document.getElementById("record_type").value = btn.getAttribute("data-type");
            document.getElementById("record_title").value = btn.getAttribute("data-title");
            document.getElementById("obtained_marks").value = btn.getAttribute("data-obtained") || "";
            document.getElementById("total_marks").value = btn.getAttribute("data-total") || "";
            document.getElementById("record_desc").value = btn.getAttribute("data-desc") || "";

            document.getElementById("form-card-title").textContent = "Modify Progress Record #" + btn.getAttribute("data-id");
            document.getElementById("submit-btn-text").textContent = "Save Changes";
            document.getElementById("cancel-edit-btn").style.display = "inline-flex";
            
            toggleMarksFields();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    });

    // Make sure student_id is sent even if disabled on form submit
    document.getElementById("record-form").addEventListener("submit", () => {
        document.getElementById("student_id").disabled = false;
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
