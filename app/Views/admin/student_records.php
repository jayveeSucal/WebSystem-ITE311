<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Student Records</h2>

    <?php if (session()->getFlashdata('stud_success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('stud_success')) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="<?= site_url('/admin/student-records/create') ?>" class="btn btn-primary">Create Student Record</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Student #</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Program</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($students)): ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?= esc($s['student_number']) ?></td>
                            <td><?= esc($s['user_name']) ?></td>
                            <td><?= esc($s['email']) ?></td>
                            <td><?= esc($s['dept_code']) ?> - <?= esc($s['dept_name']) ?></td>
                            <td><?= esc($s['prog_code']) ?> - <?= esc($s['prog_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('templates/footer') ?>
