<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1>Debug Information</h1>
        </div>
        <div class="card-body">
            <h3>System Information</h3>
            <ul>
                <li><strong>PHP Version:</strong> <?= esc($php_version) ?></li>
                <li><strong>CodeIgniter Version:</strong> <?= esc($codeigniter_version) ?></li>
                <li><strong>Environment:</strong> <?= esc($environment) ?></li>
            </ul>
            <a href="<?= base_url('/') ?>" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

