<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Edit User</h1>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back to Users</a>
    </div>

    <?php if (session()->getFlashdata('user_error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('user_error')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">User Details</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/users/update/' . $editUser['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $editUser['name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= esc($editUser['email'] ?? '') ?>" disabled>
                </div>
                <?php $currentRole = old('role', $editUser['role'] ?? 'student'); ?>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <?php if ($currentRole === 'admin'): ?>
                        <input type="text" class="form-control" value="Admin" disabled>
                        <!-- keep original role in hidden input so controller sees it if needed -->
                        <input type="hidden" name="role" value="admin">
                        <div class="form-text text-muted">Admin role cannot be changed.</div>
                    <?php else: ?>
                        <select name="role" id="role" class="form-select" required>
                            <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= $currentRole === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= $currentRole === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    <?php endif; ?>
                </div>
                <?php if (!($editUser['role'] === 'admin' && $editUser['id'] == $user['id'])): ?>
                    <div class="mb-3">
                        <label for="active" class="form-label">Status</label>
                        <?php $currentActive = (string) old('active', (string) ($editUser['active'] ?? '1')); ?>
                        <select name="active" id="active" class="form-select" required>
                            <option value="1" <?= $currentActive === '1' ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= $currentActive === '0' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" value="Active (Cannot be changed for your own account)" disabled>
                        <input type="hidden" name="active" value="1">
                        <div class="form-text text-muted">You cannot deactivate your own admin account.</div>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
