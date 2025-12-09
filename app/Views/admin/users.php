<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Manage Users</h1>
        <div>
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary me-2">Add User</a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('user_success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('user_success')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Users List</h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table mb-0 table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $u): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($u['name'] ?? '') ?></td>
                                    <td><?= esc($u['email'] ?? '') ?></td>
                                    <td><?= esc($u['role'] ?? '') ?></td>
                                    <td>
                                        <?php $isActive = (int)($u['active'] ?? 1); ?>
                                        <span class="badge <?= $isActive ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $isActive ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= esc($u['created_at'] ?? '') ?></td>
                                    <td><?= esc($u['updated_at'] ?? '') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                        <?php if (!($u['role'] === 'admin' && $u['id'] == $user['id'])): ?>
                                            <form action="<?= base_url('admin/users/toggle/' . $u['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <?php if ($isActive): ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate this user?')">Deactivate</button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Activate this user?')">Activate</button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="p-3 mb-0 text-muted">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
