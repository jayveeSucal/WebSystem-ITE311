# TODO: Academic Management System Expansion

## Phase 1: Database Design & Core Models (3NF)
- [ ] Create departments table migration
- [ ] Create programs table migration (FK to departments)
- [ ] Add course_status to courses table
- [ ] Create assignments table
- [ ] Create grades table
- [ ] Create teacher_assignments table
- [ ] Update enrollments table (add enrollment_type, status, etc.)
- [ ] Update users table (add department_id, program_id)
- [ ] Create DepartmentModel, ProgramModel, AssignmentModel, GradeModel
- [ ] Update UserModel, CourseModel, EnrollmentModel

## Phase 2: Controllers & Business Logic
- [ ] DepartmentController (CRUD)
- [ ] ProgramController (CRUD)
- [ ] AssignmentController
- [ ] GradeController
- [ ] Update AdminController for hierarchical enrollment
- [ ] Update CourseController for status management
- [ ] Implement course completion logic
- [ ] Add grading weight calculations

## Phase 3: Authentication & Security
- [ ] Implement email OTP system
- [ ] Update AuthController for OTP verification
- [ ] Add email configuration

## Phase 4: Views & UI
- [ ] Department management interface
- [ ] Program management interface
- [ ] Enhanced enrollment interfaces
- [ ] Teacher assignment interface
- [ ] Grading interface
- [ ] Student records with validation

## Phase 5: Validation & Testing
- [ ] Department/program validation logic
- [ ] Hierarchical enrollment permissions
- [ ] Data integrity checks
- [ ] Unit tests for new features

## Completed Tasks
- [x] Create migration to add academic fields to courses table (academic_year, semester, term, schedule_time, schedule_date, course_number).
- [x] Update CourseModel to include new fields in allowedFields.
- [x] Modify Course controller store and update methods to handle academic fields.
- [x] Update create.php and edit.php views to include form inputs for academic fields.
- [x] Update index.php view to display academic information in course list.
- [x] Run migration to apply database changes.
- [x] Modify the `users` method in `app/Controllers/Admin.php` to pass the current user ID to the view.
- [x] Modify `app/Views/admin/users.php` to conditionally hide the deactivate/activate button for the current admin user.
