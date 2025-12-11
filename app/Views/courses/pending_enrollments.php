<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Pending Enrollments</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Enrollment Requests Awaiting Approval</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($pending_enrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>CN</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_enrollments as $enrollment): ?>
                                <tr>
                                    <td><strong><?= esc($enrollment['student_name']) ?></strong></td>
                                    <td><?= esc($enrollment['student_email']) ?></td>
                                    <td><?= esc($enrollment['course_title']) ?></td>
                                    <td><?= esc($enrollment['course_number'] ?? 'N/A') ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($enrollment['enrolled_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-success approve-enrollment" 
                                                    data-enrollment-id="<?= $enrollment['id'] ?>"
                                                    data-student-name="<?= esc($enrollment['student_name']) ?>"
                                                    data-course-title="<?= esc($enrollment['course_title']) ?>">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger reject-enrollment" 
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal"
                                                    data-enrollment-id="<?= $enrollment['id'] ?>"
                                                    data-student-name="<?= esc($enrollment['student_name']) ?>"
                                                    data-course-title="<?= esc($enrollment['course_title']) ?>">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No pending enrollment requests at this time.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectEnrollmentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="rejection_reason" 
                                  name="rejection_reason" 
                                  rows="4" 
                                  required
                                  minlength="10"
                                  placeholder="Please provide a valid reason for rejecting this enrollment (minimum 10 characters)..."></textarea>
                        <small class="form-text text-muted">Minimum 10 characters required.</small>
                    </div>
                    <input type="hidden" id="reject_enrollment_id" name="enrollment_id">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Approve enrollment
    document.querySelectorAll('.approve-enrollment').forEach(btn => {
        btn.addEventListener('click', function() {
            const enrollmentId = this.getAttribute('data-enrollment-id');
            const studentName = this.getAttribute('data-student-name');
            const courseTitle = this.getAttribute('data-course-title');
            
            if (confirm('Approve enrollment for ' + studentName + ' in ' + courseTitle + '?')) {
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                fetch('<?= base_url('courses/approve-enrollment') ?>/' + enrollmentId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to approve enrollment'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the enrollment.');
                });
            }
        });
    });

    // Reject enrollment modal setup
    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const enrollmentId = button.getAttribute('data-enrollment-id');
            const studentName = button.getAttribute('data-student-name');
            const courseTitle = button.getAttribute('data-course-title');
            
            const modalTitle = rejectModal.querySelector('.modal-title');
            modalTitle.textContent = 'Reject Enrollment - ' + studentName + ' (' + courseTitle + ')';
            
            document.getElementById('reject_enrollment_id').value = enrollmentId;
            document.getElementById('rejection_reason').value = '';
        });
    }

    // Handle reject form submission
    const rejectForm = document.getElementById('rejectEnrollmentForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const enrollmentId = document.getElementById('reject_enrollment_id').value;
            const rejectionReason = document.getElementById('rejection_reason').value.trim();
            
            if (rejectionReason.length < 10) {
                alert('Rejection reason must be at least 10 characters long.');
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('<?= base_url('courses/reject-enrollment') ?>/' + enrollmentId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                    modal.hide();
                    // Reload page
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject enrollment'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while rejecting the enrollment.');
            });
        });
    }
});
</script>
<?= $this->endSection() ?>

