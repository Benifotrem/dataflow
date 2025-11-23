<?php

return [
    // General
    'welcome' => '¡Bienvenido a Contaplus!',
    'dashboard' => 'Panel de Control',
    'logout' => 'Cerrar Sesión',
    'login' => 'Iniciar Sesión',
    'register' => 'Registrarse',
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'delete' => 'Eliminar',
    'edit' => 'Editar',
    'view' => 'Ver',
    'back' => 'Volver',
    'search' => 'Buscar',
    'filter' => 'Filtrar',
    'export' => 'Exportar',
    'import' => 'Importar',
    'upload' => 'Subir',
    'download' => 'Descargar',
    'actions' => 'Acciones',
    'settings' => 'Configuración',
    'profile' => 'Perfil',
    'help' => 'Ayuda',
    'support' => 'Soporte',
    'documentation' => 'Documentación',
    'language' => 'Idioma',
    'currency' => 'Moneda',
    'country' => 'País',

    // Navigation
    'nav' => [
        'home' => 'Inicio',
        'features' => 'Características',
        'pricing' => 'Precios',
        'blog' => 'Blog',
        'contact' => 'Contacto',
        'documents' => 'Documentos',
        'transactions' => 'Transacciones',
        'entities' => 'Entidades',
        'reports' => 'Reportes',
        'admin' => 'Administración',
    ],

    // Dashboard
    'stats' => [
        'total_documents' => 'Documentos Totales',
        'pending_documents' => 'Documentos Pendientes',
        'processing_documents' => 'Documentos en Proceso',
        'total_transactions' => 'Transacciones',
        'this_month' => 'Este Mes',
        'total_entities' => 'Entidades Fiscales',
        'documents_usage' => 'Documentos Este Mes',
        'usage_percentage' => ':percentage% utilizado',
        'remaining' => 'Quedan :count documentos',
    ],

    // Documents
    'documents' => [
        'title' => 'Documentos',
        'upload' => 'Subir Documento',
        'upload_new' => 'Subir Nuevo Documento',
        'type' => 'Tipo de Documento',
        'date' => 'Fecha',
        'status' => 'Estado',
        'actions' => 'Acciones',
        'no_documents' => 'No hay documentos disponibles',
        'invoice' => 'Factura',
        'receipt' => 'Recibo',
        'bank_statement' => 'Extracto Bancario',
        'tax_document' => 'Documento Fiscal',
        'other' => 'Otro',
    ],

    // Alerts
    'alerts' => [
        'limit_reached' => 'Límite de Documentos Alcanzado',
        'near_limit' => 'Acercándote al Límite de Documentos',
        'limit_reached_message' => 'Has alcanzado el límite de :limit documentos para este mes. No podrás procesar más documentos hasta el próximo mes o hasta que actualices tu plan.',
        'near_limit_message' => 'Has utilizado :used de :limit documentos este mes (:percentage%). Considera actualizar tu plan para evitar interrupciones.',
        'upgrade_plan' => 'Actualizar Plan',
        'view_plans' => 'Ver Planes',
        'limit_blocked' => 'Límite alcanzado',
    ],

    // Admin
    'admin' => [
        'dashboard' => 'Panel de Administración',
        'manage_clients' => 'Gestionar Clientes',
        'manage_blog' => 'Blog',
        'company_settings' => 'Configuración Empresa',
        'profile_settings' => 'Configuración Perfil',
        'blog_settings' => 'Configuración Blog',
        'email_settings' => 'Configuración Email',
        'total_clients' => 'Total Clientes',
        'active_clients' => ':count activos',
        'total_users' => 'Total Usuarios',
        'docs_this_month' => 'Docs Este Mes',
        'recent_clients' => 'Clientes Recientes',
    ],

    // Emails
    'email' => [
        'welcome_subject' => '¡Bienvenido a Contaplus!',
        'password_reset_subject' => 'Recupera tu contraseña - Contaplus',
        'document_limit_subject' => '⚠️ Acercándote al límite de documentos - Contaplus',
    ],

    // Auth
    'auth' => [
        'login_title' => 'Iniciar Sesión',
        'register_title' => 'Crear Cuenta',
        'email' => 'Email',
        'password' => 'Contraseña',
        'remember_me' => 'Recordarme',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'no_account' => '¿No tienes cuenta?',
        'already_have_account' => '¿Ya tienes cuenta?',
        'sign_up' => 'Regístrate',
        'sign_in' => 'Inicia sesión',
    ],

    // Pricing
    'pricing' => [
        'title' => 'Planes y Precios',
        'monthly' => 'Mensual',
        'yearly' => 'Anual',
        'per_month' => '/mes',
        'per_year' => '/año',
        'documents' => 'documentos/mes',
        'users' => 'usuarios',
        'support' => 'Soporte',
        'choose_plan' => 'Elegir Plan',
        'current_plan' => 'Plan Actual',
    ],
];
