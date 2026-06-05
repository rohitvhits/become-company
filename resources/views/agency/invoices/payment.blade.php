@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/css/invoice-module.css') }}">

<div class="main-panel">
    <div class="content-wrapper agency-invoice-wrapper">
        <div class="page-title-main">
            <div class="d-flex align-items-center">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-3 font-weight-bold">Pay Invoice {{ $invoice->invoice_number }}</h5>
                    <a href="{{ route('agency.invoices.show', $invoice) }}" class="btn btn-sm btn-secondary invoice-action-btn btn-sm">
                        <i class="mdi mdi-arrow-left me-2"></i>Back to Invoice
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <!-- Payment Amount Section -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-credit-card me-2"></i>Choose Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="payment-methods">
                            @foreach($paymentMethods as $method => $config)
                                @if($config['enabled'])
                                    <div class="payment-method-card mb-3" data-method="{{ $method }}">
                                        <div class="payment-option-modern">
                                            <div class="payment-option-content">
                                                <div class="payment-option-radio">
                                                    <input class="payment-radio" type="radio" name="payment_method"
                                                           id="method_{{ $method }}" value="{{ $method }}">
                                                    <span class="radio-checkmark"></span>
                                                </div>
                                                <label class="payment-option-label" for="method_{{ $method }}">
                                                    <div class="payment-option-info">
                                                        <div class="payment-method-icon">
                                                            @if($method === 'stripe')
                                                                <div class="payment-icon-wrapper stripe">
                                                                    <i class="mdi mdi-credit-card"></i>
                                                                </div>
                                                            @elseif($method === 'valor')
                                                                <div class="payment-icon-wrapper valor">
                                                                    <i class="mdi mdi-credit-card-outline"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="payment-method-details">
                                                            <h6 class="payment-method-name">{{ $config['name'] }}</h6>
                                                            <p class="payment-method-description">{{ $config['description'] }}</p>
                                                        </div>
                                                        <div class="payment-method-badges">
                                                            @if($method === 'stripe')
                                                                <span class="payment-badge">Visa</span>
                                                                <span class="payment-badge">Mastercard</span>
                                                                <span class="payment-badge">Amex</span>
                                                            @elseif($method === 'valor')
                                                                <svg width="50" height="40" viewBox="0 0 100 60">
                                                                    <circle cx="40" cy="30" r="20" fill="#EB001B"/>
                                                                    <circle cx="60" cy="30" r="20" fill="#F79E1B"/>
                                                                    <text x="50" y="35" text-anchor="middle" fill="white" font-size="10" font-family="Arial" font-weight="bold">MC</text>
                                                                </svg>

                                                                <!-- Visa -->
                                                                <svg width="50" height="40" viewBox="0 0 100 60">
                                                                    <rect width="100" height="60" fill="#1A1F71"/>
                                                                    <text x="50" y="38" text-anchor="middle" fill="white" font-size="20" font-family="Arial" font-weight="bold">VISA</text>
                                                                </svg>

                                                                <!-- Amex -->
                                                                <svg width="50" height="40" viewBox="0 0 100 60">
                                                                    <rect width="100" height="60" fill="#2E77BC"/>
                                                                    <text x="50" y="38" text-anchor="middle" fill="white" font-size="12" font-family="Arial" font-weight="bold">AMEX</text>
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Payment Forms Container -->
                        <div class="payment-forms-container">
                            <!-- Stripe Payment Form -->
                            <div id="stripe-form" class="payment-form-modern" style="display: none;">
                                <div class="payment-form-header">
                                    <h6 class="form-title">
                                        <i class="mdi mdi-credit-card me-2"></i>Credit/Debit Card Information
                                    </h6>
                                </div>
                                <form id="stripe-payment-form" class="payment-form-content">
                                    <div class="card-input-container">
                                        <div id="stripe-card-element" class="card-element">
                                            <!-- Stripe Elements will create form elements here -->
                                        </div>
                                    </div>
                                    <div id="stripe-card-errors" class="payment-error-message"></div>
                                    <button type="submit" id="stripe-submit" class="btn btn-primary invoice-action-btn btn-sm">
                                        <span id="stripe-button-text">
                                            <i class="mdi mdi-lock me-2"></i>Pay $<span id="stripe-amount">{{ number_format($invoice->balance, 2) }}</span>
                                        </span>
                                        <div id="stripe-spinner" class="payment-spinner" style="display: none;"></div>
                                    </button>
                                </form>
                            </div>

                            <!-- Valor Payment Form -->
                            <div id="valor-form" class="payment-form-modern" style="display: none;">
                                <div class="payment-form-header">
                                    <h6 class="form-title">
                                        <i class="mdi mdi-credit-card-outline me-2"></i>Valor Payment
                                    </h6>
                                </div>
                                <div class="payment-form-content">
                                    <form id="valor-payment-form">
                                        <div class="card-input-container">
                                            <div id="valor-card-element" class="card-element">
                                                <!-- Valor Passage.js will create form elements here -->
                                            </div>
                                        </div>
                                        <div id="valor-card-errors" class="payment-error-message"></div>
                                        <button type="submit" id="valor-submit" class="btn btn-primary invoice-action-btn btn-sm">
                                            <span id="valor-button-text">
                                                <i class="mdi mdi-lock me-2"></i>Pay $<span id="valor-amount">{{ number_format($invoice->balance, 2) }}</span>
                                            </span>
                                            <div id="valor-spinner" class="payment-spinner" style="display: none;"></div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Card -->
                
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Invoice Summary Card -->
                <div class="card invoice-stats-card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0 font-weight-bold">
                            <i class="mdi mdi-file-document ml-2"></i>Invoice Summary
                        </h6>
                    </div>
                        <div class="invoice-summary-content">
                            <div class="row">
                                <div class="col-12">
                                    <table class="invoice-details-table table table-borderless">
                                        <tr>
                                            <td class="label">Invoice Number:</td>
                                            <td class="value">{{ $invoice->invoice_number }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Due Date:</td>
                                            <td class="value {{ $invoice->is_overdue ? 'text-danger font-weight-bold' : '' }}">
                                                {{ $invoice->due_date->format('M d, Y') }}
                                                @if($invoice->is_overdue)
                                                    <br><small>({{ abs($invoice->days_until_due) }} days overdue)</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($invoice->title)
                                        <tr>
                                            <td class="label">Description:</td>
                                            <td class="value">{{ $invoice->title }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="label">Invoice Type:</td>
                                            <td class="value">
                                                @if($invoice->type === 'uploaded_pdf')
                                                    <i class="mdi mdi-file-pdf-box text-danger me-1"></i>Uploaded PDF
                                                @elseif($invoice->type === 'quick')
                                                    <i class="mdi mdi-lightning-bolt text-success me-1"></i>Quick Invoice
                                                @else
                                                    <i class="mdi mdi-format-list-bulleted text-info me-1"></i>Detailed Invoice
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- Payment Processing Modal -->
<div class="modal fade" id="processingModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                <h5>Processing Payment...</h5>
                <p class="text-muted mb-0">Please do not close this window or navigate away.</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>Payment Error
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="error-message">An error occurred while processing your payment.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="retryPayment()">Try Again</button>
            </div>
        </div>
    </div>
</div>


<style>
.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    background: white;
}

.payment-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(117, 113, 249, 0.15);
    border-color: #00BBE0 !important;
}

