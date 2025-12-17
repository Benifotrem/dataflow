<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Dataflow') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-purple-800 to-purple-900 text-white transform transition-transform duration-200 lg:translate-x-0 -translate-x-full" id="sidebar">
            {{-- Logo --}}
            <div class="flex items-center justify-between h-16 px-6 bg-purple-900">
                <a href="{{ route('dashboard.index') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Dataflow" class="h-8" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-xl font-bold" style="display: none;">Dataflow</span>
                </a>
                <button class="lg:hidden" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- User Info --}}
            <div class="px-6 py-4 border-b border-purple-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-purple-300 truncate">{{ auth()->user()->tenant->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="px-4 py-6 space-y-2">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('dashboard.index') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('documents.*') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Documentos</span>
                </a>

                <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('transactions.*') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Transacciones</span>
                </a>

                <a href="{{ route('bank-statements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('bank-statements.*') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Extractos Bancarios</span>
                </a>

                <a href="{{ route('entities.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('entities.*') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Entidades Fiscales</span>
                </a>

                <div class="border-t border-purple-700 my-4"></div>

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-purple-700 transition {{ request()->routeIs('admin.*') ? 'bg-purple-700' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Administración</span>
                </a>
                @endif
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="lg:pl-64">
            {{-- Top Navigation --}}
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-4">
                    {{-- Mobile Menu Button --}}
                    <button class="lg:hidden" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Page Title --}}
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>

                    {{-- Right Side: Notifications + User Menu --}}
                    <div class="flex items-center gap-4">
                        {{-- Notifications Bell --}}
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="relative p-2 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">
                                    0
                                </span>
                            </button>

                            {{-- Notifications Dropdown --}}
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden flex flex-col">
                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                                    <h3 class="font-bold text-gray-900">Notificaciones</h3>
                                    <button onclick="markAllAsRead()" class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                                        Marcar todas como leídas
                                    </button>
                                </div>

                                {{-- Notifications List --}}
                                <div id="notifications-list" class="overflow-y-auto flex-1 divide-y divide-gray-100">
                                    {{-- Loading state --}}
                                    <div id="notifications-loading" class="p-8 text-center">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                        <p class="text-sm text-gray-500 mt-2">Cargando notificaciones...</p>
                                    </div>

                                    {{-- Empty state --}}
                                    <div id="notifications-empty" class="hidden p-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-sm text-gray-500 mt-2">No tienes notificaciones</p>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium block text-center">
                                        Ver todas las notificaciones
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- User Menu --}}
                        <div class="relative">
                            <button onclick="toggleUserMenu()" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg px-3 py-2 transition">
                                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configuración</a>
                                <div class="border-t my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Fiscal Assistant Chatbot Widget --}}
    <div id="fiscal-chatbot" class="fixed bottom-6 right-6 z-50">
        <!-- Botón flotante -->
        <button id="chatbot-toggle" onclick="toggleChatbot()" class="bg-purple-600 hover:bg-purple-700 text-white rounded-full p-4 shadow-lg transition-all hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" id="chatbot-badge" style="display: none;">1</span>
        </button>

        <!-- Ventana del chat -->
        <div id="chatbot-window" class="hidden absolute bottom-16 right-0 w-96 h-[32rem] bg-white rounded-lg shadow-2xl flex flex-col border border-gray-200">
            <!-- Header -->
            <div class="bg-purple-600 text-white p-4 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                        🤖
                    </div>
                    <div>
                        <h3 class="font-bold">Asistente Fiscal</h3>
                        <p class="text-xs text-purple-200">Especialista en Paraguay</p>
                    </div>
                </div>
                <button onclick="toggleChatbot()" class="hover:bg-purple-700 rounded p-1 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Messages -->
            <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <!-- Mensaje de bienvenida -->
                <div class="flex gap-2">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                        🤖
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-xs">
                        <p class="text-sm text-gray-800">¡Hola! Soy tu asistente fiscal experto en Paraguay. Puedo ayudarte con:</p>
                        <ul class="text-xs text-gray-600 mt-2 space-y-1">
                            <li>• Normativas de la SET</li>
                            <li>• Resolución RG-90</li>
                            <li>• Validación de comprobantes</li>
                            <li>• IVA y facturación</li>
                        </ul>
                        <p class="text-sm text-gray-800 mt-2">¿En qué puedo asistirte?</p>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="p-4 border-t border-gray-200 bg-white rounded-b-lg">
                <form id="chatbot-form" onsubmit="sendMessage(event)" class="flex gap-2">
                    <input
                        type="text"
                        id="chatbot-input"
                        placeholder="Escribe tu pregunta..."
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                        autocomplete="off"
                    >
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg px-4 py-2 transition" id="chatbot-send">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2 text-center">Respuestas generadas por IA</p>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            const isHidden = dropdown.classList.contains('hidden');

            dropdown.classList.toggle('hidden');

            if (isHidden) {
                loadNotifications();
            }
        }

        async function loadNotifications() {
            const list = document.getElementById('notifications-list');
            const loading = document.getElementById('notifications-loading');
            const empty = document.getElementById('notifications-empty');

            loading.classList.remove('hidden');
            empty.classList.add('hidden');

            // Remover notificaciones antiguas
            const oldNotifications = list.querySelectorAll('.notification-item');
            oldNotifications.forEach(n => n.remove());

            try {
                const response = await fetch('/notifications/recent');
                const data = await response.json();

                loading.classList.add('hidden');

                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        appendNotification(notification);
                    });
                } else {
                    empty.classList.remove('hidden');
                }

                updateBadge(data.unread_count);
            } catch (error) {
                console.error('Error loading notifications:', error);
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
            }
        }

        function appendNotification(notification) {
            const list = document.getElementById('notifications-list');
            const div = document.createElement('div');
            div.className = `notification-item p-4 hover:bg-gray-50 transition cursor-pointer ${!notification.read_at ? 'bg-purple-50' : ''}`;
            div.onclick = () => markAsRead(notification.id);

            const icon = getNotificationIcon(notification.type);

            div.innerHTML = `
                <div class="flex gap-3">
                    <div class="flex-shrink-0 text-2xl">
                        ${icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 ${!notification.read_at ? 'font-bold' : ''}">
                            ${escapeHtml(notification.title)}
                        </p>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                            ${escapeHtml(notification.message)}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            ${notification.time_ago}
                        </p>
                    </div>
                    ${!notification.read_at ? '<div class="w-2 h-2 bg-purple-600 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                </div>
            `;

            list.appendChild(div);
        }

        function getNotificationIcon(type) {
            const icons = {
                'duplicate_detected': '⚠️',
                'limit_exceeded': '🚨',
                'document_processed': '✅',
                'document_failed': '❌',
                'warning': '⚠️',
                'info': 'ℹ️'
            };
            return icons[type] || '🔔';
        }

        async function markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                loadNotifications();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        async function markAllAsRead() {
            try {
                await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                loadNotifications();
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }

        function updateBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Cargar badge al inicio
        async function loadBadgeCount() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();
                updateBadge(data.count);
            } catch (error) {
                console.error('Error loading badge count:', error);
            }
        }

        // Cargar badge cada 30 segundos
        loadBadgeCount();
        setInterval(loadBadgeCount, 30000);

        // Cerrar menús al hacer clic fuera
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const notificationsButton = event.target.closest('button[onclick="toggleNotifications()"]');

            if (!userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }

            if (!notificationsButton && !notificationsDropdown.contains(event.target)) {
                notificationsDropdown.classList.add('hidden');
            }
        });

        // Chatbot functionality
        function toggleChatbot() {
            const window = document.getElementById('chatbot-window');
            const badge = document.getElementById('chatbot-badge');
            window.classList.toggle('hidden');
            badge.style.display = 'none';
        }

        async function sendMessage(event) {
            event.preventDefault();

            const input = document.getElementById('chatbot-input');
            const messages = document.getElementById('chatbot-messages');
            const sendButton = document.getElementById('chatbot-send');
            const message = input.value.trim();

            if (!message) return;

            // Agregar mensaje del usuario
            appendMessage(message, 'user');
            input.value = '';
            sendButton.disabled = true;

            // Mostrar indicador de escritura
            const typingId = appendTypingIndicator();

            try {
                const response = await fetch('/api/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();

                // Remover indicador de escritura
                removeTypingIndicator(typingId);

                if (data.success) {
                    appendMessage(data.response, 'bot');
                } else {
                    appendMessage('Lo siento, hubo un error. Por favor intenta de nuevo.', 'bot');
                }
            } catch (error) {
                removeTypingIndicator(typingId);
                appendMessage('Error de conexión. Por favor verifica tu internet e intenta de nuevo.', 'bot');
            } finally {
                sendButton.disabled = false;
                input.focus();
            }
        }

        function appendMessage(text, type) {
            const messages = document.getElementById('chatbot-messages');
            const div = document.createElement('div');
            div.className = 'flex gap-2 fade-in';

            if (type === 'user') {
                div.innerHTML = `
                    <div class="flex-1"></div>
                    <div class="bg-purple-600 text-white rounded-lg p-3 shadow-sm max-w-xs">
                        <p class="text-sm">${escapeHtml(text)}</p>
                    </div>
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                `;
            } else {
                div.innerHTML = `
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                        🤖
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-xs">
                        <p class="text-sm text-gray-800">${formatBotMessage(text)}</p>
                    </div>
                `;
            }

            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }

        function appendTypingIndicator() {
            const messages = document.getElementById('chatbot-messages');
            const div = document.createElement('div');
            const id = 'typing-' + Date.now();
            div.id = id;
            div.className = 'flex gap-2';
            div.innerHTML = `
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                    🤖
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            `;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
            return id;
        }

        function removeTypingIndicator(id) {
            const indicator = document.getElementById(id);
            if (indicator) indicator.remove();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatBotMessage(text) {
            // Convertir markdown básico a HTML
            text = escapeHtml(text);
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
            text = text.replace(/\n/g, '<br>');
            return text;
        }
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @stack('scripts')
</body>
</html>
