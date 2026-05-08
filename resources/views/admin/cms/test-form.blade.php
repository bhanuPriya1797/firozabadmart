@component('admin.layouts.main')

@slot('title')
    Test Dynamic Form - {{ config('app.name') }}
@endslot

@slot('headerBlock')

@endslot
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Test Dynamic Form with Custom Field Validation</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="ti tabler-info-circle me-2"></i>How Dynamic Form Validation Works</h6>
                        <p class="mb-0">This demonstrates how custom fields are automatically validated based on their configuration in the custom fields module.</p>
                    </div>

                    <form action="{{ route(CustomHelper::getAdminRouteName() . '.cms.update', 6) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic CMS fields -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="ti tabler-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="About Us" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="slug" value="about-us" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Template <span class="text-danger">*</span></label>
                                <select class="form-select" name="template" required>
                                    <option value="about-us" selected>About Us Template</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Fields Example -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="ti tabler-settings me-2"></i>Custom Fields (Automatically Validated)
                                </h6>
                                <div class="alert alert-warning">
                                    <strong>Note:</strong> These fields will be automatically validated based on their custom field configuration:
                                    <ul class="mb-0 mt-2">
                                        <li><strong>experience_years</strong>: Number field with min=1, max=100, required</li>
                                        <li><strong>mission_statement</strong>: Text field with min_length=10, required</li>
                                        <li><strong>contact_email</strong>: Email field, required</li>
                                        <li><strong>website_url</strong>: URL field, optional</li>
                                        <li><strong>team_size</strong>: Select field with options, required</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Example custom fields that would be generated dynamically -->
                            <div class="col-md-6">
                                <label class="form-label">Experience Years <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                                       name="experience_years" value="{{ old('experience_years', '15') }}" 
                                       min="1" max="100" required>
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Must be between 1 and 100</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                       name="contact_email" value="{{ old('contact_email', 'info@ilmission.org') }}" required>
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Website URL</label>
                                <input type="url" class="form-control @error('website_url') is-invalid @enderror" 
                                       name="website_url" value="{{ old('website_url', 'https://ilmission.org') }}">
                                @error('website_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Team Size <span class="text-danger">*</span></label>
                                <select class="form-select @error('team_size') is-invalid @enderror" name="team_size" required>
                                    <option value="">Select Team Size</option>
                                    <option value="1-5" {{ old('team_size') == '1-5' ? 'selected' : '' }}>1-5 members</option>
                                    <option value="6-10" {{ old('team_size') == '6-10' ? 'selected' : '' }}>6-10 members</option>
                                    <option value="11-20" {{ old('team_size') == '11-20' ? 'selected' : '' }}>11-20 members</option>
                                    <option value="20+" {{ old('team_size') == '20+' ? 'selected' : '' }}>20+ members</option>
                                </select>
                                @error('team_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Mission Statement <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('mission_statement') is-invalid @enderror" 
                                          name="mission_statement" rows="3" required>{{ old('mission_statement', 'IL Mission provides financial assistance to students in education whose families no longer have the sufficient means to pay the required fees.') }}</textarea>
                                @error('mission_statement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Must be at least 10 characters long</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti tabler-device-floppy me-1"></i> Test Validation
                                </button>
                                <a href="{{ route(CustomHelper::getAdminRouteName() . '.cms.index') }}" class="btn btn-secondary">
                                    <i class="ti tabler-x me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Validation Rules Display -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Generated Validation Rules</h6>
                                </div>
                                <div class="card-body">
                                    <p>When you submit this form, the following validation rules will be automatically applied:</p>
                                    <pre class="bg-light p-3 rounded"><code>// Generated by CustomFieldHelper::generateValidationRules('cms', 6)
$rules = [
    'experience_years' => ['required', 'numeric', 'min:1', 'max:100'],
    'contact_email' => ['required', 'email'],
    'website_url' => ['nullable', 'url'],
    'team_size' => ['required'],
    'mission_statement' => ['required', 'min:10'],
];

$messages = [
    'experience_years.required' => 'The Experience Years field is required.',
    'experience_years.numeric' => 'The Experience Years must be a number.',
    'experience_years.min' => 'The Experience Years must be at least 1.',
    'experience_years.max' => 'The Experience Years may not be greater than 100.',
    'contact_email.required' => 'The Contact Email field is required.',
    'contact_email.email' => 'The Contact Email must be a valid email address.',
    'website_url.url' => 'The Website URL must be a valid URL.',
    'team_size.required' => 'The Team Size field is required.',
    'mission_statement.required' => 'The Mission Statement field is required.',
    'mission_statement.min' => 'The Mission Statement must be at least 10 characters.',
];</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent
