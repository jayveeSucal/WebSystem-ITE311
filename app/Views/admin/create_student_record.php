<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Create Student Record</h2>

    <?php if (session()->getFlashdata('stud_error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('stud_error')) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('/admin/student-records/store') ?>" method="post" class="card card-body bg-light">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="user_id" class="form-label">Student User</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">-- Select Student User --</option>
                <?php foreach ($studentUsers as $u): ?>
                    <option value="<?= esc($u['id']) ?>" <?= set_select('user_id', $u['id']) ?>>
                        <?= esc($u['name']) ?> (<?= esc($u['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="student_number" class="form-label">Student Number</label>
            <input type="text" name="student_number" id="student_number" class="form-control" value="<?= set_value('student_number') ?>" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
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
            <div class="col-md-6">
                <label for="program_id" class="form-label">Program</label>
                <select name="program_id" id="program_id" class="form-select" required>
                    <option value="">-- Select Program --</option>
                    <?php foreach ($programs as $prog): ?>
                        <option value="<?= esc($prog['id']) ?>" <?= set_select('program_id', $prog['id']) ?>>
                            <?= esc($prog['code']) ?> - <?= esc($prog['name']) ?> (<?= esc($prog['department_id']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <p class="text-muted small">
            Note: On save, the system will verify that the selected program actually belongs to the selected department.
        </p>

        <div class="d-flex justify-content-between">
            <a href="<?= site_url('/admin/student-records') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<?= $this->include('templates/footer') ?>
