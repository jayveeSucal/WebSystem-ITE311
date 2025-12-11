<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Enrolled Students - <?= esc($course['title']) ?></h1>
        <a href="<?= base_url('admin/courses/schedule') ?>" class="btn btn-secondary">Back to Schedule</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($enrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Enrolled At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?= esc($enrollment['name']) ?></td>
                                    <td><?= esc($enrollment['email']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($enrollment['enrolled_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <strong>Total Enrolled: <?= count($enrollments) ?></strong>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No students enrolled in this course yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

