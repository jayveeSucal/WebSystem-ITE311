# Notifications System Implementation TODO

## Database Setup
- [x] Create migration file `CreateNotificationsTable.php` with specified fields (id, user_id, message, is_read, created_at) and up/down methods
- [ ] Run the migration to create the notifications table

## Notification Model
- [ ] Create `NotificationModel.php` with methods: getUnreadCount($userId), getNotificationsForUser($userId), markAsRead($notificationId)

## Update Base Controller
- [ ] Modify `BaseController.php` to fetch unread notification count for logged-in user and pass to views

## Update Layout
- [ ] Modify `header.php` to include notification badge placeholder in navbar

## Notifications Controller & API
- [ ] Create `Notifications.php` controller with `get()` method (returns JSON with unread count and latest notifications)
- [ ] Add `mark_as_read($id)` method to Notifications controller (marks notification as read, returns success/failure JSON)

## Routes
- [ ] Add routes in `Routes.php`: $routes->get('/notifications', 'Notifications::get'); $routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

## Trigger Updates
- [ ] Modify `Course.php` enroll method to create notification when student enrolls in course

## Notification UI
- [ ] Add Bootstrap dropdown in navbar with badge showing unread count
- [ ] Add jQuery AJAX to fetch /notifications on page load and update badge/dropdown
- [ ] Add Mark as Read buttons in dropdown that call /notifications/mark_read/[id]
- [ ] Optionally refresh every 60 seconds

## Testing
- [ ] Test by logging in as student, enrolling in course, verifying badge appears correctly
- [ ] Verify dropdown shows latest notifications
- [ ] Verify Mark as Read works and updates badge
