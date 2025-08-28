<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'My Site' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">Site</a>
        <div>
            <a href="<?= site_url('/') ?>" class="btn btn-outline-light me-2">Home</a>
            <a href="<?= site_url('/about') ?>" class="btn btn-outline-light me-2">About</a>
            <a href="<?= site_url('/contact') ?>" class="btn btn-outline-light">Contact</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card bg-dark text-light p-4">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
