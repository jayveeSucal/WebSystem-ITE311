<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Teacher Assignments</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Assignment Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Quick Assign Teacher to Course</h5>
        </div>
        <div class="card-body">
            <form id="quickAssignForm" method="POST" action="<?= base_url('admin/teacher-assignments/quick-assign') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="quick_course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_course_id" name="course_id" required>
                            <option value="">-- Select Course --</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course['id'] ?>" 
                                            data-cn="<?= esc($course['course_number'] ?? '') ?>"
                                            data-time="<?= esc($course['schedule_time'] ?? '') ?>">
                                        <?= esc($course['title']) ?> 
                                        <?php if (!empty($course['course_number'])): ?>
                                            (<?= esc($course['course_number']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quick_teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_teacher_id" name="teacher_id" required>
                            <option value="">-- Select Teacher --</option>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" data-teacher-id="<?= $teacher['id'] ?>">
                                        <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted" id="teacher-conflict-warning" style="display: none; color: red !important;"></small>
                    </div>
                    <div class="col-md-2">
                        <label for="quick_cn" class="form-label">CN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_cn" name="course_number" 
                               placeholder="e.g., CS101" required>
                    </div>
                    <div class="col-md-2">
                        <label for="quick_time" class="form-label">Schedule Time <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_time" name="schedule_time" required>
                            <option value="">-- Select Time --</option>
                            <option value="07:00-08:00">7:00 AM - 8:00 AM</option>
                            <option value="08:00-09:00">8:00 AM - 9:00 AM</option>
                            <option value="09:00-10:00">9:00 AM - 10:00 AM</option>
                            <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                            <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                            <option value="12:00-13:00">12:00 PM - 1:00 PM</option>
                            <option value="13:00-14:00">1:00 PM - 2:00 PM</option>
                            <option value="14:00-15:00">2:00 PM - 3:00 PM</option>
                            <option value="15:00-16:00">3:00 PM - 4:00 PM</option>
                            <option value="16:00-17:00">4:00 PM - 5:00 PM</option>
                            <option value="17:00-18:00">5:00 PM - 6:00 PM</option>
                            <option value="18:00-19:00">6:00 PM - 7:00 PM</option>
                            <option value="19:00-20:00">7:00 PM - 8:00 PM</option>
                            <option value="20:00-21:00">8:00 PM - 9:00 PM</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100 d-block">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Assign Teachers to Courses</h5>
        </div>
        <div class="card-body">
            <?php if (empty($teachers)): ?>
                <div class="alert alert-warning">
                    <strong>No teachers found!</strong> Please create teacher accounts first. Teachers should have role 'teacher' or 'instructor' in the users table.
                </div>
            <?php endif; ?>
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>CN</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Term</th>
                                <th>Units</th>
                                <th>Time</th>
                                <th>Current Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= esc($course['title']) ?></td>
                                    <td><?= esc($course['course_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['academic_year'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['term'] ?? 'N/A') ?></td>
                                    <td><?= isset($course['units']) && $course['units'] !== null ? esc($course['units']) : 'N/A' ?></td>
                                    <td>
                                        <?php if (!empty($course['schedule_time'])): ?>
                                            <?php 
                                            // Check if it's a time range format (HH:MM-HH:MM) or single time
                                            $timeValue = $course['schedule_time'];
                                            if (strpos($timeValue, '-') !== false) {
                                                // Time range format
                                                list($start, $end) = explode('-', $timeValue);
                                                $startFormatted = date('g:i A', strtotime($start));
                                                $endFormatted = date('g:i A', strtotime($end));
                                                echo esc($startFormatted . ' - ' . $endFormatted);
                                            } else {
                                                // Single time format (legacy)
                                                echo date('h:i A', strtotime($timeValue));
                                            }
                                            ?>
                                            <?php if (!empty($course['schedule_date'])): ?>
                                                <br><small class="text-muted"><?= date('M d, Y', strtotime($course['schedule_date'])) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not scheduled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['teacher_name']) && (!empty($course['teacher_role']) && in_array($course['teacher_role'], ['teacher', 'instructor']))): ?>
                                            <strong><?= esc($course['teacher_name']) ?></strong><br>
                                            <small class="text-muted"><?= esc($course['teacher_email']) ?></small>
                                        <?php elseif (!empty($course['teacher_name']) && (!empty($course['teacher_role']) && $course['teacher_role'] === 'admin')): ?>
                                            <span class="text-warning">Admin (needs teacher assignment)</span>
                                        <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm teacher-select" 
                                                data-course-id="<?= $course['id'] ?>"
                                                data-schedule-time="<?= esc($course['schedule_time'] ?? '') ?>"
                                                data-schedule-date="<?= esc($course['schedule_date'] ?? '') ?>"
                                                style="min-width: 200px;">
                                            <option value="">-- Select Teacher --</option>
                                            <?php 
                                            // Get conflicting teacher IDs for this course
                                            $conflictingTeacherIds = $courseTeacherConflicts[$course['id']] ?? [];
                                            
                                            foreach ($teachers as $teacher):
                                                $teacherId = $teacher['id'];
                                                $hasConflict = in_array($teacherId, $conflictingTeacherIds);
                                                
                                                // Show teacher if no conflict, or if already assigned to this course
                                                $isCurrentTeacher = ($course['user_id'] == $teacherId && (!empty($course['teacher_role']) && in_array($course['teacher_role'], ['teacher', 'instructor'])));
                                                
                                                // Only show teachers without conflicts (or current teacher)
                                                if (!$hasConflict || $isCurrentTeacher):
                                            ?>
                                                <option value="<?= $teacher['id'] ?>" 
                                                        <?= $isCurrentTeacher ? 'selected' : '' ?>>
                                                    <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                                </option>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                            <?php if (!empty($course['teacher_role']) && $course['teacher_role'] === 'admin'): ?>
                                                <option value="" selected>-- Unassign Admin --</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No courses found. Create courses first to assign teachers.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill CN and Time when course is selected in quick assign form
    // Also filter teachers based on schedule conflicts
    const quickCourseSelect = document.getElementById('quick_course_id');
    const quickCnInput = document.getElementById('quick_cn');
    const quickTimeInput = document.getElementById('quick_time');
    const quickTeacherSelect = document.getElementById('quick_teacher_id');
    const teacherConflictWarning = document.getElementById('teacher-conflict-warning');
    const allTeachers = <?= json_encode($teachers) ?>;
    const courseConflicts = <?= json_encode($courseTeacherConflicts) ?>;
    
    function filterTeachersForSchedule(courseId, scheduleTime, scheduleDate) {
        if (!quickTeacherSelect) {
            return;
        }
        
        // If no schedule time, show all teachers
        if (!scheduleTime) {
            quickTeacherSelect.innerHTML = '<option value="">-- Select Teacher --</option>';
            allTeachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.name + ' (' + teacher.email + ')';
                quickTeacherSelect.appendChild(option);
            });
            teacherConflictWarning.style.display = 'none';
            return;
        }
        
        // Fetch available teachers from API
        const params = new URLSearchParams({
            schedule_time: scheduleTime,
            course_id: courseId || ''
        });
        if (scheduleDate) {
            params.append('schedule_date', scheduleDate);
        }
        
        fetch('<?= base_url('admin/teacher-assignments/available-teachers') ?>?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentValue = quickTeacherSelect.value;
                    
                    // Clear and rebuild options
                    quickTeacherSelect.innerHTML = '<option value="">-- Select Teacher --</option>';
                    
                    data.teachers.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.name + ' (' + teacher.email + ')';
                        quickTeacherSelect.appendChild(option);
                    });
                    
                    // Restore previous selection if still available
                    if (currentValue && quickTeacherSelect.querySelector(`option[value="${currentValue}"]`)) {
                        quickTeacherSelect.value = currentValue;
                    }
                    
                    // Show warning if no teachers available
                    if (data.teachers.length === 0) {
                        teacherConflictWarning.textContent = 'Walang available teachers para sa schedule na ito (lahat ay may conflict).';
                        teacherConflictWarning.style.display = 'block';
                    } else {
                        teacherConflictWarning.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching available teachers:', error);
            });
    }
    
    if (quickCourseSelect && quickCnInput && quickTimeInput) {
        quickCourseSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const courseId = selectedOption.value;
                const cn = selectedOption.getAttribute('data-cn');
                const time = selectedOption.getAttribute('data-time');
                
                if (cn) {
                    quickCnInput.value = cn;
                }
                if (time) {
                    // Check if it's a time range format (HH:MM-HH:MM) or single time
                    if (time.includes('-')) {
                        // Time range format - set the select value
                        quickTimeInput.value = time;
                    } else {
                        // Single time format (legacy) - try to match with closest range
                        const timeParts = time.split(':');
                        if (timeParts.length >= 2) {
                            const hour = parseInt(timeParts[0]);
                            const minute = parseInt(timeParts[1]);
                            // Find closest time range option
                            const timeStr = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                            // Try to find matching option or closest
                            for (let option of quickTimeInput.options) {
                                if (option.value && option.value.startsWith(timeStr)) {
                                    quickTimeInput.value = option.value;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                // Filter teachers based on course schedule
                const courseData = <?= json_encode(array_combine(array_column($courses, 'id'), $courses)) ?>;
                const course = courseData[courseId];
                if (course) {
                    filterTeachersForSchedule(courseId, course.schedule_time || time, course.schedule_date || null);
                }
            } else {
                // Clear fields if no course selected
                quickCnInput.value = '';
                quickTimeInput.value = '';
                // Reset teacher dropdown
                if (quickTeacherSelect) {
                    quickTeacherSelect.innerHTML = '<option value="">-- Select Teacher --</option>';
                    allTeachers.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.name + ' (' + teacher.email + ')';
                        quickTeacherSelect.appendChild(option);
                    });
                }
                teacherConflictWarning.style.display = 'none';
            }
        });
        
        // Also filter when schedule time changes
        if (quickTimeInput) {
            quickTimeInput.addEventListener('change', function() {
                const courseId = quickCourseSelect.value;
                const scheduleTime = this.value;
                if (courseId && scheduleTime) {
                    const courseData = <?= json_encode(array_combine(array_column($courses, 'id'), $courses)) ?>;
                    const course = courseData[courseId];
                    if (course) {
                        filterTeachersForSchedule(courseId, scheduleTime, course.schedule_date || null);
                    } else {
                        // If course not found in data, filter based on selected time
                        filterTeachersForSchedule(courseId, scheduleTime, null);
                    }
                }
            });
        }
    }

    document.querySelectorAll('.teacher-select').forEach(select => {
        select.addEventListener('change', function() {
            const courseId = this.getAttribute('data-course-id');
            const teacherId = this.value;
            
            const formData = new FormData();
            formData.append('course_id', courseId);
            formData.append('teacher_id', teacherId);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            fetch('<?= base_url('admin/teacher-assignments/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
                    
                    // Reload after 1 second
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to update assignment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the assignment.');
            });
        });
    });
});
</script>
<?= $this->endSection() ?>

