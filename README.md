# ITE311-BAJARIAS - Online Student Portal (CodeIgniter 4)

## Getting Started

1. Requirements
   - PHP 8.1+
   - Composer
   - MySQL
   - XAMPP (as used in lab)

2. Install dependencies

```bash
composer install
```

3. Configure environment
   - Copy `env` to `.env`
   - Update database credentials in `.env`

4. Run migrations

```bash
php spark migrate
```

5. Seed sample data

```bash
php spark db:seed UserSeeder
php spark db:seed CourseSeeder
php spark db:seed EnrollmentSeeder
```

6. Start development server (or use XAMPP Apache)

```bash
php spark serve
```

## Features Implemented
- Authentication (login, register, logout) with role-based dashboard (student/teacher/admin)
- Course management for teachers/admins (create, edit, delete, list)
- Student enrollment via AJAX with CSRF protection
- Bootstrap 5 styling with shared `template.php`

## Routes
- `/login`, `/register`, `/logout`, `/dashboard`
- `/courses`, `/courses/create`, `/courses/edit/{id}`
- `/course/enroll` (POST), `/course/enrolled` (GET)

## Notes
- Session guards protect teacher/admin pages
- Admin dashboard shows basic counts
- Student dashboard lists enrolled and available courses
