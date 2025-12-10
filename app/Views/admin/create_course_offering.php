<?= $this->include('templates/header') ?>

<div class="container my-4">
    <h2 class="mb-3">Create Course Offering</h2>

    <?php if (session()->getFlashdata('offering_error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('offering_error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('/admin/courses/offering/store') ?>" method="post" class="card card-body bg-light">
        <?= csrf_field() ?>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                    <option value="">-- Select Academic Year --</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= esc($year['id']) ?>" <?= set_select('academic_year_id', $year['id']) ?>>
                            <?= esc($year['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="semester_id" class="form-label">Semester</label>
                <select name="semester_id" id="semester_id" class="form-select" required>
                    <option value="">-- Select Semester --</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="term_id" class="form-label">Term</label>
                <select name="term_id" id="term_id" class="form-select" required>
                    <option value="">-- Select Term --</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="course_id" class="form-label">Course (Subject)</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= esc($course['id']) ?>" <?= set_select('course_id', $course['id']) ?>>
                            <?= esc($course['code'] ?? '') ?> - <?= esc($course['title'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="course_number" class="form-label">Course Number (CN)</label>
                <input type="text" name="course_number" id="course_number" class="form-control" value="<?= set_value('course_number') ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="schedule_date" class="form-label">Schedule Date</label>
                <input type="date" name="schedule_date" id="schedule_date" class="form-control" value="<?= set_value('schedule_date') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="schedule_time" class="form-label">Schedule Time</label>
                <input type="time" name="schedule_time" id="schedule_time" class="form-control" value="<?= set_value('schedule_time') ?>" required>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= site_url('/admin/courses/schedule') ?>" class="btn btn-secondary">Back to Schedule</a>
            <button type="submit" class="btn btn-primary">Save Offering</button>
        </div>
    </form>
</div>

<script>
    // Simple dependent dropdowns for Semester and Term
    document.addEventListener('DOMContentLoaded', function () {
        const academicSelect = document.getElementById('academic_year_id');
        const semesterSelect = document.getElementById('semester_id');
        const termSelect = document.getElementById('term_id');

        function clearSelect(select, placeholder) {
            select.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            select.appendChild(opt);
        }

        academicSelect.addEventListener('change', function () {
            const yearId = this.value;
            clearSelect(semesterSelect, '-- Select Semester --');
            clearSelect(termSelect, '-- Select Term --');
            if (!yearId) return;

            fetch('<?= site_url('api/semesters/by-year') ?>/' + yearId)
                .then(res => res.json())
                .then(data => {
                    if (!data || !Array.isArray(data)) return;
                    data.forEach(function (s) {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        semesterSelect.appendChild(opt);
                    });
                });
        });

        semesterSelect.addEventListener('change', function () {
            const semId = this.value;
            clearSelect(termSelect, '-- Select Term --');
            if (!semId) return;

            fetch('<?= site_url('api/terms/by-semester') ?>/' + semId)
                .then(res => res.json())
                .then(data => {
                    if (!data || !Array.isArray(data)) return;
                    data.forEach(function (t) {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.textContent = t.term_number;
                        termSelect.appendChild(opt);
                    });
                });
        });
    });
</script>

<?= $this->include('templates/footer') ?>
