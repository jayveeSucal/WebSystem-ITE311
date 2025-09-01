
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-danger">Logout</a>
    </div>

    <div class="alert alert-primary text-center" role="alert">
        Welcome back, <strong><?= esc(session('userEmail')) ?></strong>!
    </div>


    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <h5>About This Page</h5>
            <p>This is a protected page only visible after login. Use this dashboard to view key metrics and recent activities.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>