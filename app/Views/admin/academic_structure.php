<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Academic Structure Management</h3>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Create New School Year Banner / Actions -->
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="bi bi-calendar-plus me-2"></i>
            <strong>Create New School Year</strong>
        </div>
        <div class="card-body bg-light">
            <div class="alert alert-warning" role="alert">
                <div class="fw-bold mb-1">Required: You must provide dates for all terms:</div>
                <ul class="mb-0">
                    <li><span class="fw-semibold">Semester 1</span> - Term 1 and Term 2 dates</li>
                    <li><span class="fw-semibold">Semester 2</span> - Term 1 and Term 2 dates</li>
                </ul>
            </div>

            <div class="mb-3">
                <label for="select-school-year" class="form-label">School Year</label>
                <select id="select-school-year" class="form-select" aria-label="Select School Year">
                    <option selected disabled>Select School Year</option>
                    <!-- Options should be populated from backend (e.g., 2024-2025) -->
                </select>
                <div class="form-text">Select a school year from the dropdown</div>
            </div>
        </div>
    </div>

    <!-- Semester 1 -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header text-white" style="background-color:#157a8a;">
            <strong>Semester 1</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Term 1 -->
                <div class="col-md-6">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-dark text-white py-2">
                            <strong>Term 1</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="s1t1-start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="s1t1-start" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="mb-0">
                                <label for="s1t1-end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="s1t1-end" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Term 2 -->
                <div class="col-md-6">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-dark text-white py-2">
                            <strong>Term 2</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="s1t2-start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="s1t2-start" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="mb-0">
                                <label for="s1t2-end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="s1t2-end" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester 2 -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header text-dark" style="background-color:#ffd24d;">
            <strong>Semester 2</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Term 1 -->
                <div class="col-md-6">
                    <div class="card border-secondary h-100">
                        <div class="card-header bg-dark text-white py-2">
                            <strong>Term 1</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="s2t1-start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="s2t1-start" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="mb-0">
                                <label for="s2t1-end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="s2t1-end" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Term 2 -->
                <div class="col-md-6">
                    <div class="card border-secondary h-100">
                        <div class="card-header bg-dark text-white py-2">
                            <strong>Term 2</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="s2t2-start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="s2t2-start" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="mb-0">
                                <label for="s2t2-end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="s2t2-end" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button id="btn-save-structure" class="btn btn-primary">Save Structure</button>
        <button id="btn-reset-structure" class="btn btn-outline-secondary">Reset</button>
    </div>
</div>

<!-- Optional icons (Bootstrap Icons CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
// Placeholder JS hooks to integrate with your backend API later
(function() {
  const state = {
    yearId: null,
    s1t1: { start: null, end: null },
    s1t2: { start: null, end: null },
    s2t1: { start: null, end: null },
    s2t2: { start: null, end: null }
  };

  function init() {
    document.getElementById('btn-save-structure').addEventListener('click', onSave);
    document.getElementById('btn-reset-structure').addEventListener('click', onReset);
  }

  function onSave() {
    const payload = collectPayload();
    if (!validatePayload(payload)) return;

    // TODO: Replace with real API endpoint for saving academic structure
    // Example: fetch('<?= base_url('api/academic/structure/save') ?>', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
    alert('Structure payload prepared. Integrate with backend to save.');
    console.log('Academic Structure Payload:', payload);
  }

  function onReset() {
    ['s1t1-start','s1t1-end','s1t2-start','s1t2-end','s2t1-start','s2t1-end','s2t2-start','s2t2-end'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
  }

  function collectPayload() {
    const get = id => (document.getElementById(id) || {}).value || null;
    return {
      academic_year_id: document.getElementById('select-school-year').value || null,
      semesters: [
        {
          sequence: 1,
          terms: [
            { sequence: 1, start_date: get('s1t1-start'), end_date: get('s1t1-end') },
            { sequence: 2, start_date: get('s1t2-start'), end_date: get('s1t2-end') }
          ]
        },
        {
          sequence: 2,
          terms: [
            { sequence: 1, start_date: get('s2t1-start'), end_date: get('s2t1-end') },
            { sequence: 2, start_date: get('s2t2-start'), end_date: get('s2t2-end') }
          ]
        }
      ]
    };
  }

  function validatePayload(payload) {
    if (!payload.academic_year_id) {
      alert('Please select a School Year.');
      return false;
    }
    // Example validation: ensure each term has start and end
    for (const sem of payload.semesters) {
      for (const term of sem.terms) {
        if (!term.start_date || !term.end_date) {
          alert(`Please complete dates for Semester ${sem.sequence} - Term ${term.sequence}.`);
          return false;
        }
        if (term.end_date < term.start_date) {
          alert(`End date must be after start date for Semester ${sem.sequence} - Term ${term.sequence}.`);
          return false;
        }
      }
    }
    return true;
  }

  document.addEventListener('DOMContentLoaded', init);
})();
</script>
<?= $this->endSection() ?>
