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

<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <?= $this->renderSection('content') ?>
    <footer class="mt-5 text-center text-muted small">
        <hr>
        <div>Online Student Portal &middot; Built with CodeIgniter 4 & Bootstrap 5</div>
    </footer>
    </div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
