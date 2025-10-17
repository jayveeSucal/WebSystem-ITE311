<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= ucfirst($role) ?> Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-danger">Logout</a>
    </div>

    <div class="alert alert-primary text-center" role="alert">
        Welcome back, <strong><?= esc($user['name']) ?></strong>! 
        <span class="badge bg-secondary"><?= ucfirst($role) ?></span>
    </div>

    <!-- Alert container for dynamic messages -->
    <div id="alert-container"></div>

    <?php if ($role === 'student'): ?>
    <!-- Student Dashboard - Course Enrollment System -->
    <div class="row">
        <!-- Enrolled Courses Section -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">My Enrolled Courses</h5>
                </div>
                <div class="card-body">
                    <div id="enrolled-courses">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Courses Section: populated either from controller or fallback list -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Available Courses</h5>
                </div>
                <div class="card-body">
                    <div id="available-courses">
                        <div class="text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($role === 'teacher'): ?>
    <!-- Teacher Dashboard -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">My Courses</h5>
                    <p class="card-text">Manage your courses and content</p>
                    <a href="<?= base_url('courses') ?>" class="btn btn-light">View Courses</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Submissions</h5>
                    <p class="card-text">Review student submissions</p>
                    <a href="#" class="btn btn-light">Review</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Create Course</h5>
                    <p class="card-text">Add new course content</p>
                    <a href="<?= base_url('courses/create') ?>" class="btn btn-light">Create</a>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($role === 'admin'): ?>
    <!-- Admin Dashboard -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?= $stats['admin']['usersTotal'] ?? '0' ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <h2><?= $stats['admin']['coursesTotal'] ?? '0' ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Enrollments</h5>
                    <h2>0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">System Status</h5>
                    <span class="badge bg-success">Online</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>User Management</h5>
                </div>
                <div class="card-body">
                    <p>Manage users, roles, and permissions</p>
                    <a href="#" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>System Settings</h5>
                </div>
                <div class="card-body">
                    <p>Configure system settings and preferences</p>
                    <a href="#" class="btn btn-secondary">Settings</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php if ($role === 'student'): ?>
<script>
$(document).ready(function() {
    // Load enrolled courses on page load
    loadEnrolledCourses();
    loadAvailableCourses();

    // Function to show alerts (success/danger) with auto-hide
    function showAlert(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Function to load enrolled courses via GET JSON
    function loadEnrolledCourses() {
        $.get('<?= base_url('course/enrolled') ?>')
            .done(function(response) {
                if (response.success) {
                    displayEnrolledCourses(response.enrollments);
                } else {
                    $('#enrolled-courses').html('<p class="text-muted">Error loading enrolled courses.</p>');
                }
            })
            .fail(function() {
                $('#enrolled-courses').html('<p class="text-muted">Error loading enrolled courses.</p>');
            });
    }

    // Render enrolled courses list items
    function displayEnrolledCourses(enrollments) {
        if (enrollments.length === 0) {
            $('#enrolled-courses').html('<p class="text-muted">You are not enrolled in any courses yet.</p>');
            return;
        }

        let html = '<div class="list-group">';
        enrollments.forEach(function(enrollment) {
            html += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${enrollment.title}</h6>
                        <small>${new Date(enrollment.enrollment_date).toLocaleDateString()}</small>
                    </div>
                    <p class="mb-1">${enrollment.description || 'No description available.'}</p>
                </div>
            `;
        });
        html += '</div>';
        $('#enrolled-courses').html(html);
    }

    // Load available courses from PHP-provided data or fallback seed list
    function loadAvailableCourses() {
        <?php if (isset($available_courses) && !empty($available_courses)): ?>
        const availableCourses = <?= json_encode($available_courses) ?>;
        console.log('Available courses:', availableCourses); // Debug log
        displayAvailableCourses(availableCourses);
        <?php else: ?>
        console.log('No available courses found, using fallback'); // Debug log
        // Fallback courses if database query fails
        const fallbackCourses = [
            { id: 1, title: 'Introduction to Web Development', description: 'Learn the basics of HTML, CSS, and JavaScript.' },
            { id: 2, title: 'PHP and MySQL Fundamentals', description: 'Master server-side programming with PHP and database management.' },
            { id: 3, title: 'CodeIgniter Framework', description: 'Build robust web applications using the CodeIgniter framework.' },
            { id: 4, title: 'Database Design', description: 'Learn to design and optimize database schemas.' },
            { id: 5, title: 'Security Best Practices', description: 'Implement security measures in web applications.' }
        ];
        displayAvailableCourses(fallbackCourses);
        <?php endif; ?>
    }

    // Render available courses with an Enroll button per item
    function displayAvailableCourses(courses) {
        let html = '<div class="list-group">';
        courses.forEach(function(course) {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${course.title}</h6>
                        <p class="mb-1">${course.description}</p>
                    </div>
                    <button class="btn btn-primary btn-sm enroll-btn" data-course-id="${course.id}">
                        Enroll
                    </button>
                </div>
            `;
        });
        html += '</div>';
        $('#available-courses').html(html);
    }

    // Handle enrollment button clicks (AJAX POST)
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const courseId = button.data('course-id');
        const courseTitle = button.closest('.list-group-item').find('h6').text();
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enrolling...');
        
        // Send AJAX request with CSRF token
        const postData = {
            course_id: courseId
        };
        
        // Add CSRF token if available
        <?php if (function_exists('csrf_token') && function_exists('csrf_hash')): ?>
        postData['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
        <?php endif; ?>
        
        $.post('<?= base_url('course/enroll') ?>', postData)
        .done(function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                // Remove the course from available courses with smooth animation
                button.closest('.list-group-item').fadeOut(300, function() {
                    $(this).remove();
                });
                // Reload enrolled courses
                loadEnrolledCourses();
            } else {
                showAlert(response.message || 'Failed to enroll in course.', 'danger');
                // Re-enable button
                button.prop('disabled', false).text('Enroll');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Enrollment error:', error);
            showAlert('Network error. Please check your connection and try again.', 'danger');
            // Re-enable button
            button.prop('disabled', false).text('Enroll');
        });
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
