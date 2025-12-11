<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Course Schedule</h1>
        <a href="<?= base_url('admin/courses/offering/create') ?>" class="btn btn-primary">Create Course Offering</a>
    </div>

    <?php if (! empty($courses)): ?>
        <?php $first = $courses[0]; ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center" role="alert">
            <div>
                <strong>Academic Year:</strong> <?= esc($first['academic_year']) ?>
                &middot;
                <strong>Semester:</strong> <?= esc($first['semester']) ?>
            </div>
            <div class="small text-muted">
                Total courses: <strong><?= count($courses) ?></strong>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            No course schedule available yet. Create a new course offering to populate this list.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm bg-light text-dark">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Scheduled Courses</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-primary text-center text-dark">
                        <tr>
                            <th>Course</th>
                            <th>Course #</th>
                            <th>Schedule</th>
                            <th>Teacher</th>
                            <th>Enrolled</th>
                            <th>Term / Sem / AY</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <?php
                                    $status = $course['status'] ?? 'Upcoming';
                                    $badgeClass = 'bg-secondary';
                                    if ($status === 'Upcoming') {
                                        $badgeClass = 'bg-info';
                                    } elseif ($status === 'Ongoing') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status === 'Completed') {
                                        $badgeClass = 'bg-secondary';
                                    }

                                    $date = $course['schedule_date']
                                        ? date('M d, Y', strtotime($course['schedule_date']))
                                        : '';
                                    $time = $course['schedule_time']
                                        ? date('h:i A', strtotime($course['schedule_time']))
                                        : '';
                                    $scheduleText = trim($date . ' ' . $time);
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= esc($course['title']) ?></div>
                                        <div class="small text-muted">ID: <?= esc($course['id']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?= esc($course['course_number']) ?>
                                    </td>
                                    <td>
                                        <div class="schedule-display" id="schedule-<?= $course['id'] ?>">
                                            <?php if ($scheduleText !== ''): ?>
                                                <div class="fw-semibold">
                                                    <?= esc($date) ?>
                                                    <?php if (!empty($course['schedule_date'])): ?>
                                                        <span class="text-primary">(<?= date('l', strtotime($course['schedule_date'])) ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small text-muted"><?= esc($time) ?></div>
                                            <?php else: ?>
                                                <span class="text-muted">Not scheduled</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="schedule-edit" id="schedule-edit-<?= $course['id'] ?>" style="display: none;">
                                            <form class="schedule-edit-form" data-course-id="<?= $course['id'] ?>">
                                                <div class="mb-2">
                                                    <label class="form-label small">Date</label>
                                                    <input type="date" 
                                                           class="form-control form-control-sm schedule-date-input" 
                                                           name="schedule_date" 
                                                           id="schedule-date-<?= $course['id'] ?>"
                                                           value="<?= esc($course['schedule_date'] ?? '') ?>" 
                                                           required>
                                                    <div class="day-name-display small text-primary fw-semibold mt-1" id="day-name-<?= $course['id'] ?>">
                                                        <?php 
                                                        if (!empty($course['schedule_date'])) {
                                                            $dayName = date('l', strtotime($course['schedule_date']));
                                                            echo esc($dayName);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Time</label>
                                                    <input type="time" 
                                                           class="form-control form-control-sm" 
                                                           name="schedule_time" 
                                                           value="<?= esc($course['schedule_time'] ?? '') ?>" 
                                                           required>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                    <button type="button" class="btn btn-secondary btn-sm cancel-edit">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <?= esc($course['teacher_name'] ?? 'Unassigned') ?>
                                    </td>
                                    <td class="text-center">
                                        <?= esc($course['enrolled_count'] ?? 0) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= esc($course['term']) ?> / <?= esc($course['semester']) ?> / <?= esc($course['academic_year']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?>"><?= esc($status) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-info edit-schedule-btn" 
                                                    data-course-id="<?= $course['id'] ?>"
                                                    title="Edit Schedule">
                                                <i class="bi bi-calendar-event"></i> Schedule
                                            </button>
                                            <a href="<?= site_url('/courses/edit/' . $course['id']) ?>" class="btn btn-outline-primary">Edit</a>
                                            <a href="<?= site_url('/course/enrolled?course_id=' . $course['id']) ?>" class="btn btn-outline-secondary">View Students</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No course schedule available.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('schedule_success')): ?>
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 9999;">
        <?= esc(session()->getFlashdata('schedule_success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('schedule_error')): ?>
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 9999;">
        <?= esc(session()->getFlashdata('schedule_error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update day name display
    function updateDayName(dateInput) {
        const courseId = dateInput.id.replace('schedule-date-', '');
        const dayNameDiv = document.getElementById('day-name-' + courseId);
        
        if (dateInput.value && dayNameDiv) {
            const date = new Date(dateInput.value + 'T00:00:00');
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = dayNames[date.getDay()];
            dayNameDiv.textContent = dayName;
            dayNameDiv.style.display = 'block';
        } else if (dayNameDiv) {
            dayNameDiv.style.display = 'none';
        }
    }

    // Initialize day names for existing dates
    document.querySelectorAll('.schedule-date-input').forEach(input => {
        if (input.value) {
            updateDayName(input);
        }
        
        // Update day name when date changes
        input.addEventListener('change', function() {
            updateDayName(this);
        });
    });

    // Handle edit schedule button clicks
    document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const displayDiv = document.getElementById('schedule-' + courseId);
            const editDiv = document.getElementById('schedule-edit-' + courseId);
            
            if (displayDiv && editDiv) {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
                
                // Update day name when opening edit form
                const dateInput = document.getElementById('schedule-date-' + courseId);
                if (dateInput) {
                    updateDayName(dateInput);
                }
            }
        });
    });

    // Handle cancel button clicks
    document.querySelectorAll('.cancel-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.schedule-edit-form');
            const courseId = form.getAttribute('data-course-id');
            const displayDiv = document.getElementById('schedule-' + courseId);
            const editDiv = document.getElementById('schedule-edit-' + courseId);
            
            if (displayDiv && editDiv) {
                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';
            }
        });
    });

    // Handle form submissions
    document.querySelectorAll('.schedule-edit-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const courseId = this.getAttribute('data-course-id');
            const formData = new FormData(this);
            formData.append('course_id', courseId);
            
            // Add CSRF token
            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';
            formData.append(csrfName, csrfHash);
            
            fetch('<?= base_url('admin/courses/schedule/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated schedule
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update schedule'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the schedule.');
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
