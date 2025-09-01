
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4">Create Your Account</h2>

        <?php if (session()->getFlashdata('register_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('register_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url('register') ?>" method="post">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= esc(old('name')) ?>" placeholder="Enter your full name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= esc(old('email')) ?>" placeholder="Enter your email">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Confirm your password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3 small">
            Already have an account? <a href="<?= base_url('login') ?>" class="text-primary">Log in here</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>