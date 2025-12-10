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
                                        <?= $scheduleText !== '' ? esc($scheduleText) : '<span class="text-muted">Not scheduled</span>' ?>
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
                                            <a href="<?= site_url('/courses/edit/' . $course['id']) ?>" class="btn btn-outline-primary">Edit</a>
                                            <a href="<?= site_url('/course/enrolled') ?>" class="btn btn-outline-secondary">View Students</a>
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
<?= $this->endSection() ?>
