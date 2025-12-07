<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Add User</h1>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back to Users</a>
    </div>

    <?php if (session()->getFlashdata('user_error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('user_error')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">New User Details</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/users/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">-- Select Role --</option>
                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="teacher" <?= old('role') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                        <option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="active" class="form-label">Status</label>
                    <select name="active" id="active" class="form-select" required>
                        <option value="1" <?= old('active', '1') === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= old('active') === '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
