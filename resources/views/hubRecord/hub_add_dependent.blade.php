<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Dependent - Nybest Medical</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            height: 50px;
            width: auto;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .content-wrapper {
            flex: 1;
            padding: 2rem 0;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            border: 1px solid var(--border-color);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .required {
            color: var(--danger-color);
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control.error {
            border-color: var(--danger-color);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-width: 120px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 0;
            text-align: center;
            color: var(--text-secondary);
            margin-top: auto;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background: #dbeafe;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .form-card {
                padding: 1.5rem;
                margin: 0 0.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .header-title {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .content-wrapper {
                padding: 1rem 0;
            }

            .form-card {
                padding: 1rem;
                border-radius: 12px;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-content">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/logo-ny.png') }}" alt="Nybest Medical" class="logo me-3">
                        
                    </div>
                    <div class="d-none d-md-block">
                       
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-wrapper">
            <div class="container">
                <div class="form-container">
                    <div class="form-card fade-in dependent-form">
                        <div class="form-header">
                            <h2 class="form-title">Add dependents</h2>
                            <p class="form-subtitle">Please fill in the required information to add a new dependent</p>
                        </div>



                        <!-- Dependent Form -->
                        <form id="add_new_hub" novalidate>
                            @csrf
                         
                            <input type="hidden" id="hub_record_id" name="hub_record_id" value="{{$record->id}}">
                            <input type="hidden" name="type" value="link">
                            
                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user text-primary"></i>
                                    Personal Information
                                </h3>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="dep_first_name" class="form-label">
                                            First Name <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="dep_first_name" 
                                               name="first_name" 
                                               placeholder="Enter first name"
                                               required>
                                        <div class="error-message" id="dep_first_name_error"></div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dep_last_name" class="form-label">
                                            Last Name <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="dep_last_name" 
                                               name="last_name" 
                                               placeholder="Enter last name"
                                               required>
                                        <div class="error-message" id="dep_last_name_error"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="dep_email" class="form-label">
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="dep_email" 
                                           name="email" 
                                           placeholder="Enter email address">
                                    <div class="error-message" id="dep_email_error"></div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-phone text-primary"></i>
                                    Contact Information
                                </h3>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="dep_mobile_no" class="form-label">
                                            Mobile Number <span class="required">*</span>
                                        </label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="dep_mobile_no" 
                                               name="mobile" 
                                               placeholder="(555) 123-4567"
                                               maxlength="15"
                                               required>
                                        <div class="error-message" id="dep_mobile_error"></div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dep_phone" class="form-label">
                                            Phone Number
                                        </label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="dep_phone" 
                                               name="phone" 
                                               placeholder="(555) 123-4567"
                                               maxlength="15">
                                        <div class="error-message" id="dep_phone_error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Identification Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-id-card text-primary"></i>
                                    Identification
                                </h3>
                                
                                <div class="form-group">
                                    <label for="dep_ssn" class="form-label">
                                        Social Security Number <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dep_ssn" 
                                           name="ssn" 
                                           placeholder="XXX-XX-XXXX"
                                           maxlength="11"
                                           required>
                                    <div class="error-message" id="dep_ssn_error"></div>
                                    <small class="text-muted">Format: XXX-XX-XXXX</small>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                              
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <i class="fas fa-save"></i>
                                    Save Dependent
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="form-card fade-in thankyou-message" style="display: none;">
                        
                        <div class="form-header">
                            <h2 class="form-title">Thank You!</h2> 
                            <p class="form-subtitle">Your dependent has been added successfully.</p>
                        </div>


                      
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="mb-0">
                            &copy; 2019 - {{ date('Y') }} Nybest Medical. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- jQuery Confirm -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    
    <!-- Toastr for notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Form validation and submission
            const form = $('#add_new_hub');
            const submitBtn = $('#btnSubmit');
            const cancelBtn = $('#btnCancel');

            // Input masking for phone numbers
            function formatPhoneNumber(input) {
                let value = input.value.replace(/\D/g, '');
                if (value.length >= 6) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
                }
                input.value = value;
            }

            // SSN formatting
            function formatSSN(input) {
                let value = input.value.replace(/\D/g, '');
                if (value.length >= 5) {
                    value = value.replace(/(\d{3})(\d{2})(\d{0,4})/, '$1-$2-$3');
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{3})(\d{0,2})/, '$1-$2');
                }
                input.value = value;
            }

            // Apply input formatting
            $('#dep_mobile_no, #dep_phone').on('input', function() {
                formatPhoneNumber(this);
            });

            $('#dep_ssn').on('input', function() {
                formatSSN(this);
            });

            // Real-time validation
            function validateField(field, rules) {
                const value = field.val().trim();
                const fieldName = field.attr('name');
                const errorElement = $(`#${field.attr('id')}_error`);
                
                // Clear previous error
                field.removeClass('error');
                errorElement.removeClass('show').text('');

                // Required field validation
                if (rules.required && !value) {
                    field.addClass('error');
                    errorElement.addClass('show').text(`${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`);
                    return false;
                }

                // Email validation
                if (rules.email && value) {
                    const emailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9.-]+\.[a-z]{2,6}$/gm;
                    if (!emailRegex.test(value)) {
                        field.addClass('error');
                        errorElement.addClass('show').text('Please enter a valid email address');
                        return false;
                    }
                }

                // Phone validation
                if (rules.phone && value && value.replace(/\D/g, '').length < 10) {
                    field.addClass('error');
                    errorElement.addClass('show').text('Please enter a valid phone number');
                    return false;
                }

                // SSN validation
                if (rules.ssn && value) {
                    const ssnPattern = /^\d{3}-\d{2}-\d{4}$/;
                    if (!ssnPattern.test(value)) {
                        field.addClass('error');
                        errorElement.addClass('show').text('Invalid SSN format');
                        return false;
                    }
                }

                return true;
            }

            // Global variables
            let _RECORD_ID = '';
            let _SAVE_HUB_DEPENDENT_DETAILS = "{{ url('hub-dependent-save')}}";
            
            // Validation rules
            const validationRules = {
                'first_name': { required: true },
                'last_name': { required: true },
                'email': { email: true },
                'mobile': { required: true, phone: true },
                'phone': { phone: true },
                'ssn': { required: true, ssn: true }
            };

            // Validate on blur
            Object.keys(validationRules).forEach(fieldName => {
                $(`[name="${fieldName}"]`).on('blur', function() {
                    validateField($(this), validationRules[fieldName]);
                });
            });

            // Form submission
            form.on('submit', function(e) {
                e.preventDefault();
                
                // Validate all fields
                let isValid = true;
                Object.keys(validationRules).forEach(fieldName => {
                    const field = $(`[name="${fieldName}"]`);
                    if (!validateField(field, validationRules[fieldName])) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    toastr.error('Please correct the errors above');
                    return;
                }

                // Show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner"></span> Processing...');
                form.addClass('loading');

                // Collect form data
                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('agency_id', $('#hub_agency_id').val() || '');
                
                
                // Show confirmation dialog
                $.confirm({
                    title: "Are you sure?",
                    content: "The provided data is accurate and relevant, and do you wish to proceed with submission?",
                    type: 'blue',
                    columnClass: 'col-md-9',
                    buttons: {
                        submit: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function () {
                                // AJAX call to save dependent
                                $.ajax({
                                    async: false,
                                    global: false,
                                    type: "POST",
                                    url: _SAVE_HUB_DEPENDENT_DETAILS || '/save-hub-dependent',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        // Reset form state
                                        submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Dependent');
                                        form.removeClass('loading');
                                        
                                        if (res.status) {
                                            toastr.success(res.error_msg || 'Dependent added successfully!');
                                            
                                            // Reset form
                                            form[0].reset();
                                            $('.error-message').removeClass('show').text('');
                                            $('.form-control').removeClass('error');
                                            
                                            // Close modal if exists
                                            $('.close').click();
                                            
                                            // Reload dependent data if function exists
                                            $('.dependent-form').hide();
                                            $('.thankyou-message').show();
                                        } else {
                                            toastr.error(res.error_msg || 'Failed to save dependent');
                                        }
                                    },
                                    error: function(jqr) {
                                        // Reset form state
                                        submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Dependent');
                                        form.removeClass('loading');
                                        
                                        const errorMsg = jqr.responseJSON?.error_msg || 'An error occurred while saving';
                                        toastr.error(errorMsg);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                        }
                    }
                });
            });

            // Cancel button
            cancelBtn.on('click', function() {
                $.confirm({
                    title: 'Are you sure?',
                    content: 'All entered data will be lost.',
                    type: 'orange',
                    buttons: {
                        confirm: {
                            text: 'Yes, cancel',
                            btnClass: 'btn-orange',
                            action: function() {
                                window.history.back();
                            }
                        },
                        cancel: {
                            text: 'No, keep editing',
                            btnClass: 'btn-default'
                        }
                    }
                });
            });

            // Add smooth scrolling for better UX
            $('html').css('scroll-behavior', 'smooth');
        });
    </script>
</body>
</html>

