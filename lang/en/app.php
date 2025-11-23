<?php

return [
    // General
    'welcome' => 'Welcome to Contaplus!',
    'dashboard' => 'Dashboard',
    'logout' => 'Logout',
    'login' => 'Login',
    'register' => 'Register',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'view' => 'View',
    'back' => 'Back',
    'search' => 'Search',
    'filter' => 'Filter',
    'export' => 'Export',
    'import' => 'Import',
    'upload' => 'Upload',
    'download' => 'Download',
    'actions' => 'Actions',
    'settings' => 'Settings',
    'profile' => 'Profile',
    'help' => 'Help',
    'support' => 'Support',
    'documentation' => 'Documentation',
    'language' => 'Language',
    'currency' => 'Currency',
    'country' => 'Country',

    // Navigation
    'nav' => [
        'home' => 'Home',
        'features' => 'Features',
        'pricing' => 'Pricing',
        'blog' => 'Blog',
        'contact' => 'Contact',
        'documents' => 'Documents',
        'transactions' => 'Transactions',
        'entities' => 'Entities',
        'reports' => 'Reports',
        'admin' => 'Admin',
    ],

    // Dashboard
    'stats' => [
        'total_documents' => 'Total Documents',
        'pending_documents' => 'Pending Documents',
        'processing_documents' => 'Processing Documents',
        'total_transactions' => 'Transactions',
        'this_month' => 'This Month',
        'total_entities' => 'Fiscal Entities',
        'documents_usage' => 'Documents This Month',
        'usage_percentage' => ':percentage% used',
        'remaining' => ':count documents remaining',
    ],

    // Documents
    'documents' => [
        'title' => 'Documents',
        'upload' => 'Upload Document',
        'upload_new' => 'Upload New Document',
        'type' => 'Document Type',
        'date' => 'Date',
        'status' => 'Status',
        'actions' => 'Actions',
        'no_documents' => 'No documents available',
        'invoice' => 'Invoice',
        'receipt' => 'Receipt',
        'bank_statement' => 'Bank Statement',
        'tax_document' => 'Tax Document',
        'other' => 'Other',
    ],

    // Alerts
    'alerts' => [
        'limit_reached' => 'Document Limit Reached',
        'near_limit' => 'Approaching Document Limit',
        'limit_reached_message' => 'You have reached the limit of :limit documents for this month. You will not be able to process more documents until next month or until you upgrade your plan.',
        'near_limit_message' => 'You have used :used of :limit documents this month (:percentage%). Consider upgrading your plan to avoid interruptions.',
        'upgrade_plan' => 'Upgrade Plan',
        'view_plans' => 'View Plans',
        'limit_blocked' => 'Limit reached',
    ],

    // Admin
    'admin' => [
        'dashboard' => 'Administration Dashboard',
        'manage_clients' => 'Manage Clients',
        'manage_blog' => 'Blog',
        'company_settings' => 'Company Settings',
        'profile_settings' => 'Profile Settings',
        'blog_settings' => 'Blog Settings',
        'email_settings' => 'Email Settings',
        'total_clients' => 'Total Clients',
        'active_clients' => ':count active',
        'total_users' => 'Total Users',
        'docs_this_month' => 'Docs This Month',
        'recent_clients' => 'Recent Clients',
    ],

    // Emails
    'email' => [
        'welcome_subject' => 'Welcome to Contaplus!',
        'password_reset_subject' => 'Reset your password - Contaplus',
        'document_limit_subject' => '⚠️ Approaching document limit - Contaplus',
    ],

    // Auth
    'auth' => [
        'login_title' => 'Login',
        'register_title' => 'Create Account',
        'email' => 'Email',
        'password' => 'Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot your password?',
        'no_account' => "Don't have an account?",
        'already_have_account' => 'Already have an account?',
        'sign_up' => 'Sign up',
        'sign_in' => 'Sign in',
    ],

    // Pricing
    'pricing' => [
        'title' => 'Plans & Pricing',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'per_month' => '/month',
        'per_year' => '/year',
        'documents' => 'documents/month',
        'users' => 'users',
        'support' => 'Support',
        'choose_plan' => 'Choose Plan',
        'current_plan' => 'Current Plan',
    ],
];
