
<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <h2 class="text-center mb-4">Hello</h2>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success" role="alert">
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('login') ?>" method="post">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= esc(old('email')) ?>" placeholder="Enter your email">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3 small">
            Don't have an account? <a href="<?= base_url('register') ?>" class="text-primary">Register here</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>