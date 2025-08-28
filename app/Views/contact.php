<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<h1>Contact Us</h1>
<p>You can reach us at <a href="mailto:contact@example.com" class="text-info">contact@example.com</a>.</p>
<form>
    <div class="mb-3">
        <label for="name" class="form-label">Your Name</label>
        <input type="text" id="name" class="form-control" placeholder="Enter your name">
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea id="message" class="form-control" rows="4" placeholder="Your message"></textarea>
    </div>
    <button class="btn btn-success">Send</button>
</form>

<?= $this->endSection() ?>
