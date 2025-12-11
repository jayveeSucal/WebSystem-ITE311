<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Completed Courses</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Course Completion History</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Course #</th>
                                <th>Teacher</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Term</th>
                                <th>Schedule Date</th>
                                <th>Enrolled Students</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($course['title']) ?></strong>
                                        <?php if (!empty($course['description'])): ?>
                                            <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($course['course_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['teacher_name'] ?? 'Unassigned') ?></td>
                                    <td><?= esc($course['academic_year'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['term'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($course['schedule_date'])): ?>
                                            <?= date('M d, Y', strtotime($course['schedule_date'])) ?>
                                            <?php if (!empty($course['schedule_time'])): ?>
                                                <br><small class="text-muted"><?= date('h:i A', strtotime($course['schedule_time'])) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?= esc($course['enrolled_count'] ?? 0) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Completed</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <strong>Total Completed Courses: <?= count($courses) ?></strong>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No completed courses found. Courses are marked as completed when their schedule date has passed and they have enrolled students.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

