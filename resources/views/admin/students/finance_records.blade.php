@component('admin.layouts.main')

@slot('title')
    Finance Records - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $usr = auth()->user();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Finance Records Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Finance Records - <span style="color:rgb(4, 126, 248);">{{ $application->student->first_name.' '.$application->student->surname }}</span> (Case ID: <span style="color:rgb(4, 126, 248);">{{ $application->case_id }}</span>)</h4>
            @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.finance_manage')))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFinanceModal">
                <i class="bx bx-plus me-1"></i> Add Finance Record
            </button>
            @endif
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Appeal No</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                        <th>Donation Type</th>
                        <th>Muqalid</th>
                        <th>Support Type</th>
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financeRecords as $record)
                        <tr>
                            <td>{{ $record->appeal_no }}</td>
                            <td><span class="badge bg-label-info">{{ $record->amount }}</span></td>
                            <td>{{ $record->payment_mode }}</td>
                            <td>
                                @if($record->payment_status == 'Completed')
                                    <span class="badge bg-label-success">Completed</span>
                                @else
                                    <span class="badge bg-label-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $record->donation_type }}</td>
                            <td>{{ $record->muqalid }}</td>
                            <td>{{ $record->support_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                            <td>{{ $record->details }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">No finance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Finance Record Modal -->
    <div class="modal fade" id="addFinanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" id="financeForm" action="{{ route($ADMIN_ROUTE_NAME.'.students.storeFinanceRecords', CustomHelper::encrypt($application->id)) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-dollar-circle me-1"></i> Add Finance Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @php
                        $resolvedAmountNeeded = isset($approvedAmount) && $approvedAmount !== null ? $approvedAmount : ($application->amount_needed ?? 0);
                        $totalPaid = $financeRecords->sum('amount');
                        $remainingAmount = max(0, $resolvedAmountNeeded - $totalPaid);
                    @endphp
                    
                    <div class="alert alert-info mb-3">
                        <strong>Amount Needed:</strong> {{ number_format($resolvedAmountNeeded, 0) }}<br>
                        <strong>Total Paid:</strong> {{ number_format($totalPaid, 0) }}<br>
                        <strong>Remaining Amount:</strong> {{ number_format($remainingAmount, 0) }}
                    </div>
                    
                    <div id="financeFormContainer">
                        <div class="finance-record row g-3 border rounded p-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Appeal No</label>
                                <input type="text" name="appeal_no[]" class="form-control" placeholder="Appeal No" value="{{ date('m-Y') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount[]" class="form-control amount-input" placeholder="Amount" value="{{ $remainingAmount > 0 ? $remainingAmount : '' }}" max="{{ $remainingAmount }}">
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Max: {{ number_format($remainingAmount, 0) }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Mode</label>
                                <select name="payment_mode[]" class="form-select">
                                    <option value="">Please Select</option>
                                    <option>Bank Transfer</option>
                                    <option>Cheque</option>
                                    <option>Cash</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="payment_status[]" class="form-select">
                                    <option value="">Please Select</option>
                                    <option>Completed</option>
                                    <option>Pending</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Donation Type</label>
                                <select name="donation_type[]" class="form-select">
                                    <option>Donation</option>
                                    <option>Khums</option>
                                    <option>Zakat</option>
                                    <option>Sadqah</option>
                                    <option>Mali Imam</option>
                                    <option>Mali Sadat</option>
                                    <option>Others</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Muqalid</label>
                                <select name="muqalid[]" class="form-select">
                                    <option value="">Please Select</option>
                                    <option>Imam Khameneni</option>
                                    <option>Syed Ali Sistani</option>
                                    <option>Others</option>
                                    <option>N/A</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Support Type</label>
                                <select name="support_type[]" class="form-select">
                                    <option>One-time</option>
                                    <option>Recurring</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date[]" class="form-control" value="{{ date('Y-m-d') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Details</label>
                                <textarea name="details[]" rows="5" class="form-control" placeholder="Details"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addMoreFinance">
                        <i class="bx bx-plus-circle"></i> Add More
                    </button>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-save me-1"></i> Save Records
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@slot('footerBlock')
<script>
const applicationAmountNeeded = {{ (isset($approvedAmount) && $approvedAmount !== null ? $approvedAmount : ($application->amount_needed ?? 0)) }};
const totalPaid = {{ $financeRecords->sum('amount') }};

function calculateRemainingAmount() {
    let currentTotal = 0;
    document.querySelectorAll('.amount-input').forEach(input => {
        if (input.value) {
            currentTotal += parseFloat(input.value);
        }
    });
    return Math.max(0, applicationAmountNeeded - totalPaid - currentTotal);
}

function updateAmountLimits() {
    const remainingAmount = calculateRemainingAmount();
    document.querySelectorAll('.amount-input').forEach((input, index) => {
        if (index > 0) { // Skip first input as it's already set
            const currentValue = parseFloat(input.value) || 0;
            const maxAllowed = remainingAmount + currentValue;
            input.setAttribute('max', maxAllowed);
            const helpText = input.parentNode.querySelector('.text-muted');
            if (helpText) {
                helpText.textContent = `Max: ${maxAllowed.toLocaleString()}`;
            }
        }
    });
}

document.getElementById('addMoreFinance').addEventListener('click', function () {
    let container = document.getElementById('financeFormContainer');
    let newRecord = container.querySelector('.finance-record').cloneNode(true);
    
    const remainingAmount = calculateRemainingAmount();
    
    // Reset inputs
    newRecord.querySelectorAll('input, textarea').forEach(input => {
        if (input.name === 'amount[]') {
            input.value = remainingAmount > 0 ? remainingAmount : '';
            input.setAttribute('max', remainingAmount);
        } else if (input.name === 'appeal_no[]') {
            input.value = '{{ date('m-Y') }}';
        } else {
            input.value = '';
        }
    });
    newRecord.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    
    // Update help text
    const helpText = newRecord.querySelector('.text-muted');
    if (helpText) {
        helpText.textContent = `Max: ${remainingAmount.toLocaleString()}`;
    }

    // Add remove button only for cloned boxes
    let removeBtn = document.createElement('button');
    removeBtn.type = "button";
    removeBtn.className = "btn btn-sm btn-outline-danger mt-2 remove-finance";
    removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove Box';
    newRecord.appendChild(removeBtn);

    container.appendChild(newRecord);
    
    // Add event listener for amount validation
    const amountInput = newRecord.querySelector('.amount-input');
    amountInput.addEventListener('input', validateAmount);
});

function validateAmount(event) {
    const input = event.target;
    const value = parseFloat(input.value);
    const max = parseFloat(input.getAttribute('max'));
    
    if (value > max) {
        input.setCustomValidity(`Amount cannot exceed ${max.toLocaleString()}`);
        input.classList.add('is-invalid');
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = `Amount cannot exceed ${max.toLocaleString()}`;
        }
    } else {
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }
    
    updateAmountLimits();
}

// Add validation to existing amount inputs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.amount-input').forEach(input => {
        input.addEventListener('input', validateAmount);
    });
});

// Handle remove button
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-finance')) {
        e.target.closest('.finance-record').remove();
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let form = document.getElementById("financeForm");
    let modal = document.getElementById("addFinanceModal");

    // Handle AJAX form submit
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Clear previous errors
        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach(el => el.innerText = "");

        let formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                let data = await response.json();
                if (data.errors) {
                    // Show validation errors
                    Object.keys(data.errors).forEach(field => {
                        if (field === 'amount' && !field.includes('.')) {
                            // General amount error (like exceeding total)
                            let firstAmountInput = form.querySelector('[name="amount[]"]');
                            if (firstAmountInput) {
                                firstAmountInput.classList.add("is-invalid");
                                let feedback = firstAmountInput.closest(".col-md-3, .col-md-12")?.querySelector(".invalid-feedback");
                                if (feedback) {
                                    feedback.innerText = data.errors[field][0];
                                }
                            }
                            // Also show alert for better visibility
                            alert(data.errors[field][0]);
                        } else {
                            // Field-specific errors like "amount.0"
                            let parts = field.split('.');
                            let baseName = parts[0]; // "amount"
                            let index = parts[1] ?? 0; // "0"

                            let inputs = form.querySelectorAll(`[name="${baseName}[]"]`);
                            if (inputs[index]) {
                                inputs[index].classList.add("is-invalid");
                                let feedback = inputs[index].closest(".col-md-3, .col-md-12")?.querySelector(".invalid-feedback");
                                if (feedback) {
                                    feedback.innerText = data.errors[field][0];
                                }
                            }
                        }
                    });
                }
                throw new Error("Validation failed");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal and reset
                bootstrap.Modal.getInstance(modal).hide();
                form.reset();
                document.getElementById("financeFormContainer").innerHTML = form.querySelector(".finance-record").outerHTML;

                // Optionally reload table (or append new row)
                location.reload();
            }
        })
        .catch(err => console.error(err));
    });

    // Reset form when modal closes
    modal.addEventListener("hidden.bs.modal", function () {
        form.reset();
        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach(el => el.innerText = "");
        document.getElementById("financeFormContainer").innerHTML = form.querySelector(".finance-record").outerHTML;
    });
});
</script>
@endslot

@endcomponent