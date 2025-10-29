<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Course Management</h1>
        <div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary me-2">Back to Dashboard</a>
            <a href="<?= base_url('courses/create') ?>" class="btn btn-primary">Create New Course</a>
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

    <div class="row">
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
                <div class="col-md-6 col-lg-4 mb-4">
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
<?= $this->endSection() ?>
