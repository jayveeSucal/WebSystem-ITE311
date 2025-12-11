<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-3">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">Site</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a href="<?= site_url('/') ?>" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="<?= site_url('/about') ?>" class="nav-link">About</a></li>
                <li class="nav-item"><a href="<?= site_url('/contact') ?>" class="nav-link">Contact</a></li>
                <?php $role = session('userRole'); ?>
                <?php if (session('isLoggedIn')): ?>
                    <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">Dashboard</a></li>
                    <?php if ($role === 'admin'): ?>
                                                                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><h6 class="dropdown-header">Academic Management</h6></li>
                                                                                                                                                                <li><a class="dropdown-item" href="<?= site_url('/admin/departments') ?>">Departments</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/programs') ?>">Programs</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/student-records') ?>">Student Records</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('/admin/users') ?>">Manage Users</a></li>
                            </ul>
                        </li>
                    <?php elseif ($role === 'teacher'): ?>
                        <li class="nav-item"><a href="<?= site_url('/courses') ?>" class="nav-link">My Courses</a></li>
                        <li class="nav-item"><a href="<?= site_url('/courses/create') ?>" class="nav-link">Create Course</a></li>
                    <?php elseif ($role === 'student'): ?>
                        <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">My Dashboard</a></li>
                        <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">My Courses</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <?php if (! session('isLoggedIn')): ?>
                    <a href="<?= site_url('/login') ?>" class="btn btn-outline-light me-2">Login</a>
                    <a href="<?= site_url('/register') ?>" class="btn btn-light text-primary">Register</a>
                <?php else: ?>
                    <!-- Notifications dropdown -->
                    <div class="dropdown me-3">
                        <a class="btn btn-secondary dropdown-toggle position-relative" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Notifications
                            <span id="notif-badge" class="badge bg-danger position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="width: 300px;" id="notificationsDropdownMenu">
                            <li><div class="dropdown-header">Notifications</div></li>
                            <li><div id="notif-list" class="list-group list-group-flush"></div></li>
                            <li><div class="dropdown-footer text-center small text-muted p-2">Last 5 notifications</div></li>
                        </ul>
                    </div>

                    <span class="navbar-text me-3 small"><?= esc(session('userEmail')) ?> (<?= esc($role) ?>)</span>
                    <a href="<?= site_url('/settings') ?>" class="btn btn-outline-light me-2">Settings</a>
                    <a href="<?= site_url('/logout') ?>" class="btn btn-light text-primary">Logout</a>
                <?php endif; ?>
            </div>
            
            <script>
            // Fetch and render notifications
            function fetchNotifications() {
                $.getJSON('<?= site_url('/notifications') ?>')
                    .done(function(res) {
                        if (!res.success) return;
                        var count = parseInt(res.unread_count) || 0;
                        var $badge = $('#notif-badge');
                        if (count > 0) {
                            $badge.text(count).show();
                        } else {
                            $badge.hide();
                        }

                        var list = $('#notif-list');
                        list.empty();
                        // Since we're now only getting unread notifications from the server,
                        // we can display all of them without filtering
                        if (res.notifications && res.notifications.length > 0) {
                            res.notifications.forEach(function(n) {
                                var item = $('<div>').addClass('list-group-item bg-dark text-light').attr('data-notification-id', n.id);
                                var msg = $('<div>').text(n.message);
                                var time = $('<div>').addClass('small text-muted').text(n.created_at);
                                var btn = $('<button>').addClass('btn btn-sm btn-outline-light mt-2').text('Mark as Read');
                                btn.on('click', function(e) {
                                    // Prevent dropdown from closing
                                    e.preventDefault();
                                    e.stopPropagation();
                                    
                                    var $notificationItem = $(this).closest('.list-group-item');
                                    var notificationId = $notificationItem.data('notification-id');
                                    
                                    var $btn = $(this);
                                    
                                    // Disable button to prevent double clicks
                                    $btn.prop('disabled', true).text('Marking...');
                                    
                                    $.post('<?= site_url('/notifications/mark_read') ?>/' + notificationId)
                                        .done(function(resp) {
                                            if (resp.success) {
                                                // Remove the notification item immediately
                                                $notificationItem.fadeOut(300, function() {
                                                    $(this).remove();
                                                    
                                                    // Update badge count from response
                                                    var newCount = parseInt(resp.unread_count) || 0;
                                                    var $badge = $('#notif-badge');
                                                    if (newCount > 0) {
                                                        $badge.text(newCount).show();
                                                    } else {
                                                        $badge.hide();
                                                    }
                                                    
                                                    // If no notifications left, show message
                                                    if ($('#notif-list .list-group-item').length === 0) {
                                                        $('#notif-list').append($('<div>').addClass('p-3 small text-muted').text('No unread notifications'));
                                                    }
                                                });
                                            } else {
                                                // Re-enable button if failed
                                                $btn.prop('disabled', false).text('Mark as Read');
                                                alert('Failed to mark as read. Please try again.');
                                            }
                                        })
                                        .fail(function() {
                                            // Re-enable button if failed
                                            $btn.prop('disabled', false).text('Mark as Read');
                                            alert('Error marking notification as read. Please try again.');
                                        });
                                    
                                    return false;
                                });
                                item.append(msg).append(time).append(btn);
                                list.append(item);
                            });
                        } else {
                            list.append($('<div>').addClass('p-3 small text-muted').text('No unread notifications'));
                        }
                    });
            }

            $(document).ready(function() {
                // Initial fetch
                fetchNotifications();
                // Optional refresh every 60s
                setInterval(fetchNotifications, 60000);
            });
            </script>
        </div>
    </div>
</nav>
