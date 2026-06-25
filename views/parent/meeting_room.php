<?php
// views/parent/meeting_room.php
include __DIR__ . '/../layout/header.php';
?>

<div class="fade-in">
    <!-- Top Action bar -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold">PTM Meeting Session</h3>
            <p class="text-slate-400 text-sm">Meeting with Teacher: <strong class="text-white"><?php echo htmlspecialchars($teacher['name']); ?></strong> (Subject Instructor)</p>
        </div>
        
        <div class="flex gap-2">
            <button class="btn btn-danger btn-sm" data-open-modal="feedback-modal">
                <i class="fa-solid fa-phone-slash mr-1"></i>
                Leave Session & Rate
            </button>
        </div>
    </div>

    <!-- Side by Side Layout -->
    <div class="meeting-panel-layout">
        <!-- Google Meet Simulator Box -->
        <div class="content-card mb-0 flex flex-col justify-between" style="min-height: 520px;">
            <div class="card-title mb-2">
                <span>Google Meet Video Feed</span>
                <span class="badge badge-success flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    Live Connection
                </span>
            </div>

            <!-- Video Call Canvas Frame -->
            <div class="embedded-view relative flex flex-col items-center justify-center bg-slate-950 rounded-xl overflow-hidden flex-1 my-4">
                <!-- Teacher Video Feed Simulator -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="absolute w-48 h-48 rounded-full border border-indigo-550 opacity-20 animate-ping"></div>
                    <div class="text-center z-10">
                        <div class="w-24 h-24 rounded-full bg-slate-800 border-2 border-indigo-500 flex items-center justify-center text-4xl font-bold text-white mx-auto shadow-2xl mb-4">
                            <?php echo strtoupper(substr($teacher['name'], 0, 1)); ?>
                        </div>
                        <h4 class="text-lg font-semibold text-slate-200"><?php echo htmlspecialchars($teacher['name']); ?></h4>
                        <p class="text-xs text-indigo-400 font-mono text-center">Conducting live performance review...</p>
                    </div>
                </div>

                <!-- Self Parent Camera View Overlay -->
                <div class="absolute bottom-4 right-4 w-32 h-24 rounded-lg bg-slate-900 border border-slate-700 shadow-xl overflow-hidden flex items-center justify-center">
                    <span class="text-xs text-slate-400 font-semibold">You (Parent)</span>
                </div>
                
                <!-- Timing indicator -->
                <div class="absolute bottom-4 left-4 bg-black bg-opacity-70 px-3 py-1.5 rounded-lg border border-slate-800 text-xs font-mono text-indigo-300">
                    <i class="fa-solid fa-clock mr-1 animate-pulse text-indigo-500"></i>
                    <span id="call-timer">00:00</span> / <?php echo $meeting['duration']; ?> mins
                </div>
            </div>

            <!-- Call Command Controls -->
            <div class="flex justify-center gap-4 py-2 border-t border-slate-800">
                <button class="w-10 h-10 rounded-full bg-slate-800 text-white hover:bg-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-microphone"></i>
                </button>
                <button class="w-10 h-10 rounded-full bg-slate-800 text-white hover:bg-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-video"></i>
                </button>
                <button class="w-10 h-10 rounded-full bg-red-600 text-white hover:bg-red-500 flex items-center justify-center transition" data-open-modal="feedback-modal" title="End Call">
                    <i class="fa-solid fa-phone-slash"></i>
                </button>
            </div>
        </div>

        <!-- Student DMC & Academic Progress Panel -->
        <div class="dmc-panel flex flex-col">
            <h3 class="text-lg font-bold border-b border-slate-800 pb-3 mb-4">
                <i class="fa-solid fa-graduation-cap text-indigo-400 mr-2"></i>
                Student Performance & Records
            </h3>

            <?php if ($student): ?>
                <div class="space-y-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="grid grid-cols-2 gap-4 text-sm bg-slate-900 bg-opacity-40 p-3 rounded-lg border border-slate-800">
                            <div>
                                <span class="text-xs text-slate-500 block">STUDENT</span>
                                <strong class="text-white text-base"><?php echo htmlspecialchars($student['full_name']); ?></strong>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block">REG NO</span>
                                <strong class="text-slate-200 font-mono text-sm"><?php echo htmlspecialchars($student['registration_no']); ?></strong>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block">CLASS / GRADE</span>
                                <strong class="text-slate-300"><?php echo htmlspecialchars($student['class_name']); ?></strong>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block">STUDENT DB ID</span>
                                <strong class="text-slate-300">#<?php echo $student['student_id']; ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="flex border-b border-slate-800 my-2">
                        <button class="flex-1 pb-2 text-center text-xs font-bold border-b-2 border-indigo-500 text-white tab-btn" onclick="openTab(event, 'tab-marks')">Test Marks</button>
                        <button class="flex-1 pb-2 text-center text-xs font-bold border-b-2 border-transparent text-slate-400 tab-btn" onclick="openTab(event, 'tab-behaviour')">Behaviour & Comments</button>
                        <button class="flex-1 pb-2 text-center text-xs font-bold border-b-2 border-transparent text-slate-400 tab-btn" onclick="openTab(event, 'tab-dmc')">Original DMC</button>
                    </div>

                    <!-- Tab 1: Marks -->
                    <div id="tab-marks" class="tab-content block space-y-3">
                        <div class="p-3 rounded-xl border border-slate-800 bg-slate-900 bg-opacity-40 max-h-[300px] overflow-y-auto">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Academic Ledger</h4>
                            <?php if (empty($academicRecords)): ?>
                                <p class="text-xs text-slate-550 text-center py-4">No individual test marks recorded yet.</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php foreach ($academicRecords as $ar): 
                                        if (!in_array($ar['record_type'], ['Test', 'Exam', 'Assignment'])) continue;
                                    ?>
                                        <div class="flex justify-between items-center text-xs border-b border-slate-850 pb-2 mb-2">
                                            <div>
                                                <strong class="text-white block"><?php echo htmlspecialchars($ar['title']); ?></strong>
                                                <span class="text-slate-500"><?php echo $ar['record_type']; ?> | <?php echo date('M d, Y', strtotime($ar['created_at'])); ?></span>
                                            </div>
                                            <div class="text-right">
                                                <strong class="text-emerald-400"><?php echo $ar['obtained_marks']; ?> / <?php echo $ar['total_marks']; ?></strong>
                                                <?php if ($ar['file_path']): ?>
                                                    <a href="<?php echo url($ar['file_path']); ?>" target="_blank" class="text-[10px] text-indigo-400 block hover:underline">Download Paper</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab 2: Behaviour -->
                    <div id="tab-behaviour" class="tab-content hidden space-y-3">
                        <div class="p-3 rounded-xl border border-slate-800 bg-slate-900 bg-opacity-40 max-h-[300px] overflow-y-auto">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Behaviour & Classroom Participation Logs</h4>
                            <?php if (empty($academicRecords)): ?>
                                <p class="text-xs text-slate-550 text-center py-4">No behavior reports recorded yet.</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php foreach ($academicRecords as $ar): 
                                        if (!in_array($ar['record_type'], ['Behaviour', 'Participation'])) continue;
                                    ?>
                                        <div class="text-xs border-b border-slate-850 pb-2 mb-2">
                                            <div class="flex justify-between mb-1">
                                                <span class="badge badge-warning text-[9px]"><?php echo $ar['record_type']; ?></span>
                                                <span class="text-slate-500"><?php echo date('M d, Y', strtotime($ar['created_at'])); ?></span>
                                            </div>
                                            <strong class="text-slate-200 block"><?php echo htmlspecialchars($ar['title']); ?></strong>
                                            <p class="text-slate-400 mt-1"><?php echo htmlspecialchars($ar['description'] ?: 'No comments'); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab 3: DMC -->
                    <div id="tab-dmc" class="tab-content hidden flex-1 flex flex-col">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Scanned DMC Report Card (PDF)</span>
                        <?php if ($dmc && $dmc['pdf_file_path']): ?>
                            <iframe src="<?php echo url($dmc['pdf_file_path']); ?>" class="dmc-pdf-viewer flex-1 mt-2" style="border: 1px solid var(--border-color); border-radius: 8px;"></iframe>
                        <?php else: ?>
                            <div class="dmc-pdf-viewer flex-1 flex flex-col items-center justify-center p-4 mt-2">
                                <i class="fa-solid fa-file-pdf text-4xl text-slate-600 mb-2"></i>
                                <span class="text-xs text-slate-500">No scanned PDF document uploaded yet.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-slate-500 text-sm">No student profile is registered under your account yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Post-Meeting Feedback Form -->
