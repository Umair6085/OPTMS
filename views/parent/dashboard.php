<?php
// views/parent/dashboard.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <?php if (isset($_SESSION['feedback_success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo htmlspecialchars($_SESSION['feedback_success']); unset($_SESSION['feedback_success']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Side: Registered Students -->
        <div class="content-card lg:col-span-1">
            <h3 class="card-title">
                <span>Registered Students</span>
                <a href="<?php echo url('index.php?action=parent_register_student'); ?>" class="text-xs text-indigo-400 hover:text-indigo-300">
                    <i class="fa-solid fa-plus mr-1"></i>Add
                </a>
            </h3>

            <?php if (empty($students)): ?>
                <div class="text-center py-6 text-slate-500 text-sm">
                    No students registered yet.<br>
                    <a href="<?php echo url('index.php?action=parent_register_student'); ?>" class="text-indigo-400 hover:underline block mt-2">Register Student Profile</a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($students as $student): ?>
                        <div class="p-4 rounded-xl bg-slate-900 bg-opacity-40 border border-slate-800">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 bg-opacity-10 border border-indigo-500 border-opacity-20 flex items-center justify-center text-indigo-400">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white"><?php echo htmlspecialchars($student['full_name']); ?></h4>
                                    <span class="text-xs text-slate-400 font-mono"><?php echo htmlspecialchars($student['registration_no']); ?></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-slate-300 border-t border-slate-800 pt-2 mt-2">
                                <div>
                                    <span class="text-slate-500 block">CLASS</span>
                                    <strong><?php echo htmlspecialchars($student['class_name']); ?></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">DOB</span>
                                    <strong><?php echo htmlspecialchars($student['dob']); ?></strong>
                                </div>
                            </div>

                            <!-- Academic DMC Summary Check -->
                            <div class="mt-3 pt-2 border-t border-slate-800">
                                <?php 
                                $dmcRecords = Dmc::getByStudent($student['student_id']);
                                if (!empty($dmcRecords)):
                                    $studentDmc = $dmcRecords[0];
                                ?>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-400">DMC Grade Card:</span>
                                        <span class="font-bold text-emerald-400"><?php echo $studentDmc['obtained_marks']; ?> / <?php echo $studentDmc['total_marks']; ?></span>
                                    </div>
                                    <?php if ($studentDmc['pdf_file_path']): ?>
                                        <a href="<?php echo url($studentDmc['pdf_file_path']); ?>" target="_blank" class="text-xs text-indigo-400 hover:underline block mt-1">
                                            <i class="fa-solid fa-file-pdf mr-1"></i>Download Report Card
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs text-slate-500"><i class="fa-solid fa-clock-rotate-left mr-1"></i>No DMC results uploaded yet.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Side: Scheduled Meetings -->
        <div class="content-card lg:col-span-2">
            <h3 class="card-title">Meeting Schedule Settings</h3>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Instructor</th>
                            <th>Slot Time</th>
                            <th>Google Meet Link</th>
                            <th>Status</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($meetings)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-slate-400 py-8">
                                    <i class="fa-regular fa-calendar-times text-4xl text-slate-600 block mb-2"></i>
                                    No booked meetings found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $pendingFeedbackMeeting = null;
                            foreach ($meetings as $meeting): 
                                $hasFeedback = Feedback::getByMeeting($meeting['meeting_id']);
                                if ($meeting['status'] === 'Completed' && !$hasFeedback) {
                                    $pendingFeedbackMeeting = $meeting;
                                }
                            ?>
                                <tr>
                                    <td class="font-semibold text-white"><?php echo htmlspecialchars($meeting['teacher_name']); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo date('M d, Y @ h:i A', strtotime($meeting['slot_time'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        require_once __DIR__ . '/../../models/Settings.php';
                                        $enforceDues = Settings::get('restrict_dues_booking', '1') === '1';
                                        $hasUnpaid = Payment::hasUnpaidDues($_SESSION['user_id']);
                                        
                                        if ($meeting['status'] === 'Scheduled'): 
                                            if ($enforceDues && $hasUnpaid):
                                        ?>
                                                <span class="badge badge-danger" title="Please clear your pending dues to join the session.">
                                                    <i class="fa-solid fa-lock mr-1"></i>
                                                    Restricted: Pay Dues
                                                </span>
                                        <?php else: ?>
                                                <a href="<?php echo url('index.php?action=parent_meeting_room&id=' . $meeting['meeting_id']); ?>" 
                                                   class="btn btn-primary btn-sm flex items-center justify-center gap-1">
                                                    <i class="fa-solid fa-video text-white"></i>
                                                    Join PTM Session
                                                </a>
                                        <?php 
                                            endif;
                                        else: 
                                        ?>
                                            <span class="text-slate-500">Meeting Over</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($meeting['status'] === 'Scheduled'): ?>
                                            <span class="badge badge-warning">Scheduled</span>
                                        <?php elseif ($meeting['status'] === 'Completed'): ?>
                                            <span class="badge badge-success">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($meeting['status'] === 'Completed'): ?>
                                            <?php if ($hasFeedback): ?>
                                                <span class="text-emerald-400 text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i>Rated</span>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" 
                                                        data-open-modal="feedback-modal" 
                                                        data-meeting-id="<?php echo $meeting['meeting_id']; ?>">
                                                    <i class="fa-regular fa-comment-dots text-indigo-400"></i>
                                                    Feedback
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-xs">Waiting Conclude</span>
                                        <?php endif; ?>
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

<!-- Modal: Post-Meeting Feedback -->
<div class="modal <?php echo $pendingFeedbackMeeting ? 'active' : ''; ?>" id="feedback-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Meeting Concluded! Feedback</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=parent_submit_feedback'); ?>" method="POST" class="needs-validation">
            <input type="hidden" name="meeting_id" id="feedback_meeting_id" value="<?php echo $pendingFeedbackMeeting ? $pendingFeedbackMeeting['meeting_id'] : ''; ?>">
            
            <p class="text-sm text-slate-300 mb-4">
                Please rate your meeting session with 
                <strong class="text-white">
                    <?php echo $pendingFeedbackMeeting ? htmlspecialchars($pendingFeedbackMeeting['teacher_name']) : 'the teacher'; ?>
                </strong>:
            </p>

            <!-- Star Rating System -->
            <div class="star-rating">
                <input type="radio" id="star5" name="rating_stars" value="5" required>
                <label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star4" name="rating_stars" value="4">
                <label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star3" name="rating_stars" value="3">
                <label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star2" name="rating_stars" value="2">
                <label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star1" name="rating_stars" value="1">
                <label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
            </div>

            <div class="form-group">
                <label for="comments" class="form-label">Review Comments</label>
                <textarea name="comments" id="comments" class="form-control" rows="4" placeholder="Leave your notes, comments, or any concerns regarding the student's progress..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Submit Feedback Review</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
