<?php
// views/admin/events.php
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Creation Form -->
        <div class="content-card lg:col-span-1">
            <h3 class="card-title">Schedule PTM Event</h3>
            
            <form action="<?php echo url('index.php?action=admin_event_create'); ?>" method="POST" class="needs-validation">
                <div class="form-group">
                    <label for="event_date" class="form-label">PTM Date</label>
                    <input type="date" name="event_date" id="event_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="slot_duration" class="form-label">Duration Per Slot (Minutes)</label>
                    <select name="slot_duration" id="slot_duration" class="form-control form-select" required>
                        <option value="10">10 Minutes</option>
                        <option value="15" selected>15 Minutes</option>
                        <option value="20">20 Minutes</option>
                        <option value="30">30 Minutes</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-6">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Publish PTM Event
                </button>
            </form>
        </div>

        <!-- Scheduled PTM Events List -->
        <div class="content-card lg:col-span-2">
            <h3 class="card-title">Active PTM Calendars</h3>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event Date</th>
                            <th>Timing Range</th>
                            <th>Slot Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-slate-400 py-8">
                                    <i class="fa-regular fa-calendar-times text-4xl text-slate-600 block mb-2"></i>
                                    No PTM events configured yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td>#<?php echo $event['event_id']; ?></td>
                                    <td class="font-semibold text-white">
                                        <?php echo date('l, F d, Y', strtotime($event['event_date'])); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo date('h:i A', strtotime($event['start_time'])); ?> - 
                                            <?php echo date('h:i A', strtotime($event['end_time'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo $event['slot_duration']; ?> minutes</strong> per meeting
                                    </td>
                                    <td>
                                        <?php if (strtotime($event['event_date']) >= strtotime(date('Y-m-d'))): ?>
                                            <span class="badge badge-success">Upcoming</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo url('index.php?action=admin_event_delete&id=' . $event['event_id']); ?>" 
                                           class="btn btn-secondary btn-sm text-red-400 hover:bg-red-950 hover:text-white"
                                           onclick="return confirm('Deleting this event will cancel associated bookings. Continue?');">
                                            <i class="fa-solid fa-trash"></i>
                                            Remove
                                        </a>
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

<?php include __DIR__ . '/../layout/footer.php'; ?>
