<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">Site</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a href="<?= site_url('/') ?>" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="<?= site_url('/about') ?>" class="nav-link">About</a></li>
                <li class="nav-item"><a href="<?= site_url('/contact') ?>" class="nav-link">Contact</a></li>
                <?php $role = session('userRole'); ?>
                <?php if (session('isLoggedIn')): ?>
                    <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">Dashboard</a></li>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item"><a href="<?= site_url('/courses') ?>" class="nav-link">Manage Courses</a></li>
                        <li class="nav-item"><a href="<?= site_url('/courses/create') ?>" class="nav-link">Create Course</a></li>
                    <?php elseif ($role === 'teacher'): ?>
                        <li class="nav-item"><a href="<?= site_url('/courses') ?>" class="nav-link">My Courses</a></li>
                        <li class="nav-item"><a href="<?= site_url('/courses/create') ?>" class="nav-link">Create Course</a></li>
                    <?php elseif ($role === 'student'): ?>
                        <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">My Dashboard</a></li>
                        <li class="nav-item"><a href="<?= site_url('/dashboard') ?>" class="nav-link">My Courses</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <?php if (! session('isLoggedIn')): ?>
                    <a href="<?= site_url('/login') ?>" class="btn btn-outline-light me-2">Login</a>
                    <a href="<?= site_url('/register') ?>" class="btn btn-light text-primary">Register</a>
                <?php else: ?>
                    <span class="navbar-text me-3 small"><?= esc(session('userEmail')) ?> (<?= esc($role) ?>)</span>
                    <a href="<?= site_url('/logout') ?>" class="btn btn-light text-primary">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


