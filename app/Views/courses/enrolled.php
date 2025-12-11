<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Enrolled Courses</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($courses)): ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($course['title']) ?></h5>
                            <p class="card-text"><?= esc(substr($course['description'] ?? '', 0, 100)) ?>...</p>
                            <p class="text-muted small">
                                Course #: <?= esc($course['course_number'] ?? 'N/A') ?><br>
                                <?php if (!empty($course['schedule_date'])): ?>
                                    Schedule: <?= date('M d, Y', strtotime($course['schedule_date'])) ?>
                                    <?php if (!empty($course['schedule_time'])): ?>
                                        at <?= date('h:i A', strtotime($course['schedule_time'])) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                            <a href="<?= base_url('courses/materials/' . $course['id']) ?>" class="btn btn-primary btn-sm">View Materials</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            You are not enrolled in any courses yet.
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

