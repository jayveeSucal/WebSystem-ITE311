<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Course Management</h1>
        <div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Internal actions toolbar -->
    <div class="mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= base_url('courses/create') ?>" class="btn btn-primary">Create Course</a>
            <a href="<?= base_url('admin/courses/schedule') ?>" class="btn btn-outline-primary">Course Schedule</a>
        </div>
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

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="position-relative">
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
                            <button class="btn btn-outline-primary" type="button" id="search-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg> Search
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="clear-search-btn" style="display: none;">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-2 mt-md-0">
                        <input class="form-check-input" type="checkbox" id="server-side-search" checked>
                        <label class="form-check-label" for="server-side-search">
                            Server-side search
                        </label>
                    </div>
                </div>
            </div>
            <div id="search-results-info" class="mt-2 small text-muted" style="display: none;"></div>
        </div>
    </div>

    <div class="row" id="courses-container">
        <?php if (empty($courses)): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">No Courses Found</h5>
                        <p class="card-text">You haven't created any courses yet.</p>
                        <a href="<?= base_url('courses/create') ?>" class="btn btn-primary">Create Your First Course</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</div>

<style>
    /* Autocomplete Suggestions Styling */
    #search-suggestions {
        background-color: #fff;
        border: 1px solid #dee2e6;
    }
    
    #search-suggestions .suggestion-item {
        background-color: #fff;
        color: #212529;
        border-color: #dee2e6;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #search-suggestions .suggestion-item:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    #search-suggestions .suggestion-item mark {
        background-color: #ffc107;
        color: #000;
        padding: 0;
        font-weight: bold;
    }
    
    #search-suggestions .suggestion-item .fw-bold {
        color: #212529;
    }
</style>

<script>
$(document).ready(function() {
    // Store original courses data for client-side filtering
    const originalCourses = <?= json_encode($courses ?? []) ?>;
    let currentCourses = originalCourses;

    // Client-side search function
    function performClientSideSearch(query, showAll = false) {
        if (!query || query.trim() === '') {
            if (showAll) {
                renderCourses(originalCourses);
            }
            $('#search-results-info').hide();
            $('#clear-search-btn').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const filtered = originalCourses.filter(function(course) {
            const title = (course.title || '').toLowerCase();
            const description = (course.description || '').toLowerCase();
            return title.includes(searchTerm) || description.includes(searchTerm);
        });

        renderCourses(filtered);
        if (filtered.length > 0) {
            $('#search-results-info').text(`Found ${filtered.length} course(s) matching "${query}"`).show();
        } else {
            $('#search-results-info').text(`No courses found matching "${query}"`).show();
        }
        $('#clear-search-btn').show();
    }

    // Generate autocomplete suggestions
    function generateSuggestions(query) {
        if (!query || query.trim() === '' || originalCourses.length === 0) {
            $('#search-suggestions').hide();
            return;
        }

        const searchTerm = query.toLowerCase().trim();
        const suggestions = [];
        const maxSuggestions = 5;

        // Get matching courses - match from the first letter
        originalCourses.forEach(function(course) {
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

    // Server-side search function (AJAX)
    function performServerSideSearch(query, showAll = false) {
        const searchUrl = '<?= base_url('course/search') ?>';
        
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
                    currentCourses = response.courses;
                    renderCourses(response.courses);
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

    // Render courses to the page
    function renderCourses(courses) {
        const container = $('#courses-container');
        
        if (courses.length === 0) {
            container.html(`
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">No Courses Found</h5>
                            <p class="card-text">No courses match your search criteria.</p>
                        </div>
                    </div>
                </div>
            `);
            return;
        }

        let html = '';
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
                            <h5 class="card-title">${escapeHtml(course.title)}</h5>
                            <p class="card-text">${escapeHtml(course.description)}</p>
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
        
        container.html(html);
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
    $(document).on('click', '.suggestion-item', function() {
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
});
</script>
<?= $this->endSection() ?>
