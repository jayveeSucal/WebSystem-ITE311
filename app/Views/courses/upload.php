<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Upload Material for <?= esc($course['title']) ?></h1>
        <a href="<?= base_url('courses') ?>" class="btn btn-secondary">Back to Courses</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('courses/upload/' . $course['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="material" class="form-label">Select File <span class="text-danger">*</span></label>
                            <input type="file"
                                   class="form-control"
                                   id="material"
                                   name="material"
                                   accept=".pdf,.ppt,.pptx"
                                   required>
                            <div class="form-text">
                                <strong>Allowed file types: PDF, PPT, PPTX only.</strong> Max size: 10MB.
                            </div>
                            <small class="text-danger" id="file-error" style="display: none;"></small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('courses') ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Upload Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('material');
    const fileError = document.getElementById('file-error');
    const form = document.querySelector('form');
    
    // Validate file type on selection
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name.toLowerCase();
            const allowedExtensions = ['.pdf', '.ppt', '.pptx'];
            const fileExtension = fileName.substring(fileName.lastIndexOf('.'));
            
            // Check file extension
            if (!allowedExtensions.includes(fileExtension)) {
                fileError.textContent = 'Invalid file type. Only PDF, PPT, and PPTX files are allowed.';
                fileError.style.display = 'block';
                this.value = ''; // Clear the input
                return false;
            }
            
            // Check file size (10MB = 10 * 1024 * 1024 bytes)
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                fileError.textContent = 'File size exceeds 10MB limit.';
                fileError.style.display = 'block';
                this.value = ''; // Clear the input
                return false;
            }
            
            // Clear error if valid
            fileError.style.display = 'none';
        }
    });
    
    // Validate on form submit
    form.addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            e.preventDefault();
            fileError.textContent = 'Please select a file to upload.';
            fileError.style.display = 'block';
            return false;
        }
        
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.pdf', '.ppt', '.pptx'];
        const fileExtension = fileName.substring(fileName.lastIndexOf('.'));
        
        if (!allowedExtensions.includes(fileExtension)) {
            e.preventDefault();
            fileError.textContent = 'Invalid file type. Only PDF, PPT, and PPTX files are allowed.';
            fileError.style.display = 'block';
            fileInput.focus();
            return false;
        }
        
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            e.preventDefault();
            fileError.textContent = 'File size exceeds 10MB limit.';
            fileError.style.display = 'block';
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
