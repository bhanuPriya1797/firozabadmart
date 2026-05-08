@if($currentApplication)
<!-- Message to Student Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="messageForm" action="{{ route($routeName.'.students.send-message', $currentApplication->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messageModalLabel">Send Message to Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="message" class="form-label">Message to Student</label>
                        <textarea class="form-control" id="message" name="message" rows="6" required 
                            placeholder="Enter your message to the student. This will allow them to resubmit their application after making corrections."></textarea>
                        <div class="form-text">
                            This message will be shown to the student and their application will be set to 'Draft' status for resubmission.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