.payment-option.selected {
    border-color: #00BBE0 !important;
    box-shadow: 0 0 0 3px rgba(117, 113, 249, 0.25);
    background: rgba(117, 113, 249, 0.05);
}

/* Payment methods container */
#payment-methods {
    margin-bottom: 2rem;
}

/* Enhanced payment method visibility */
.payment-method-card {
    margin-bottom: 1rem;
}

.payment-method-card:last-child {
    margin-bottom: 0;
}

.payment-icons {
    opacity: 0.8;
}

.payment-form {
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-radius: 10px;
    background: #f8f9fa;
    padding: 1.5rem;
}

#stripe-card-element {
    background: white;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.invoice-details-table td.label {
    font-weight: 600;
    color: #6c757d;
    padding-right: 1rem;
}

.invoice-details-table td.value {
    color: #495057;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #00BBE0 0%, #57c7d4 100%) !important;
}

.text-danger {
    color: #f96868 !important;
}

.text-success {
    color: #46c35f !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-primary {
    color: #00BBE0 !important;
}

.bg-danger {
    background-color: #f96868 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

/* Form validation styles */
.is-invalid {
    border-color: #f96868 !important;
    box-shadow: 0 0 0 0.2rem rgba(249, 104, 104, 0.25) !important;
}

.invalid-feedback {
    color: #f96868;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

/* Loading states */
.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Payment form improvements */
.payment-form {
    animation: fadeIn 0.3s ease-in;
}

.payment-method-card {
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    transform: translateY(-1px);
}

/* Error handling */
.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .col-lg-8, .col-lg-4 {
        margin-bottom: 2rem;
    }

    .payment-form {
        margin-top: 1rem;
        padding: 1rem;
    }

    .card-header h6 {
        font-size: 0.9rem;
    }
}

@media (max-width: 767.98px) {
    .payment-option:hover {
        transform: none;
    }

    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    .display-4 {
        font-size: 2rem;
    }
}

@media (max-width: 576px) {
    .payment-option .card-body {
        padding: 1rem;
    }

    .payment-icons {
        font-size: 1.25rem;
    }

    .h2 {
        font-size: 1.75rem;
    }

    .btn-lg {
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
    }

    .payment-method-card .card-body {
        padding: 1rem !important;
    }

    .form-check-input {
        transform: scale(1.1) !important;
    }
}

/* Modern Payment Options Design */
.payment-option-modern {
    margin-bottom: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #fff;
    position: relative;
    overflow: hidden;
}

.payment-option-modern:hover {
    border-color: #d4d4d4ff;
    box-shadow: 0 4px 12px rgba(117, 113, 249, 0.15);
    transform: translateY(-2px);
}

.payment-option-modern.selected {
    /* border-color: #00BBE0; */
    background: linear-gradient(135deg, #f8f7ff 0%, #fff 100%);
    box-shadow: 0 6px 20px rgba(117, 113, 249, 0.2);
}

.payment-option-content {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    position: relative;
}

.payment-option-radio {
    position: relative;
    margin-right: 1rem;
    flex-shrink: 0;
}

.payment-radio {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.radio-checkmark {
    position: relative;
    height: 22px;
    width: 22px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 50%;
    display: block;
    transition: all 0.3s ease;
}

.payment-radio:checked ~ .radio-checkmark {
    background-color: #00BBE0;
    border-color: #00BBE0;
}

.radio-checkmark:after {
    content: "";
    position: absolute;
    display: none;
    top: 50%;
    left: 50%;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
    transform: translate(-50%, -50%);
}

.payment-radio:checked ~ .radio-checkmark:after {
    display: block;
}

.payment-option-label {
    flex: 1;
    cursor: pointer;
    margin: 0;
    display: flex;
    align-items: center;
}

.payment-option-info {
    display: flex;
    align-items: center;
    width: 100%;
}

.payment-method-icon {
    margin-right: 1rem;
    flex-shrink: 0;
}

.payment-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.payment-icon-wrapper.stripe {
    background: linear-gradient(135deg, #635bff 0%, #5469d4 100%);
    color: white;
}

.payment-icon-wrapper.paypal {
    background: linear-gradient(135deg, #0070ba 0%, #003087 100%);
    color: white;
}

.payment-icon-wrapper.valor {
    background: linear-gradient(-1deg, #06b96d 50%, #011ca3 50%);
    color: white;
}

.payment-icon-wrapper i {
    font-size: 24px;
}

.payment-method-details {
    flex: 1;
}

.payment-method-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.payment-method-description {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.4;
}

.payment-method-badges {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.payment-badge {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-badge.secure {
    background: #d4edda;
    color: #155724;
}

/* Payment Amount Section */
.payment-amount-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.payment-amount-section h5 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-weight: 600;
}

.amount-input-wrapper {
    position: relative;
    margin-bottom: 1rem;
}

.amount-input-wrapper .input-group-text {
    background: #00BBE0;
    color: white;
    border: none;
    font-weight: 600;
}

.amount-input-wrapper input {
    border: 2px solid #e9ecef;
    border-left: none;
    padding: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
}

.amount-input-wrapper input:focus {
    border-color: #00BBE0;
    box-shadow: 0 0 0 0.2rem rgba(117, 113, 249, 0.25);
}

/* Quick amount buttons removed - full payment only */

/* Payment Forms */
.payment-form {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid #e9ecef;
    margin-top: 1rem;
}

.payment-form h6 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.payment-form .form-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.payment-form .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.payment-form .form-control:focus {
    border-color: #00BBE0;
    box-shadow: 0 0 0 0.2rem rgba(117, 113, 249, 0.25);
}

.security-badges {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
    align-items: center;
    justify-content: center;
}

.security-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #28a745;
    font-size: 0.85rem;
    font-weight: 500;
}

.btn-pay-now {
    background: linear-gradient(135deg, #00BBE0 0%, #5a54d6 100%);
    border: none;
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-pay-now:hover {
    background: linear-gradient(135deg, #5a54d6 0%, #4c46b8 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(117, 113, 249, 0.3);
}

.btn-pay-now:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
    .payment-container {
        padding: 0 0.5rem;
    }

    .payment-content {
        margin: 0 -0.5rem;
    }

    .payment-option-content {
        padding: 1rem;
    }

    .payment-method-icon {
        margin-right: 0.75rem;
    }

    .payment-icon-wrapper {
        width: 40px;
        height: 40px;
    }

    .payment-icon-wrapper i {
        font-size: 20px;
    }

    .payment-method-name {
        font-size: 1rem;
    }

    .payment-method-description {
        font-size: 0.85rem;
    }

    /* Quick amount buttons removed - full payment only */

    .payment-form {
        padding: 1rem;
    }

    .security-badges {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-pay-now {
        width: 100%;
        padding: 1rem;
    }

    .payment-sidebar {
        margin-top: 1rem;
    }
}

@media (max-width: 576px) {
    .payment-option-content {
        padding: 0.75rem;
    }

    .payment-amount-section {
        padding: 1rem;
    }

    .payment-form {
        padding: 0.75rem;
    }

    /* Quick amount buttons removed - full payment only */
}

/* Back Button Styling */
.btn-back-header {
    background: linear-gradient(135deg, #00BBE0 0%, #5a54d6 100%);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-back-header:hover {
    background: linear-gradient(135deg, #5a54d6 0%, #4c46b8 100%);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(117, 113, 249, 0.3);
}

.btn-back {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    color: #495057;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    color: #495057;
    text-decoration: none;
}

/* Payment Form Improvements */
.payment-form-modern {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    border: 2px solid #e9ecef;
    margin-top: 1rem;
}

.payment-form-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.form-title {
    color: #2c3e50;
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.payment-form-content {
    margin-top: 1rem;
}

.card-input-container {
    margin-bottom: 1rem;
}

.card-element {
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.card-element.StripeElement--focus {
    border-color: #00BBE0;
    box-shadow: 0 0 0 0.2rem rgba(117, 113, 249, 0.25);
}

.payment-error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    min-height: 1.25rem;
}

.payment-submit-btn {
    background: linear-gradient(135deg, #00BBE0 0%, #5a54d6 100%);
    border: none;
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.payment-submit-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #5a54d6 0%, #4c46b8 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(117, 113, 249, 0.3);
}

.payment-submit-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.payment-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Payment Navigation */
.payment-navigation {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
    text-align: center;
}

.payment-logo {
  height: 40px;
  width: auto;
  object-fit: contain;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  background: #fff;
  padding: 4px;
}

</style>

<!-- Include Stripe JS -->
@if($paymentMethods['stripe']['enabled'])
    <script src="https://js.stripe.com/v3/"></script>
@endif

<!-- Include Valor Passage JS -->
@if($paymentMethods['valor']['enabled'])
    <script src="https://js.valorpaytech.com/V1/js/Passage.min.js"
            data-name="valor_passage"
            data-clientToken="{{ config('services.valor.client_token') }}"
            data-epi="{{ config('services.valor.epi') }}"></script>
@endif




<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceId = {{ $invoice->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const maxAmount = {{ $invoice->balance }};
    let currentPaymentMethod = null;
    let isProcessing = false;

    // Validation functions
    function validateAmount() {
        // Amount is fixed to invoice balance - no validation needed
        return true;
    }

    function validatePaymentMethod() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            showError('Please select a payment method');
            return false;
        }
        return true;
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    function showError(message) {
        document.getElementById('error-message').textContent = message;
        $('#errorModal').modal('show');
    }

    // Payment method selection
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            currentPaymentMethod = this.value;

            // Remove selected class from all cards
            document.querySelectorAll('.payment-option-modern').forEach(function(card) {
                card.classList.remove('selected');
            });

            // Add selected class to chosen card
            this.closest('.payment-option-modern').classList.add('selected');

            // Hide all payment forms
            document.querySelectorAll('.payment-form-modern').forEach(function(form) {
                form.style.display = 'none';
            });

            // Show selected payment form
            const formElement = document.getElementById(this.value + '-form');
            if (formElement) {
                formElement.style.display = 'block';
            }

            // Initialize payment method specific code
            if (this.value === 'stripe') {
                setTimeout(() => {
                    initializeStripe();
                }, 100);
            } else if (this.value === 'paypal') {
                initializePayPal();
            } else if (this.value === 'valor') {
                setTimeout(() => {
                    initializeValor();
                }, 100);
            }
        });
    });

    // Payment amount is fixed - update displays
    const fixedAmount = {{ $invoice->balance }};
    document.querySelectorAll('[id$="-amount"]').forEach(function(el) {
        el.textContent = fixedAmount.toFixed(2);
    });

    @if($paymentMethods['stripe']['enabled'])
    // Stripe initialization
    let stripe, elements, cardElement;

    function initializeStripe() {
        if (stripe) return; // Already initialized

        const stripeKey = '{{ config("services.stripe.key") }}';
        if (!stripeKey) {
            console.error('Stripe public key not configured');
            document.getElementById('stripe-card-errors').textContent = 'Payment system not properly configured. Please contact support.';
            return;
        }

        stripe = Stripe(stripeKey);
        elements = stripe.elements();

        cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
            },
        });

        cardElement.mount('#stripe-card-element');

        cardElement.on('change', function(event) {
            const displayError = document.getElementById('stripe-card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        document.getElementById('stripe-payment-form').addEventListener('submit', handleStripeSubmit);
    }

    async function handleStripeSubmit(event) {
        event.preventDefault();

        if (isProcessing) {
            return;
        }

        if (!validateAmount() || !validatePaymentMethod()) {
            return;
        }

        const submitButton = document.getElementById('stripe-submit');
        const spinner = document.getElementById('stripe-spinner');
        const buttonText = document.getElementById('stripe-button-text');

        isProcessing = true;
        submitButton.disabled = true;
        spinner.style.display = 'inline-block';

        try {
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page and try again.');
            }

            const amount = {{ $invoice->balance }}; // Fixed full payment amount

            if (!stripe || !cardElement) {
                throw new Error('Payment system not properly initialized. Please refresh the page and try again.');
            }

            // Create payment intent
            const response = await fetch(`/account/agency/invoices/${invoiceId}/create-stripe-intent`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ amount: amount })
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Server error (${response.status}): ${errorText}`);
            }

            const intentResponse = await response.json();

            if (!intentResponse.success) {
                throw new Error(intentResponse.message || 'Failed to create payment intent');
            }

            const { client_secret } = intentResponse;

            // Confirm payment with Stripe
            const { error, paymentIntent } = await stripe.confirmCardPayment(client_secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: '{{ auth()->user()->name ?? "" }}',
                        email: '{{ auth()->user()->email ?? "" }}'
                    }
                }
            });

            if (error) {
                throw new Error(error.message);
            }

            // Payment succeeded, now process on backend
            showProcessingModal();

            const processResponse = await fetch(`/account/agency/invoices/${invoiceId}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: 'stripe',
                    payment_intent_id: paymentIntent.id,
                    amount: amount
                })
            });

            if (!processResponse.ok) {
                const errorData = await processResponse.json().catch(() => ({}));
                throw new Error(errorData.message || `Server error: ${processResponse.status}`);
            }

            const result = await processResponse.json();

            if (result.success) {
                // Redirect to success page
                setTimeout(() => {
                    window.location.href = result.redirect_url || `/account/agency/invoices/${invoiceId}`;
                }, 1000);
            } else {
                throw new Error(result.message || 'Payment processing failed');
            }

        } catch (error) {
            console.error('Payment error:', error);
            hideProcessingModal();

            // Show error in card errors element
            const errorElement = document.getElementById('stripe-card-errors');
            if (errorElement) {
                errorElement.textContent = error.message;
            } else {
                showError(error.message);
            }

            isProcessing = false;
            submitButton.disabled = false;
            spinner.style.display = 'none';
        }
    }
    @endif

    @if($paymentMethods['valor']['enabled'])
    // Valor initialization
    let valorPassage;
    let valorInitialized = false;

    function initializeValor() {
        if (valorInitialized) return; // Already initialized

        // Wait for Passage.js to load
        if (typeof Passage === 'undefined') {
            console.error('Valor Passage.js not loaded');
            document.getElementById('valor-card-errors').textContent = 'Payment system not properly configured. Please contact support.';
            return;
        }

        try {
            // Initialize Passage
            valorPassage = new Passage();

            // Mount Passage elements to the form
            valorPassage.create('card-number', {
                placeholder: 'Card Number',
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#424770',
                        '::placeholder': {
                            color: '#aab7c4',
                        },
                    },
                }
            }, 'valor-card-element');

            valorInitialized = true;

            document.getElementById('valor-payment-form').addEventListener('submit', handleValorSubmit);
        } catch (error) {
            console.error('Valor initialization error:', error);
            document.getElementById('valor-card-errors').textContent = 'Failed to initialize payment system: ' + error.message;
        }
    }

    async function handleValorSubmit(event) {
        event.preventDefault();

        if (isProcessing) {
            return;
        }

        if (!validateAmount() || !validatePaymentMethod()) {
            return;
        }

        const submitButton = document.getElementById('valor-submit');
        const spinner = document.getElementById('valor-spinner');
        const buttonText = document.getElementById('valor-button-text');

        isProcessing = true;
        submitButton.disabled = true;
        spinner.style.display = 'inline-block';

        try {
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page and try again.');
            }

            const amount = {{ $invoice->balance }}; // Fixed full payment amount

            if (!valorPassage) {
                throw new Error('Payment system not properly initialized. Please refresh the page and try again.');
            }

            // Create token with Valor Passage
            valorPassage.createToken(async function(error, response) {
                if (error) {
                    throw new Error(error.message || 'Failed to tokenize payment information');
                }

                if (!response || !response.token) {
                    throw new Error('Invalid payment token received');
                }

                try {
                    // Show processing modal
                    showProcessingModal();

                    // Process payment on backend
                    const processResponse = await fetch(`/account/agency/invoices/${invoiceId}/pay`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_method: 'valor',
                            valor_token: response.token,
                            amount: amount
                        })
                    });

                    if (!processResponse.ok) {
                        const errorData = await processResponse.json().catch(() => ({}));
                        throw new Error(errorData.message || `Server error: ${processResponse.status}`);
                    }

                    const result = await processResponse.json();

                    if (result.success) {
                        // Redirect to success page
                        setTimeout(() => {
                            window.location.href = result.redirect_url || `/account/agency/invoices/${invoiceId}`;
                        }, 1000);
                    } else {
                        throw new Error(result.message || 'Payment processing failed');
                    }

                } catch (error) {
                    console.error('Payment error:', error);
                    hideProcessingModal();

                    // Show error in card errors element
                    const errorElement = document.getElementById('valor-card-errors');
                    if (errorElement) {
                        errorElement.textContent = error.message;
                    } else {
                        showError(error.message);
                    }

                    isProcessing = false;
                    submitButton.disabled = false;
                    spinner.style.display = 'none';
                }
            });

        } catch (error) {
            console.error('Payment error:', error);
            hideProcessingModal();

            // Show error in card errors element
            const errorElement = document.getElementById('valor-card-errors');
            if (errorElement) {
                errorElement.textContent = error.message;
            } else {
                showError(error.message);
            }

            isProcessing = false;
            submitButton.disabled = false;
            spinner.style.display = 'none';
        }
    }
    @endif


    function showProcessingModal() {
        $('#processingModal').modal('show');
    }

    function hideProcessingModal() {
        $('#processingModal').modal('hide');
    }

    // Global retry function
    window.retryPayment = function() {
        $('#errorModal').modal('hide');

        // Reset processing state
        isProcessing = false;

        // Re-enable submit button
        const submitButton = document.getElementById('stripe-submit');
        if (submitButton) {
            submitButton.disabled = false;
            document.getElementById('stripe-spinner').style.display = 'none';
        }

        // Clear any error messages
        const errorElement = document.getElementById('stripe-card-errors');
        if (errorElement) {
            errorElement.textContent = '';
        }
    };

    // Quick amount buttons removed - full payment only

    // Initialize first available payment method
    const firstPaymentMethod = document.querySelector('input[name="payment_method"]');
    if (firstPaymentMethod) {
        firstPaymentMethod.checked = true;
        firstPaymentMethod.dispatchEvent(new Event('change'));
    }

    // Add form submission handler to prevent default form submit
    const paymentForms = document.querySelectorAll('.payment-form form');
    paymentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    });
});
</script>

@include('include/footer')