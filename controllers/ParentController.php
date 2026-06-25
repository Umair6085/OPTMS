<?php
// controllers/ParentController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Meeting.php';
require_once __DIR__ . '/../models/Feedback.php';

class ParentController {

    public function registerStudent() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['student_name']);
            $regNo = trim($_POST['registration_no']);
            $className = trim($_POST['class_name']);
            $dob = trim($_POST['dob']);
            $parentId = $_SESSION['user_id'];

            if (empty($fullName) || empty($regNo) || empty($className) || empty($dob)) {
                $error = 'All student registration fields are required.';
            } elseif (Student::findByRegistrationNo($regNo)) {
                $error = 'Student Registration Number already registered in our system.';
            } else {
                try {
                    global $pdo;
                    $pdo->beginTransaction();

                    $studentId = Student::create($parentId, $fullName, $regNo, $className, $dob);
                    Payment::createInvoice($studentId, 5000.00); // Standard fee

                    $pdo->commit();
                    $success = 'Student successfully registered! Standard dues generated.';
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error = $e->getMessage();
                }
            }
        }

        include __DIR__ . '/../views/parent/register_student.php';
    }

    public function verifyDues() {
        $dues = Payment::getByParent($_SESSION['user_id']);
        include __DIR__ . '/../views/parent/dues.php';
    }

    public function jazzcashSandbox() {
        $paymentId = (int)$_GET['payment_id'];
        $payment = Payment::findById($paymentId);

        if (!$payment) {
            redirect('index.php?action=parent_dues');
        }

        // Validate that this payment belongs to the logged-in parent's student
        global $pdo;
        $stmt = $pdo->prepare("SELECT s.parent_id FROM Students s JOIN Payments p ON s.student_id = p.student_id WHERE p.payment_id = ?");
        $stmt->execute([$paymentId]);
        $ownerId = $stmt->fetchColumn();

        if ($ownerId != $_SESSION['user_id']) {
            redirect('index.php?action=parent_dues');
        }

        if ($payment['status'] === 'Paid') {
            redirect('index.php?action=parent_dues');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simulated transaction processing
            $trxId = 'JCX-' . rand(100000, 999999) . date('md');
            Payment::markAsPaid($paymentId, $trxId);
            
            $_SESSION['payment_success'] = "Payment processed successfully via JazzCash! Transaction ID: $trxId";
            redirect('index.php?action=parent_dues');
        }

        include __DIR__ . '/../views/parent/jazzcash_sandbox.php';
    }

    public function bookSlot() {
        // Financial Gatekeeper: restrict booking if setting is ON and any dues are unpaid
        require_once __DIR__ . '/../models/Settings.php';
        $enforceDues = Settings::get('restrict_dues_booking', '1') === '1';
        if ($enforceDues && Payment::hasUnpaidDues($_SESSION['user_id'])) {
            $_SESSION['booking_error'] = 'Access restricted: Please clear your pending dues to unlock the Meeting Scheduler.';
            redirect('index.php?action=parent_dues');
        }

        $error = '';
        $success = '';

        // Handle Booking submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teacherId = (int)$_POST['teacher_id'];
            $slotTime = $_POST['slot_time'];
            $parentId = $_SESSION['user_id'];

            if (empty($teacherId) || empty($slotTime)) {
                $error = 'Please select a teacher and an available meeting slot.';
            } else {
                require_once __DIR__ . '/../models/TimeSlots.php';
                $matchedSlot = TimeSlots::getByTeacherAndTime($teacherId, $slotTime);
                
                if (!$matchedSlot || $matchedSlot['status'] !== 'Available') {
                    $error = 'The selected meeting slot has just been booked by another parent. Please choose another slot.';
                } else {
                    // Generate Google Meet Link
                    $meetLink = 'https://meet.google.com/' . substr(md5(uniqid()), 0, 3) . '-' . substr(md5(uniqid()), 4, 4) . '-' . substr(md5(uniqid()), 9, 3);
                    
                    Meeting::create($teacherId, $parentId, $slotTime, $meetLink, $matchedSlot['duration']);
                    
                    // Mark slot as booked
                    TimeSlots::updateStatus($matchedSlot['slot_id'], 'Booked');
                    
                    $success = 'PTM meeting slot booked successfully! Google Meet link generated.';
                }
            }
        }

        // Fetch teachers list to select
        $teachers = User::getByRole('Teacher');

        // Fetch upcoming PTM events defined by admin
        $ptmEvents = Meeting::getPtmEvents();

        // Calculate available slots dynamically for selected teacher
        $selectedTeacherId = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : null;
        $selectedEventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
        
        $availableSlots = [];
        $event = null;
        if ($selectedTeacherId && $selectedEventId) {
            // Find chosen event
            foreach ($ptmEvents as $e) {
                if ($e['event_id'] == $selectedEventId) {
                    $event = $e;
                    break;
                }
            }

            if ($event) {
                // Booking date validation: restrict booking to 1 or 2 days prior to the event date
                $eventDateStr = $event['event_date'];
                $eventTime = strtotime($eventDateStr . ' 00:00:00');
                $todayTime = strtotime(date('Y-m-d') . ' 00:00:00');
                $diffDays = ($eventTime - $todayTime) / 86400;

                if ($diffDays < 1 || $diffDays > 2) {
                    $error = "Booking access restricted: You can only book a slot 1 or 2 days prior to the PTM date (" . date('M d, Y', $eventTime) . "). Today is " . date('M d, Y') . ".";
                    $selectedTeacherId = null; // Prevent showing slots
                } else {
                    require_once __DIR__ . '/../models/TimeSlots.php';
                    global $pdo;
                    $stmt = $pdo->prepare("SELECT * FROM TimeSlots 
                                           WHERE teacher_id = ? 
                                           AND DATE(slot_time) = ? 
                                           ORDER BY slot_time ASC");
                    $stmt->execute([$selectedTeacherId, $event['event_date']]);
                    $slotsFromDb = $stmt->fetchAll();

                    foreach ($slotsFromDb as $slot) {
                        $time = strtotime($slot['slot_time']);
                        $availableSlots[] = [
                            'slot_id' => $slot['slot_id'],
                            'time_raw' => $slot['slot_time'],
                            'time_label' => date('h:i A', $time),
                            'is_booked' => ($slot['status'] !== 'Available')
                        ];
                    }
                }
            }
        }

        include __DIR__ . '/../views/parent/book_slot.php';
    }

    public function submitFeedback() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $meetingId = (int)$_POST['meeting_id'];
            $ratingStars = (int)$_POST['rating_stars'];
            $comments = trim($_POST['comments']);

            if ($ratingStars >= 1 && $ratingStars <= 5) {
                Feedback::create($meetingId, $ratingStars, $comments);
                $_SESSION['feedback_success'] = 'Thank you for your valuable feedback!';
            }
        }
        redirect('index.php?action=dashboard');
    }

    public function meetingRoom() {
        $meetingId = (int)$_GET['id'];
        $meeting = Meeting::findById($meetingId);

        if (!$meeting || $meeting['parent_id'] != $_SESSION['user_id']) {
            redirect('index.php?action=dashboard');
        }

        // Check dues restriction setting
        require_once __DIR__ . '/../models/Settings.php';
        $enforceDues = Settings::get('restrict_dues_booking', '1') === '1';
        if ($enforceDues && Payment::hasUnpaidDues($_SESSION['user_id'])) {
            $_SESSION['dues_error'] = 'Access restricted: Please clear your pending dues to unlock the Live PTM Session.';
            redirect('index.php?action=parent_dues');
        }

        // Fetch student linked to parent
        $students = Student::getByParent($_SESSION['user_id']);
        $student = null;
        $dmc = null;
        $academicRecords = [];
        if (!empty($students)) {
            $student = $students[0];
            $dmcList = Dmc::getByStudent($student['student_id']);
            if (!empty($dmcList)) {
                $dmc = $dmcList[0];
            }
            require_once __DIR__ . '/../models/AcademicRecords.php';
            $academicRecords = AcademicRecords::getByStudent($student['student_id']);
        }

        // Fetch teacher details
        $teacher = User::findById($meeting['teacher_id']);

        include __DIR__ . '/../views/parent/meeting_room.php';
    }
}
?>