<div class="modal" id="feedback-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Conclude Session & Leave Feedback</h3>
            <button class="close-modal">&times;</button>
        </div>
        <form action="<?php echo url('index.php?action=parent_submit_feedback'); ?>" method="POST" class="needs-validation">
            <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
            
            <p class="text-sm text-slate-350 mb-4">
                Thank you for attending the PTM. Please submit your rating and comments for instructor <strong class="text-white"><?php echo htmlspecialchars($teacher['name']); ?></strong> to conclude the meeting session:
            </p>

            <!-- Star Rating -->
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
                <label for="comments" class="form-label">Feedback Comments</label>
                <textarea name="comments" id="comments" class="form-control" rows="4" placeholder="Share your experience or note any critical concerns regarding student performance..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-6">Submit Feedback & Exit</button>
        </form>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    const tabcontents = document.querySelectorAll(".tab-content");
    tabcontents.forEach(tc => {
        tc.classList.add("hidden");
        tc.classList.remove("block");
    });

    const tablinks = document.querySelectorAll(".tab-btn");
    tablinks.forEach(tl => {
        tl.classList.remove("border-indigo-500", "text-white");
        tl.classList.add("border-transparent", "text-slate-400");
    });

    const activeTab = document.getElementById(tabName);
    activeTab.classList.remove("hidden");
    activeTab.classList.add("block");
    
    evt.currentTarget.classList.remove("border-transparent", "text-slate-400");
    evt.currentTarget.classList.add("border-indigo-500", "text-white");
}

document.addEventListener("DOMContentLoaded", () => {
    // Call Timer simulation
    let seconds = 0;
    const timerEl = document.getElementById("call-timer");
    setInterval(() => {
        seconds++;
        let mins = Math.floor(seconds / 60);
        let secs = seconds % 60;
        timerEl.textContent = 
            (mins < 10 ? "0" + mins : mins) + ":" + 
            (secs < 10 ? "0" + secs : secs);
    }, 1000);
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
