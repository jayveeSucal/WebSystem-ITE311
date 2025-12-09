# TODO: Remove Deactivate Button for Admin User in Manage Users

## Tasks
- [x] Modify the `users` method in `app/Controllers/Admin.php` to pass the current user ID to the view.
- [x] Modify `app/Views/admin/users.php` to conditionally hide the deactivate/activate button for the current admin user.
  - Add a condition to check if the user is an admin and matches the current session user ID.
  - If true, do not display the toggle button.
  - Otherwise, display the button as usual.
