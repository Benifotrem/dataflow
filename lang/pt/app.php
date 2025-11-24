<?php

return [
    // General
    'welcome' => 'Bem-vindo ao Dataflow!',
    'dashboard' => 'Painel de Controle',
    'logout' => 'Sair',
    'login' => 'Entrar',
    'register' => 'Registrar',
    'save' => 'Salvar',
    'cancel' => 'Cancelar',
    'delete' => 'Excluir',
    'edit' => 'Editar',
    'view' => 'Ver',
    'back' => 'Voltar',
    'search' => 'Buscar',
    'filter' => 'Filtrar',
    'export' => 'Exportar',
    'import' => 'Importar',
    'upload' => 'Carregar',
    'download' => 'Baixar',
    'actions' => 'Ações',
    'settings' => 'Configurações',
    'profile' => 'Perfil',
    'help' => 'Ajuda',
    'support' => 'Suporte',
    'documentation' => 'Documentação',
    'language' => 'Idioma',
    'currency' => 'Moeda',
    'country' => 'País',

    // Navigation
    'nav' => [
        'home' => 'Início',
        'features' => 'Recursos',
        'pricing' => 'Preços',
        'blog' => 'Blog',
        'contact' => 'Contato',
        'documents' => 'Documentos',
        'transactions' => 'Transações',
        'entities' => 'Entidades',
        'reports' => 'Relatórios',
        'admin' => 'Administração',
    ],

    // Dashboard
    'stats' => [
        'total_documents' => 'Documentos Totais',
        'pending_documents' => 'Documentos Pendentes',
        'processing_documents' => 'Documentos em Processamento',
        'total_transactions' => 'Transações',
        'this_month' => 'Este Mês',
        'total_entities' => 'Entidades Fiscais',
        'documents_usage' => 'Documentos Este Mês',
        'usage_percentage' => ':percentage% utilizado',
        'remaining' => 'Restam :count documentos',
    ],

    // Documents
    'documents' => [
        'title' => 'Documentos',
        'upload' => 'Carregar Documento',
        'upload_new' => 'Carregar Novo Documento',
        'type' => 'Tipo de Documento',
        'date' => 'Data',
        'status' => 'Status',
        'actions' => 'Ações',
        'no_documents' => 'Não há documentos disponíveis',
        'invoice' => 'Fatura',
        'receipt' => 'Recibo',
        'bank_statement' => 'Extrato Bancário',
        'tax_document' => 'Documento Fiscal',
        'other' => 'Outro',
    ],

    // Alerts
    'alerts' => [
        'limit_reached' => 'Limite de Documentos Atingido',
        'near_limit' => 'Aproximando-se do Limite de Documentos',
        'limit_reached_message' => 'Você atingiu o limite de :limit documentos para este mês. Não será possível processar mais documentos até o próximo mês ou até que você atualize seu plano.',
        'near_limit_message' => 'Você utilizou :used de :limit documentos este mês (:percentage%). Considere atualizar seu plano para evitar interrupções.',
        'upgrade_plan' => 'Atualizar Plano',
        'view_plans' => 'Ver Planos',
        'limit_blocked' => 'Limite atingido',
    ],

    // Admin
    'admin' => [
        'dashboard' => 'Painel de Administração',
        'manage_clients' => 'Gerenciar Clientes',
        'manage_blog' => 'Blog',
        'company_settings' => 'Configurações da Empresa',
        'profile_settings' => 'Configurações do Perfil',
        'blog_settings' => 'Configurações do Blog',
        'email_settings' => 'Configurações de Email',
        'total_clients' => 'Total de Clientes',
        'active_clients' => ':count ativos',
        'total_users' => 'Total de Usuários',
        'docs_this_month' => 'Docs Este Mês',
        'recent_clients' => 'Clientes Recentes',
    ],

    // Emails
    'email' => [
        'welcome_subject' => 'Bem-vindo ao Dataflow!',
        'password_reset_subject' => 'Recupere sua senha - Dataflow',
        'document_limit_subject' => '⚠️ Aproximando-se do limite de documentos - Dataflow',
    ],

    // Auth
    'auth' => [
        'login_title' => 'Entrar',
        'register_title' => 'Criar Conta',
        'email' => 'Email',
        'password' => 'Senha',
        'remember_me' => 'Lembrar-me',
        'forgot_password' => 'Esqueceu sua senha?',
        'no_account' => 'Não tem uma conta?',
        'already_have_account' => 'Já tem uma conta?',
        'sign_up' => 'Registre-se',
        'sign_in' => 'Entre',
    ],

    // Pricing
    'pricing' => [
        'title' => 'Planos e Preços',
        'monthly' => 'Mensal',
        'yearly' => 'Anual',
        'per_month' => '/mês',
        'per_year' => '/ano',
        'documents' => 'documentos/mês',
        'users' => 'usuários',
        'support' => 'Suporte',
        'choose_plan' => 'Escolher Plano',
        'current_plan' => 'Plano Atual',
    ],
];
