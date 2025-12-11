<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Create New Course</h1>
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
                    <form action="<?= base_url('courses/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?= esc(old('title')) ?>" 
                                   required 
                                   placeholder="Enter course name">
                        </div>

                        <div class="mb-3">
                            <label for="course_number" class="form-label">Control Number (CN) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light" id="cn-prefix">CN-</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="cn_number" 
                                       name="cn_number" 
                                       value="<?= esc(old('cn_number')) ?>" 
                                       required 
                                       placeholder="0001"
                                       pattern="[0-9]{4}"
                                       maxlength="4"
                                       minlength="4"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       aria-describedby="cn-prefix cn-help">
                                <input type="hidden" id="course_number" name="course_number" value="">
                            </div>
                            <div class="form-text" id="cn-help">
                                Mag-type ng eksaktong 4 digits (e.g., 0001, 0002, 0123). Format: CN-0001
                            </div>
                            <small class="text-danger" id="cn-error" style="display: none;"></small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select class="form-select" 
                                        id="academic_year" 
                                        name="academic_year" 
                                        required>
                                    <option value="">-- Select Academic Year --</option>
                                    <?php if (isset($academicYears) && !empty($academicYears)): ?>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= esc($year['display']) ?>" <?= set_select('academic_year', $year['display']) ?>>
                                                <?= esc($year['display']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" 
                                        id="semester" 
                                        name="semester" 
                                        required>
                                    <option value="">-- Select Semester --</option>
                                    <option value="1st Semester" <?= set_select('semester', '1st Semester') ?>>1st Semester</option>
                                    <option value="2nd Semester" <?= set_select('semester', '2nd Semester') ?>>2nd Semester</option>
                                    <option value="Summer" <?= set_select('semester', 'Summer') ?>>Summer</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                <select class="form-select" 
                                        id="term" 
                                        name="term" 
                                        required>
                                    <option value="">-- Select Term --</option>
                                    <option value="Term 1" <?= set_select('term', 'Term 1') ?>>Term 1</option>
                                    <option value="Term 2" <?= set_select('term', 'Term 2') ?>>Term 2</option>
                                    <option value="Term 3" <?= set_select('term', 'Term 3') ?>>Term 3</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="5" 
                                      required 
                                      placeholder="Enter course description"><?= esc(old('description')) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('courses') ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numberInput = document.getElementById('cn_number');
    const hiddenInput = document.getElementById('course_number');
    const errorMsg = document.getElementById('cn-error');
    
    function updateCourseNumber() {
        const number = numberInput.value.trim();
        
        if (number) {
            // Pad with zeros to make it exactly 4 digits (e.g., 1 -> 0001, 12 -> 0012)
            const paddedNumber = number.padStart(4, '0');
            hiddenInput.value = 'CN-' + paddedNumber;
        } else {
            hiddenInput.value = '';
        }
        
        // Clear error when user types
        errorMsg.style.display = 'none';
        errorMsg.textContent = '';
    }
    
    numberInput.addEventListener('input', function() {
        // Limit to 4 digits only
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
        updateCourseNumber();
    });
    
    // Format on blur to ensure proper padding to 4 digits
    numberInput.addEventListener('blur', function() {
        const number = this.value.trim();
        if (number) {
            const paddedNumber = number.padStart(4, '0');
            this.value = paddedNumber;
            updateCourseNumber();
        }
    });
    
    // Validate on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const number = numberInput.value.trim();
        
        if (!number) {
            e.preventDefault();
            errorMsg.textContent = 'Kailangan ang number para sa Control Number.';
            errorMsg.style.display = 'block';
            numberInput.focus();
            return false;
        }
        
        if (!/^\d+$/.test(number)) {
            e.preventDefault();
            errorMsg.textContent = 'Numbers lang ang pwedeng i-type para sa Control Number.';
            errorMsg.style.display = 'block';
            numberInput.focus();
            return false;
        }
        
        if (number.length !== 4) {
            e.preventDefault();
            errorMsg.textContent = 'Kailangan eksaktong 4 digits para sa Control Number (e.g., 0001, 0123).';
            errorMsg.style.display = 'block';
            numberInput.focus();
            return false;
        }
        
        updateCourseNumber();
    });
});
</script>
<?= $this->endSection() ?>
