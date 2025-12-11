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
            <?php if (!$canCreateNewYear && !empty($currentYearMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Hindi maaaring gumawa ng bagong school year:</strong><br>
                    <?= esc($currentYearMessage) ?>
                </div>
            <?php else: ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Maaari kang gumawa ng bagong school year.</strong>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-warning" role="alert">
                <div class="fw-bold mb-1">Required: You must provide dates for all terms:</div>
                <ul class="mb-0">
                    <li><span class="fw-semibold">Semester 1</span> - Term 1 and Term 2 dates</li>
                    <li><span class="fw-semibold">Semester 2</span> - Term 1 and Term 2 dates</li>
                </ul>
            </div>

            <div class="mb-3">
                <label for="select-school-year" class="form-label">School Year <span class="text-danger">*</span></label>
                <select id="select-school-year" class="form-select" aria-label="Select School Year">
                    <option value="" selected disabled>-- Select School Year --</option>
                    <?php if (isset($academicYears) && !empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= esc($year['id']) ?>" <?= ($year['is_current'] ?? 0) ? 'data-current="1"' : '' ?>>
                                <?= esc($year['display']) ?>
                                <?= ($year['is_current'] ?? 0) ? ' (Current)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Walang naka-record na school year</option>
                    <?php endif; ?>
                    <?php if ($canCreateNewYear): ?>
                        <option value="new" style="color: #0d6efd; font-weight: bold;">+ Create New School Year</option>
                    <?php else: ?>
                        <option value="new" disabled style="color: #6c757d; font-style: italic;">+ Create New School Year (Not Available - May aktibong school year)</option>
                    <?php endif; ?>
                </select>
                <div class="form-text">
                    <?php if (empty($academicYears)): ?>
                        <span class="text-warning">Walang school year sa database. Piliin ang "Create New School Year" para magsimula.</span>
                    <?php elseif ($canCreateNewYear): ?>
                        Pumili ng existing school year o gumawa ng bago
                    <?php else: ?>
                        Pumili ng existing school year para i-view o i-edit
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- New School Year Form (shown when "Create New" is selected) -->
            <div id="new-year-form" style="display: none;" class="mt-3 p-3 border rounded bg-white">
                <h6 class="mb-3">New School Year Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new-year-start" class="form-label">Year Start <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new-year-start" min="2020" max="2100" placeholder="e.g., 2025">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="new-year-end" class="form-label">Year End <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new-year-end" min="2020" max="2100" placeholder="e.g., 2026">
                    </div>
                </div>
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
                                <label for="s1t1-start" class="form-label">Start Date <small class="text-muted">(Auto-calculates other terms)</small></label>
                                <input type="date" class="form-control" id="s1t1-start" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="mb-0">
                                <label for="s1t1-end" class="form-label">End Date <small class="text-muted">(Auto-calculates other terms)</small></label>
                                <input type="date" class="form-control" id="s1t1-end" placeholder="dd/mm/yyyy">
                            </div>
                            <small class="text-info"><i class="bi bi-info-circle"></i> Kapag naka-set ang Term 1, awtomatikong makakalkula ang iba pang terms (3 buwan ang interval).</small>
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

    <div class="d-flex justify-content-end gap-2 mb-4">
        <button id="btn-save-structure" class="btn btn-primary">Save Structure</button>
        <button id="btn-reset-structure" class="btn btn-outline-secondary">Reset</button>
    </div>

    <!-- Display Saved Academic Structures -->
    <div class="mt-5">
        <h4 class="mb-3">Saved Academic Structures</h4>
        
        <?php if (empty($academicStructures)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Walang naka-save na academic structure. Gumawa ng bago sa itaas.
            </div>
        <?php else: ?>
            <?php foreach ($academicStructures as $structure): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center <?= $structure['is_current'] ? 'bg-success text-white' : 'bg-secondary text-white' ?>">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            School Year: <?= esc($structure['display']) ?>
                            <?php if ($structure['is_current']): ?>
                                <span class="badge bg-light text-dark ms-2">Current</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($structure['semesters'])): ?>
                            <p class="text-muted mb-0">Walang naka-set na semesters para sa school year na ito.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($structure['semesters'] as $semester): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-primary h-100">
                                            <div class="card-header bg-primary text-white">
                                                <strong><?= esc($semester['name']) ?> Semester</strong>
                                            </div>
                                            <div class="card-body">
                                                <?php if (empty($semester['terms'])): ?>
                                                    <p class="text-muted mb-0 small">Walang naka-set na terms.</p>
                                                <?php else: ?>
                                                    <?php foreach ($semester['terms'] as $term): ?>
                                                        <div class="mb-2 p-2 border rounded">
                                                            <strong><?= esc($term['name']) ?></strong>
                                                            <?php if ($term['start_date'] && $term['end_date']): ?>
                                                                <div class="small text-muted mt-1">
                                                                    <i class="bi bi-calendar-event me-1"></i>
                                                                    <strong>Start:</strong> <?= date('F d, Y', strtotime($term['start_date'])) ?><br>
                                                                    <strong>End:</strong> <?= date('F d, Y', strtotime($term['end_date'])) ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="small text-warning mt-1">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                                    Walang naka-set na dates
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Optional icons (Bootstrap Icons CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
// Placeholder JS hooks to integrate with your backend API later
(function() {
  const state = {
    yearId: null,
    isNewYear: false,
    s1t1: { start: null, end: null },
    s1t2: { start: null, end: null },
    s2t1: { start: null, end: null },
    s2t2: { start: null, end: null }
  };

  // Function to add months to a date
  function addMonths(date, months) {
    const result = new Date(date);
    result.setMonth(result.getMonth() + months);
    return result;
  }

  // Function to format date as YYYY-MM-DD
  function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  // Function to calculate and auto-fill term dates
  function calculateTermDates() {
    const s1t1Start = document.getElementById('s1t1-start').value;
    const s1t1End = document.getElementById('s1t1-end').value;

    if (!s1t1Start || !s1t1End) {
      return; // Don't calculate if Term 1 is not complete
    }

    const startDate = new Date(s1t1Start);
    const endDate = new Date(s1t1End);

    // Validate that end date is after start date
    if (endDate <= startDate) {
      return;
    }

    // Calculate duration of Term 1 in days
    const termDurationDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

    // Term 2 starts 3 months after Term 1 ends
    const s1t2StartDate = addMonths(endDate, 3);
    // Term 2 ends after the same duration as Term 1
    const s1t2EndDate = new Date(s1t2StartDate);
    s1t2EndDate.setDate(s1t2EndDate.getDate() + termDurationDays);

    // Term 3 (Semester 2, Term 1) starts 3 months after Term 2 ends
    const s2t1StartDate = addMonths(s1t2EndDate, 3);
    const s2t1EndDate = new Date(s2t1StartDate);
    s2t1EndDate.setDate(s2t1EndDate.getDate() + termDurationDays);

    // Term 4 (Semester 2, Term 2) starts 3 months after Term 3 ends
    const s2t2StartDate = addMonths(s2t1EndDate, 3);
    const s2t2EndDate = new Date(s2t2StartDate);
    s2t2EndDate.setDate(s2t2EndDate.getDate() + termDurationDays);

    // Auto-fill the dates automatically
    document.getElementById('s1t2-start').value = formatDate(s1t2StartDate);
    document.getElementById('s1t2-end').value = formatDate(s1t2EndDate);
    document.getElementById('s2t1-start').value = formatDate(s2t1StartDate);
    document.getElementById('s2t1-end').value = formatDate(s2t1EndDate);
    document.getElementById('s2t2-start').value = formatDate(s2t2StartDate);
    document.getElementById('s2t2-end').value = formatDate(s2t2EndDate);
  }

  function init() {
    document.getElementById('btn-save-structure').addEventListener('click', onSave);
    document.getElementById('btn-reset-structure').addEventListener('click', onReset);
    
    // Auto-calculate term dates when Term 1 dates are set
    const s1t1StartInput = document.getElementById('s1t1-start');
    const s1t1EndInput = document.getElementById('s1t1-end');
    
    if (s1t1StartInput && s1t1EndInput) {
      s1t1StartInput.addEventListener('change', calculateTermDates);
      s1t1EndInput.addEventListener('change', calculateTermDates);
    }
    
    // Handle school year dropdown change
    const yearSelect = document.getElementById('select-school-year');
    if (yearSelect) {
      // Auto-select "Create New" if no existing years (only "Create New" option exists)
      const options = yearSelect.options;
      if (options.length === 2 && options[1].value === 'new') {
        // Only "Select" and "Create New" options exist
        yearSelect.value = 'new';
        const newYearForm = document.getElementById('new-year-form');
        if (newYearForm) newYearForm.style.display = 'block';
        state.isNewYear = true;
        state.yearId = null;
      }
      
      yearSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const newYearForm = document.getElementById('new-year-form');
        
        if (selectedValue === 'new') {
          state.isNewYear = true;
          state.yearId = null;
          if (newYearForm) newYearForm.style.display = 'block';
        } else {
          state.isNewYear = false;
          state.yearId = selectedValue;
          if (newYearForm) newYearForm.style.display = 'none';
        }
      });
    }
  }

  function onSave() {
    const payload = collectPayload();
    if (!validatePayload(payload)) return;

    // Show loading state
    const saveBtn = document.getElementById('btn-save-structure');
    const originalText = saveBtn.textContent;
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    fetch('<?= base_url('admin/academic-structure/save') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
      },
      body: JSON.stringify(payload)
    })
    .then(async response => {
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json();
      } else {
        // If response is not JSON, it might be an error page
        const text = await response.text();
        console.error('Non-JSON response:', text);
        throw new Error('Server returned non-JSON response. Status: ' + response.status);
      }
    })
    .then(data => {
      if (data.success) {
        alert('Academic structure saved successfully!');
        window.location.reload();
      } else {
        alert('Error: ' + (data.message || 'Failed to save academic structure'));
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      console.error('Payload sent:', payload);
      alert('An error occurred while saving: ' + error.message + '\n\nPlease check the browser console for more details.');
      saveBtn.disabled = false;
      saveBtn.textContent = originalText;
    });
  }

  function onReset() {
    ['s1t1-start','s1t1-end','s1t2-start','s1t2-end','s2t1-start','s2t1-end','s2t2-start','s2t2-end'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
  }

  function collectPayload() {
    const get = id => (document.getElementById(id) || {}).value || null;
    const yearSelect = document.getElementById('select-school-year');
    const selectedValue = yearSelect ? yearSelect.value : null;
    
    const payload = {
      is_new_year: selectedValue === 'new',
      academic_year_id: selectedValue === 'new' ? null : selectedValue,
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
    
    // If creating new year, include year start and end
    if (selectedValue === 'new') {
      payload.year_start = parseInt(get('new-year-start')) || null;
      payload.year_end = parseInt(get('new-year-end')) || null;
    }
    
    return payload;
  }

  function validatePayload(payload) {
    // Validate school year selection
    if (payload.is_new_year) {
      if (!payload.year_start || !payload.year_end) {
        alert('Please provide both Year Start and Year End for the new school year.');
        return false;
      }
      if (payload.year_end <= payload.year_start) {
        alert('Year End must be greater than Year Start.');
        return false;
      }
      
      // Check if can create new year (server-side validation will also check this)
      <?php if (!$canCreateNewYear): ?>
      alert('Hindi maaaring gumawa ng bagong school year habang may aktibong school year.');
      return false;
      <?php endif; ?>
    } else {
      if (!payload.academic_year_id) {
        alert('Please select a School Year.');
        return false;
      }
      
      // Check if selected year is current and user is trying to modify
      const yearSelect = document.getElementById('select-school-year');
      const selectedOption = yearSelect.options[yearSelect.selectedIndex];
      if (selectedOption && selectedOption.dataset.current === '1') {
        if (!confirm('Ang napiling school year ay kasalukuyang aktibo. Sigurado ka bang gusto mong i-edit ito?')) {
          return false;
        }
      }
    }
    
    // Validate term dates
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
