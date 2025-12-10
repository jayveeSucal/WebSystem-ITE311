<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Departments</h2>

    <?php if (session()->getFlashdata('dept_success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('dept_success')) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="<?= site_url('/admin/departments/create') ?>" class="btn btn-primary">Create Department</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($departments)): ?>
                    <?php foreach ($departments as $dept): ?>
                        <tr>
                            <td><?= esc($dept['code']) ?></td>
                            <td><?= esc($dept['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted">No departments defined.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('templates/footer') ?>
