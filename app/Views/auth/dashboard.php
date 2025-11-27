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
                    <!-- Search Bar for Enrolled Courses -->
                    <div class="mb-3 position-relative">
                        <!-- Autocomplete Suggestions Dropdown (appears above) -->
                        <div id="enrolled-search-suggestions" class="list-group position-absolute bottom-100 start-0 end-0 mb-2 shadow-lg" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000; border-radius: 0.375rem;">
                        </div>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="enrolled-course-search-input" 
                                   placeholder="Search enrolled courses..."
                                   aria-label="Search enrolled courses"
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="enrolled-search-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="clear-enrolled-search-btn" style="display: none;">
                                Clear
                            </button>
                        </div>
                        <div id="enrolled-search-results-info" class="mt-2 small text-muted" style="display: none;"></div>
                    </div>
                    <div id="enrolled-courses">
                        <?php if (isset($enrollments) && !empty($enrollments)): ?>
                            <div class="list-group">
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?= esc($enrollment['title']) ?></h6>
                                            <small><?= esc($enrollment['enrolled_at']) ?></small>
                                        </div>
                                        <p class="mb-1"><?= esc($enrollment['description']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">You are not enrolled in any courses yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Courses Section -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Available Courses</h5>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3 position-relative">
                        <!-- Autocomplete Suggestions Dropdown (appears above) -->
                        <div id="search-suggestions" class="list-group position-absolute bottom-100 start-0 end-0 mb-2 shadow-lg" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000; border-radius: 0.375rem;">
                        </div>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="course-search-input" 
                                   placeholder="Search courses by title or description..."
                                   aria-label="Search courses"
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="search-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="clear-search-btn" style="display: none;">
                                Clear
                            </button>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="server-side-search" checked>
                            <label class="form-check-label text-muted small" for="server-side-search">
                                Use server-side search (comprehensive results)
                            </label>
                        </div>
                        <div id="search-results-info" class="mt-2 small text-muted" style="display: none;"></div>
                    </div>
                    <div id="available-courses">
                        <?php if (isset($available_courses) && !empty($available_courses)): ?>
                            <div class="list-group">
                                <?php foreach ($available_courses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                            <p class="mb-1"><?= esc($course['description']) ?></p>
                                        </div>
                                        <button class="btn btn-primary btn-sm enroll-btn" data-course-id="<?= $course['id'] ?>">
                                            Enroll
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No available courses.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Course Materials</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($materials) && !empty($materials)): ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($material['file_name']) ?></h6>
                                        <small class="text-muted">Uploaded: <?= esc($material['created_at']) ?></small>
                                    </div>
                                    <a href="<?= base_url('courses/download/' . $material['id']) ?>" class="btn btn-sm btn-primary">Download</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No materials available.</p>
                    <?php endif; ?>
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
                    <h5 class="card-title">Upload Materials</h5>
                    <p class="card-text">Upload files to your courses</p>
                    <a href="<?= base_url('courses') ?>" class="btn btn-light">Upload</a>
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
                    <h5>Course Management</h5>
                </div>
                <div class="card-body">
                    <p>Manage courses and upload materials</p>
                    <a href="<?= base_url('courses') ?>" class="btn btn-primary">Manage Courses</a>
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

    <!-- All Courses Section with Search -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Courses</h5>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3 position-relative">
                        <!-- Autocomplete Suggestions Dropdown (appears above) -->
                        <div id="admin-search-suggestions" class="list-group position-absolute bottom-100 start-0 end-0 mb-2 shadow-lg" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000; border-radius: 0.375rem;">
                        </div>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="admin-course-search-input" 
                                   placeholder="Search courses by title or description..."
                                   aria-label="Search courses"
                                   autocomplete="off">
                            <button class="btn btn-outline-primary" type="button" id="admin-search-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg> Search
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="admin-clear-search-btn" style="display: none;">
                                Clear
                            </button>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="admin-server-side-search" checked>
                            <label class="form-check-label" for="admin-server-side-search">
                                Use server-side search (comprehensive results)
                            </label>
                        </div>
                        <div id="admin-search-results-info" class="mt-2 small text-muted" style="display: none;"></div>
                    </div>
                    <div id="admin-courses-container">
                        <?php if (isset($courses) && !empty($courses)): ?>
                            <div class="row">
                                <?php foreach ($courses as $course): ?>
                                    <div class="col-md-6 col-lg-4 mb-4 course-item" 
                                         data-title="<?= esc(strtolower($course['title'])) ?>" 
                                         data-description="<?= esc(strtolower($course['description'])) ?>">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                <p class="card-text"><?= esc($course['description']) ?></p>
                                                <small class="text-muted">
                                                    Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                                </small>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="<?= base_url('courses/edit/' . $course['id']) ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                                    <a href="<?= base_url('courses/upload/' . $course['id']) ?>" class="btn btn-outline-success btn-sm">Upload Material</a>
                                                    <a href="<?= base_url('courses/deleteMaterials/' . $course['id']) ?>"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete all materials for this course?')">Delete Materials</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No courses found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
    /* Autocomplete Suggestions Styling */
    #search-suggestions {
        background-color: #212529;
        border: 1px solid #495057;
    }
    
    #search-suggestions .suggestion-item {
        background-color: #212529;
        color: #fff;
        border-color: #495057;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #search-suggestions .suggestion-item:hover {
        background-color: #495057;
        color: #fff;
    }
    
    #search-suggestions .suggestion-item mark {
        background-color: #ffc107;
        color: #000;
        padding: 0;
        font-weight: bold;
    }
    
    #search-suggestions .suggestion-item .fw-bold {
        color: #fff;
    }
    
    /* Enrolled Courses Search Suggestions Styling */
    #enrolled-search-suggestions {
        background-color: #212529;
        border: 1px solid #495057;
    }
    
    #enrolled-search-suggestions .suggestion-item {
        background-color: #212529;
        color: #fff;
        border-color: #495057;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #enrolled-search-suggestions .suggestion-item:hover {
        background-color: #495057;
        color: #fff;
    }
    
    #enrolled-search-suggestions .suggestion-item mark {
        background-color: #ffc107;
        color: #000;
        padding: 0;
        font-weight: bold;
    }
    
    #enrolled-search-suggestions .suggestion-item .fw-bold {
        color: #fff;
    }
