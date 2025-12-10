<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1 class="mb-4">Account Settings</h1>

    <?php if (session()->getFlashdata('settings_success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('settings_success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('settings_error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('settings_error')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Update Email &amp; Password</div>
        <div class="card-body">
            <form action="<?= base_url('settings') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= old('email', $user['email'] ?? '') ?>" required>
                    <div class="form-text">Email may only contain letters, numbers, and . _ - @</div>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <div class="form-text">Required to confirm changes.</div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="new_password_confirm" class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirm" id="new_password_confirm" class="form-control">
                    <div class="form-text">Leave both new password fields empty if you only want to change your email.</div>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
