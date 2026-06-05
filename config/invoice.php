<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invoice Module Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Invoice Module including payment gateways,
    | notification settings, and other module-specific settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | This information will be used in PDF invoices and email templates.
    | You can also set these as environment variables.
    |
    */
    'company' => [
            'name' => env('COMPANY_NAME', 'NY BEST CARE INC'),
            'address' => env('COMPANY_ADDRESS', '2965 Ocean Pkwy'),
            'city' => env('COMPANY_CITY', 'Brooklyn'),
            'state' => env('COMPANY_STATE', 'NY'),
            'zip' => env('COMPANY_ZIP', '11235'),
            'phone' => env('COMPANY_PHONE', '(718) 972 3693'),
            'email' => env('COMPANY_EMAIL', 'contact@nybestmedical.com'),
            'website' => env('COMPANY_WEBSITE', 'www.nybestmedical.com'),
            'logo' => env('COMPANY_LOGO_PATH', 'img/logo-ny.png'),
            'tax_id' => env('COMPANY_TAX_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | General settings for invoice generation and management.
    |
    */
    'invoice' => [
        // Default currency
        'currency' => env('INVOICE_CURRENCY', 'USD'),

        // Default payment terms (in days)
        'default_payment_terms' => env('INVOICE_DEFAULT_PAYMENT_TERMS', 30),

        // Maximum file size for PDF uploads (in KB)
        'max_pdf_size' => env('INVOICE_MAX_PDF_SIZE', 10240), // 10MB

        // Number of invoices per page in listings
        'per_page' => env('INVOICE_PER_PAGE', 15),

        // Auto-generate invoice numbers
        'auto_generate_numbers' => env('INVOICE_AUTO_GENERATE_NUMBERS', true),

        // Invoice number prefix
        'number_prefix' => env('INVOICE_NUMBER_PREFIX', 'INV'),

        // Late fee settings
        'late_fee_enabled' => env('INVOICE_LATE_FEE_ENABLED', false),
        'late_fee_percentage' => env('INVOICE_LATE_FEE_PERCENTAGE', 1.5), // 1.5% per month
        'late_fee_grace_days' => env('INVOICE_LATE_FEE_GRACE_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for supported payment gateways.
    |
    */
    'payment_gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
            'test_mode' => env('STRIPE_TEST_MODE', true),
        ],
        'valor' => [
            'enabled' => env('VALOR_ENABLED', false),
            'test_mode' => env('VALOR_TEST_MODE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for email notifications and reminders.
    |
    */
    'notifications' => [
        // Email notifications
        'send_invoice_emails' => env('INVOICE_SEND_EMAILS', true),
        'send_payment_receipts' => env('INVOICE_SEND_RECEIPTS', true),
        'send_reminders' => env('INVOICE_SEND_REMINDERS', true),

        // Admin notification email for payments
        'admin_notification_email' => env('INVOICE_ADMIN_EMAIL', null),

        // Reminder settings
        'reminder_days' => env('INVOICE_REMINDER_DAYS', 3), // Days before due date
        'overdue_reminder_frequency' => env('INVOICE_OVERDUE_FREQUENCY', 7), // Days between overdue reminders

        // Email from address
        'from_email' => env('INVOICE_FROM_EMAIL', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('INVOICE_FROM_NAME', env('MAIL_FROM_NAME')),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for invoice and receipt file storage.
    |
    */
    'storage' => [
        // Storage disk for invoices
        'disk' => env('INVOICE_STORAGE_DISK', 'public'),

        // Directory for invoice PDFs
        'invoice_path' => 'invoices',

        // Directory for receipts
        'receipt_path' => 'receipts',

        // Keep generated PDFs after sending
        'keep_generated_pdfs' => env('INVOICE_KEEP_PDFS', true),

        // Delete uploaded PDFs when invoice is deleted
        'delete_pdfs_on_deletion' => env('INVOICE_DELETE_PDFS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Generation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF generation using DomPDF.
    |
    */
    'pdf' => [
        // Paper size (A4, Letter, etc.)
        'paper_size' => env('INVOICE_PDF_PAPER_SIZE', 'A4'),

        // Paper orientation (portrait, landscape)
        'orientation' => env('INVOICE_PDF_ORIENTATION', 'portrait'),

        // DPI for images
        'dpi' => env('INVOICE_PDF_DPI', 96),

        // Enable remote content (for external images/fonts)
        'enable_remote' => env('INVOICE_PDF_ENABLE_REMOTE', false),

        // Custom CSS for PDF styling
        'custom_css' => env('INVOICE_PDF_CUSTOM_CSS', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    |
    | Default tax configuration for invoices.
    |
    */
    'tax' => [
        // Default tax rate (percentage)
        'default_rate' => env('INVOICE_DEFAULT_TAX_RATE', 0),

        // Tax label (e.g., "VAT", "GST", "Sales Tax")
        'label' => env('INVOICE_TAX_LABEL', 'Tax'),

        // Include tax in item prices
        'inclusive' => env('INVOICE_TAX_INCLUSIVE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration for the invoice module.
    |
    */
    'security' => [
        // Enable CSRF protection for all forms
        'csrf_protection' => true,

        // Rate limiting for payment attempts
        'payment_rate_limit' => env('INVOICE_PAYMENT_RATE_LIMIT', '10,1'), // 10 attempts per minute

        // Enable audit logging
        'audit_logging' => env('INVOICE_AUDIT_LOGGING', true),

        // Allowed file types for uploads
        'allowed_file_types' => ['pdf'],

        // Maximum file size for uploads (bytes)
        'max_upload_size' => 10 * 1024 * 1024, // 10MB
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for API endpoints and rate limiting.
    |
    */
    'api' => [
        // Enable API endpoints
        'enabled' => env('INVOICE_API_ENABLED', false),

        // API rate limiting
        'rate_limit' => env('INVOICE_API_RATE_LIMIT', '60,1'), // 60 requests per minute

        // API authentication
        'auth_required' => env('INVOICE_API_AUTH_REQUIRED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Settings for integrating with other systems.
    |
    */
    'integrations' => [
        // Webhook URLs for external systems
        'webhooks' => [
            'payment_received' => env('INVOICE_WEBHOOK_PAYMENT_RECEIVED', null),
            'invoice_created' => env('INVOICE_WEBHOOK_INVOICE_CREATED', null),
            'invoice_overdue' => env('INVOICE_WEBHOOK_INVOICE_OVERDUE', null),
        ],

        // External accounting system integration
        'accounting_system' => [
            'enabled' => env('INVOICE_ACCOUNTING_INTEGRATION', false),
            'system' => env('INVOICE_ACCOUNTING_SYSTEM', null), // 'quickbooks', 'xero', etc.
        ],
    ],

];