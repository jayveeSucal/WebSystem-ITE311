<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Programs</h2>

    <?php if (session()->getFlashdata('prog_success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('prog_success')) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="<?= site_url('/admin/programs/create') ?>" class="btn btn-primary">Create Program</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($programs)): ?>
                    <?php foreach ($programs as $prog): ?>
                        <tr>
                            <td><?= esc($prog['code']) ?></td>
                            <td><?= esc($prog['name']) ?></td>
                            <td><?= esc($prog['dept_code']) ?> - <?= esc($prog['dept_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No programs defined.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('templates/footer') ?>
