<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Create Program</h2>

    <?php if (session()->getFlashdata('prog_error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('prog_error')) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('/admin/programs/store') ?>" method="post" class="card card-body bg-light">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="department_id" class="form-label">Department</label>
            <select name="department_id" id="department_id" class="form-select" required>
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= esc($dept['id']) ?>" <?= set_select('department_id', $dept['id']) ?>>
                        <?= esc($dept['code']) ?> - <?= esc($dept['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="code" class="form-label">Program Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= set_value('code') ?>" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Program Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= set_value('name') ?>" required>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= site_url('/admin/programs') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<?= $this->include('templates/footer') ?>