</style>

<?php if ($role === 'student'): ?>
<script>
$(document).ready(function() {
    // Store original enrolled courses for search
    let originalEnrolledCourses = [];
    
    // Store original available courses for client-side filtering
    let originalAvailableCourses = [];
    let currentAvailableCourses = [];
    
    // Initialize original enrolled courses from PHP if available
    <?php if (isset($enrollments) && !empty($enrollments)): ?>
    originalEnrolledCourses = <?= json_encode($enrollments) ?>;
    <?php endif; ?>
    
    // Initialize original available courses from PHP if available
    <?php if (isset($available_courses) && !empty($available_courses)): ?>
    originalAvailableCourses = <?= json_encode($available_courses) ?>;
    currentAvailableCourses = <?= json_encode($available_courses) ?>;
    <?php endif; ?>
    
    // Load enrolled courses on page load (will update if different from PHP data)
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
                    originalEnrolledCourses = response.enrollments;
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
                        <h6 class="mb-1">${escapeHtml(enrollment.title)}</h6>
                        <small>${new Date(enrollment.enrolled_at).toLocaleDateString()}</small>
                    </div>
                    <p class="mb-1">${escapeHtml(enrollment.description || 'No description available.')}</p>
                </div>
            `;
        });
        html += '</div>';
        $('#enrolled-courses').html(html);
    }

    // Client-side search function for enrolled courses
    function performEnrolledClientSideSearch(query, showAll = false) {
        if (!query || query.trim() === '') {
            if (showAll) {
                displayEnrolledCourses(originalEnrolledCourses);
            }
            $('#enrolled-search-results-info').hide();
            $('#clear-enrolled-search-btn').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const filtered = originalEnrolledCourses.filter(function(enrollment) {
            const title = (enrollment.title || '').toLowerCase();
            const description = (enrollment.description || '').toLowerCase();
            return title.includes(searchTerm) || description.includes(searchTerm);
        });

        displayEnrolledCourses(filtered);
        if (filtered.length > 0) {
            $('#enrolled-search-results-info').text(`Found ${filtered.length} enrolled course(s) matching "${query}"`).show();
        } else {
            $('#enrolled-search-results-info').text(`No enrolled courses found matching "${query}"`).show();
        }
        $('#clear-enrolled-search-btn').show();
    }

    // Generate autocomplete suggestions for enrolled courses
    function generateEnrolledSuggestions(query) {
        if (!query || query.trim() === '' || originalEnrolledCourses.length === 0) {
            $('#enrolled-search-suggestions').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const suggestions = [];
        const maxSuggestions = 5;

        // Get matching enrolled courses - match from the first letter
        originalEnrolledCourses.forEach(function(enrollment) {
            const title = (enrollment.title || '').toLowerCase();
            const description = (enrollment.description || '').toLowerCase();
            
            // Check if title or description starts with or contains the search term
            if (title.startsWith(searchTerm) || title.includes(searchTerm) || 
                description.startsWith(searchTerm) || description.includes(searchTerm)) {
                // Highlight the matching part
                const highlightedTitle = highlightMatch(enrollment.title, searchTerm);
                suggestions.push({
                    title: enrollment.title,
                    description: enrollment.description,
                    highlightedTitle: highlightedTitle
                });
            }
        });

        // Limit suggestions
        const limitedSuggestions = suggestions.slice(0, maxSuggestions);

        if (limitedSuggestions.length > 0) {
            let html = '';
            limitedSuggestions.forEach(function(suggestion, index) {
                html += `
                    <button type="button" 
                            class="list-group-item list-group-item-action suggestion-item" 
                            data-index="${index}"
                            data-title="${escapeHtml(suggestion.title)}">
                        <div class="fw-bold">${suggestion.highlightedTitle}</div>
                        <small class="text-muted">${escapeHtml(suggestion.description || 'No description')}</small>
                    </button>
                `;
            });
            $('#enrolled-search-suggestions').html(html).show();
        } else {
            $('#enrolled-search-suggestions').hide();
        }
    }

    // Load available courses from PHP-provided data or fallback seed list
    function loadAvailableCourses() {
        <?php if (isset($available_courses) && !empty($available_courses)): ?>
        const availableCourses = <?= json_encode($available_courses) ?>;
        console.log('Available courses:', availableCourses); // Debug log
        // Update the global variables if not already set
        if (originalAvailableCourses.length === 0) {
            originalAvailableCourses = availableCourses;
            currentAvailableCourses = availableCourses;
        }
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
        // Update the global variables if not already set
        if (originalAvailableCourses.length === 0) {
            originalAvailableCourses = fallbackCourses;
            currentAvailableCourses = fallbackCourses;
        }
        displayAvailableCourses(fallbackCourses);
        <?php endif; ?>
    }

    // Render available courses with an Enroll button per item
    function displayAvailableCourses(courses) {
        if (courses.length === 0) {
            $('#available-courses').html('<p class="text-muted">No courses found.</p>');
            return;
        }
        
        let html = '<div class="list-group">';
        courses.forEach(function(course) {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${escapeHtml(course.title)}</h6>
                        <p class="mb-1">${escapeHtml(course.description)}</p>
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

    // Client-side search function for available courses
    function performClientSideSearch(query, showAll = false) {
        if (!query || query.trim() === '') {
            if (showAll) {
                displayAvailableCourses(originalAvailableCourses);
            }
            $('#search-results-info').hide();
            $('#clear-search-btn').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const filtered = originalAvailableCourses.filter(function(course) {
            const title = (course.title || '').toLowerCase();
            const description = (course.description || '').toLowerCase();
            return title.includes(searchTerm) || description.includes(searchTerm);
        });

        displayAvailableCourses(filtered);
        if (filtered.length > 0) {
            $('#search-results-info').text(`Found ${filtered.length} course(s) matching "${query}"`).show();
        } else {
            $('#search-results-info').text(`No courses found matching "${query}"`).show();
        }
        $('#clear-search-btn').show();
    }

    // Generate autocomplete suggestions
    function generateSuggestions(query) {
        if (!query || query.trim() === '' || originalAvailableCourses.length === 0) {
            $('#search-suggestions').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const suggestions = [];
        const maxSuggestions = 5;

        // Get matching courses - match from the first letter
        originalAvailableCourses.forEach(function(course) {
            const title = (course.title || '').toLowerCase();
            const description = (course.description || '').toLowerCase();
            
            // Check if title or description starts with or contains the search term
            if (title.startsWith(searchTerm) || title.includes(searchTerm) || 
                description.startsWith(searchTerm) || description.includes(searchTerm)) {
                // Highlight the matching part
                const highlightedTitle = highlightMatch(course.title, searchTerm);
                suggestions.push({
                    title: course.title,
                    description: course.description,
                    highlightedTitle: highlightedTitle,
                    fullText: course.title + ' ' + course.description
                });
            }
        });

        // Limit suggestions
        const limitedSuggestions = suggestions.slice(0, maxSuggestions);

        if (limitedSuggestions.length > 0) {
            let html = '';
            limitedSuggestions.forEach(function(suggestion, index) {
                html += `
                    <button type="button" 
                            class="list-group-item list-group-item-action suggestion-item" 
                            data-index="${index}"
                            data-title="${escapeHtml(suggestion.title)}">
                        <div class="fw-bold">${suggestion.highlightedTitle}</div>
                        <small class="text-muted">${escapeHtml(suggestion.description || 'No description')}</small>
                    </button>
                `;
            });
            $('#search-suggestions').html(html).show();
        } else {
            $('#search-suggestions').hide();
        }
    }

    // Highlight matching text
    function highlightMatch(text, searchTerm) {
        if (!searchTerm) return escapeHtml(text);
        const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
        return escapeHtml(text).replace(regex, '<mark>$1</mark>');
    }

    // Escape regex special characters
    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Server-side search function (AJAX) for available courses
    function performServerSideSearch(query, showAll = false) {
        const searchUrl = '<?= base_url('course/getAvailableCourses') ?>';
        
        // If empty query and not showing all, just hide info
        if ((!query || query.trim() === '') && !showAll) {
            $('#search-results-info').hide();
            $('#clear-search-btn').hide();
            return;
        }
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            beforeSend: function() {
                $('#search-results-info').html('<span class="spinner-border spinner-border-sm me-2"></span>Searching...').show();
            },
            success: function(response) {
                if (response.success) {
                    currentAvailableCourses = response.courses;
                    displayAvailableCourses(response.courses);
                    if (query && query.trim() !== '') {
                        if (response.count > 0) {
                            $('#search-results-info').text(`Found ${response.count} course(s) matching "${query}"`).show();
                        } else {
                            $('#search-results-info').text(`No courses found matching "${query}"`).show();
                        }
                        $('#clear-search-btn').show();
                    } else {
                        $('#search-results-info').hide();
                        $('#clear-search-btn').hide();
                    }
                } else {
                    $('#search-results-info').text('Error: ' + (response.message || 'Search failed')).show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                $('#search-results-info').text('Error performing search. Please try again.').show();
            }
        });
    }

    // Search button click handler - filters to show only matching results
    $('#search-btn').on('click', function() {
        const query = $('#course-search-input').val().trim();
        const useServerSide = $('#server-side-search').is(':checked');
        
        // Hide suggestions when searching
        $('#search-suggestions').hide();
        
        // Perform actual search/filter
        if (useServerSide) {
            performServerSideSearch(query, true);
        } else {
            performClientSideSearch(query, true);
        }
    });

    // Enter key handler
    $('#course-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#search-btn').click();
        }
    });

    // Real-time suggestions as user types (shows suggestions, doesn't filter results)
    let suggestionTimeout;
    $('#course-search-input').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(suggestionTimeout);
        
        // Show suggestions as user types (from first letter - immediately)
        if (query.length >= 1) {
            suggestionTimeout = setTimeout(function() {
                generateSuggestions(query);
            }, 150); // Reduced debounce for faster response
        } else {
            $('#search-suggestions').hide();
        }
    });

    // Handle suggestion click
    $(document).on('click', '#search-suggestions .suggestion-item', function() {
        const title = $(this).data('title');
        $('#course-search-input').val(title);
        $('#search-suggestions').hide();
        // Automatically trigger search when suggestion is clicked
        $('#search-btn').click();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#course-search-input, #search-suggestions').length) {
            $('#search-suggestions').hide();
        }
    });

    // Clear search button
    $('#clear-search-btn').on('click', function() {
        $('#course-search-input').val('');
        $('#search-suggestions').hide();
        const useServerSide = $('#server-side-search').is(':checked');
        
        // Show all courses when clearing
        if (useServerSide) {
            performServerSideSearch('', true);
        } else {
            performClientSideSearch('', true);
        }
    });

    // Toggle between client-side and server-side search
    $('#server-side-search').on('change', function() {
        const query = $('#course-search-input').val();
        if ($(this).is(':checked')) {
            performServerSideSearch(query);
        } else {
            performClientSideSearch(query);
        }
    });

    // ========== ENROLLED COURSES SEARCH FUNCTIONALITY ==========
    
    // Enrolled courses search button click handler
    $('#enrolled-search-btn').on('click', function() {
        const query = $('#enrolled-course-search-input').val().trim();
        
        // Hide suggestions when searching
        $('#enrolled-search-suggestions').hide();
        
        // Perform search/filter
        performEnrolledClientSideSearch(query, true);
    });

    // Enter key handler for enrolled courses search
    $('#enrolled-course-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#enrolled-search-btn').click();
        }
    });

    // Real-time suggestions as user types for enrolled courses
    let enrolledSuggestionTimeout;
    $('#enrolled-course-search-input').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(enrolledSuggestionTimeout);
        
        // Show suggestions as user types (from first letter - immediately)
        if (query.length >= 1) {
            enrolledSuggestionTimeout = setTimeout(function() {
                generateEnrolledSuggestions(query);
            }, 150); // Reduced debounce for faster response
        } else {
            $('#enrolled-search-suggestions').hide();
        }
    });

    // Handle enrolled courses suggestion click
    $(document).on('click', '#enrolled-search-suggestions .suggestion-item', function() {
        const title = $(this).data('title');
        $('#enrolled-course-search-input').val(title);
        $('#enrolled-search-suggestions').hide();
        // Automatically trigger search when suggestion is clicked
        $('#enrolled-search-btn').click();
    });

    // Hide enrolled suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#enrolled-course-search-input, #enrolled-search-suggestions').length) {
            $('#enrolled-search-suggestions').hide();
        }
    });

    // Clear enrolled courses search button
    $('#clear-enrolled-search-btn').on('click', function() {
        $('#enrolled-course-search-input').val('');
        $('#enrolled-search-suggestions').hide();
        
        // Show all enrolled courses when clearing
        performEnrolledClientSideSearch('', true);
    });

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
                // Redirect to dashboard to show updated enrolled courses
                window.location.href = '<?= base_url('dashboard') ?>';
            } else {
                showAlert(response.message || 'Failed to enroll in course.', 'danger');
                // Re-enable button
                button.prop('disabled', false).text('Enroll');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Enrollment error:', xhr.status, error);
            let message = 'Network error. Please check your connection and try again.';
            if (xhr.status === 401) {
                message = 'You must be logged in to enroll in courses.';
            } else if (xhr.status === 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    message = response.message || 'Invalid request.';
                } catch (e) {
                    message = 'Invalid course ID or request.';
                }
            } else if (xhr.status === 404) {
                message = 'Course not found.';
            } else if (xhr.status === 500) {
                message = 'Server error. Please try again later.';
            } else if (xhr.status === 0) {
                message = 'Network error. Please check your connection and try again.';
            }
            showAlert(message, 'danger');
            // Re-enable button
            button.prop('disabled', false).text('Enroll');
        });
    });
});
</script>
<?php endif; ?>

<?php if ($role === 'admin'): ?>
<style>
    /* Admin Search Suggestions Styling */
    #admin-search-suggestions {
        background-color: #212529;
        border: 1px solid #495057;
    }
    
    #admin-search-suggestions .suggestion-item {
        background-color: #212529;
        color: #fff;
        border-color: #495057;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #admin-search-suggestions .suggestion-item:hover {
        background-color: #495057;
        color: #fff;
    }
    
    #admin-search-suggestions .suggestion-item mark {
        background-color: #ffc107;
        color: #000;
        padding: 0;
        font-weight: bold;
    }
    
    #admin-search-suggestions .suggestion-item .fw-bold {
        color: #fff;
    }
</style>

<script>
$(document).ready(function() {
    // Store original courses data for client-side filtering
    const originalAdminCourses = <?= json_encode($courses ?? []) ?>;
    let currentAdminCourses = originalAdminCourses;

    // Client-side search function
    function performAdminClientSideSearch(query, showAll = false) {
        if (!query || query.trim() === '') {
            if (showAll) {
                renderAdminCourses(originalAdminCourses);
            }
            $('#admin-search-results-info').hide();
            $('#admin-clear-search-btn').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const filtered = originalAdminCourses.filter(function(course) {
            const title = (course.title || '').toLowerCase();
            const description = (course.description || '').toLowerCase();
            return title.includes(searchTerm) || description.includes(searchTerm);
        });

        renderAdminCourses(filtered);
        if (filtered.length > 0) {
            $('#admin-search-results-info').text(`Found ${filtered.length} course(s) matching "${query}"`).show();
        } else {
            $('#admin-search-results-info').text(`No courses found matching "${query}"`).show();
        }
        $('#admin-clear-search-btn').show();
    }

    // Generate autocomplete suggestions
    function generateAdminSuggestions(query) {
        if (!query || query.trim() === '' || originalAdminCourses.length === 0) {
            $('#admin-search-suggestions').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const suggestions = [];
        const maxSuggestions = 5;

        // Get matching courses - match from the first letter
        originalAdminCourses.forEach(function(course) {
            const title = (course.title || '').toLowerCase();
            const description = (course.description || '').toLowerCase();
            
            // Check if title or description starts with or contains the search term
            if (title.startsWith(searchTerm) || title.includes(searchTerm) || 
                description.startsWith(searchTerm) || description.includes(searchTerm)) {
                // Highlight the matching part
                const highlightedTitle = highlightAdminMatch(course.title, searchTerm);
                suggestions.push({
                    title: course.title,
                    description: course.description,
                    highlightedTitle: highlightedTitle
                });
            }
        });

        // Limit suggestions
        const limitedSuggestions = suggestions.slice(0, maxSuggestions);

        if (limitedSuggestions.length > 0) {
            let html = '';
            limitedSuggestions.forEach(function(suggestion, index) {
                html += `
                    <button type="button" 
                            class="list-group-item list-group-item-action suggestion-item" 
                            data-index="${index}"
                            data-title="${escapeAdminHtml(suggestion.title)}">
                        <div class="fw-bold">${suggestion.highlightedTitle}</div>
                        <small class="text-muted">${escapeAdminHtml(suggestion.description || 'No description')}</small>
                    </button>
                `;
            });
            $('#admin-search-suggestions').html(html).show();
        } else {
            $('#admin-search-suggestions').hide();
        }
    }

    // Highlight matching text
    function highlightAdminMatch(text, searchTerm) {
        if (!searchTerm) return escapeAdminHtml(text);
        const regex = new RegExp(`(${escapeAdminRegex(searchTerm)})`, 'gi');
        return escapeAdminHtml(text).replace(regex, '<mark>$1</mark>');
    }

    // Escape regex special characters
    function escapeAdminRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Escape HTML
    function escapeAdminHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Server-side search function (AJAX)
    function performAdminServerSideSearch(query, showAll = false) {
        const searchUrl = '<?= base_url('course/search') ?>';
        
        // If empty query and not showing all, just hide info
        if ((!query || query.trim() === '') && !showAll) {
            $('#admin-search-results-info').hide();
            $('#admin-clear-search-btn').hide();
            return;
        }
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            beforeSend: function() {
                $('#admin-search-results-info').html('<span class="spinner-border spinner-border-sm me-2"></span>Searching...').show();
            },
            success: function(response) {
                if (response.success) {
                    currentAdminCourses = response.courses;
                    renderAdminCourses(response.courses);
                    if (query && query.trim() !== '') {
                        if (response.count > 0) {
                            $('#admin-search-results-info').text(`Found ${response.count} course(s) matching "${query}"`).show();
                        } else {
                            $('#admin-search-results-info').text(`No courses found matching "${query}"`).show();
                        }
                        $('#admin-clear-search-btn').show();
                    } else {
                        $('#admin-search-results-info').hide();
                        $('#admin-clear-search-btn').hide();
                    }
                } else {
                    $('#admin-search-results-info').text('Error: ' + (response.message || 'Search failed')).show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', xhr, status, error);
                let errorMessage = 'Error performing search. Please try again.';
                
                // Try to get more detailed error message
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'Search endpoint not found. Please check the server configuration.';
                } else if (xhr.status === 401) {
                    errorMessage = 'You must be logged in to search courses.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
                
                $('#admin-search-results-info').text(errorMessage).show();
            }
        });
    }

    // Render courses to the page
    function renderAdminCourses(courses) {
        const container = $('#admin-courses-container');
        
        if (courses.length === 0) {
            container.html(`
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">No Courses Found</h5>
                                <p class="card-text">No courses match your search criteria.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            return;
        }

        let html = '<div class="row">';
        courses.forEach(function(course) {
            const createdDate = new Date(course.created_at).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            html += `
                <div class="col-md-6 col-lg-4 mb-4 course-item">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">${escapeAdminHtml(course.title)}</h5>
                            <p class="card-text">${escapeAdminHtml(course.description)}</p>
                            <small class="text-muted">Created: ${createdDate}</small>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100" role="group">
                                <a href="<?= base_url('courses/edit/') ?>${course.id}" class="btn btn-outline-primary btn-sm">Edit</a>
                                <a href="<?= base_url('courses/upload/') ?>${course.id}" class="btn btn-outline-success btn-sm">Upload Material</a>
                                <a href="<?= base_url('courses/deleteMaterials/') ?>${course.id}"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete all materials for this course?')">Delete Materials</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.html(html);
    }

    // Search button click handler - filters to show only matching results
    $('#admin-search-btn').on('click', function() {
        const query = $('#admin-course-search-input').val().trim();
        const useServerSide = $('#admin-server-side-search').is(':checked');
        
        // Hide suggestions when searching
        $('#admin-search-suggestions').hide();
        
        // Perform actual search/filter
        if (useServerSide) {
            performAdminServerSideSearch(query, true);
        } else {
            performAdminClientSideSearch(query, true);
        }
    });

    // Enter key handler
    $('#admin-course-search-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#admin-search-btn').click();
        }
    });

    // Real-time suggestions as user types (shows suggestions, doesn't filter results)
    let adminSuggestionTimeout;
    $('#admin-course-search-input').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(adminSuggestionTimeout);
        
        // Show suggestions as user types (from first letter - immediately)
        if (query.length >= 1) {
            adminSuggestionTimeout = setTimeout(function() {
                generateAdminSuggestions(query);
            }, 150); // Reduced debounce for faster response
        } else {
            $('#admin-search-suggestions').hide();
        }
    });

    // Handle suggestion click
    $(document).on('click', '#admin-search-suggestions .suggestion-item', function() {
        const title = $(this).data('title');
        $('#admin-course-search-input').val(title);
        $('#admin-search-suggestions').hide();
        // Automatically trigger search when suggestion is clicked
        $('#admin-search-btn').click();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#admin-course-search-input, #admin-search-suggestions').length) {
            $('#admin-search-suggestions').hide();
        }
    });

    // Clear search button
    $('#admin-clear-search-btn').on('click', function() {
        $('#admin-course-search-input').val('');
        $('#admin-search-suggestions').hide();
        const useServerSide = $('#admin-server-side-search').is(':checked');
        
        // Show all courses when clearing
        if (useServerSide) {
            performAdminServerSideSearch('', true);
        } else {
            performAdminClientSideSearch('', true);
        }
    });

    // Toggle between client-side and server-side search
    $('#admin-server-side-search').on('change', function() {
        const query = $('#admin-course-search-input').val();
        if ($(this).is(':checked')) {
            performAdminServerSideSearch(query);
        } else {
            performAdminClientSideSearch(query);
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
