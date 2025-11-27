<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AIJA') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar {
            background: white;
            border-right: 1px solid #e5e7eb;
        }
        .nav-link {
            color: #6b7280;
            transition: all 0.2s;
        }
        .nav-link:hover {
            color: #2563eb;
            background-color: #f8fafc;
        }
        .nav-link.active {
            color: #2563eb;
            background-color: #eff6ff;
            border-right: 3px solid #2563eb;
        }
        .main-content {
            background: #f8fafc;
            min-height: 100vh;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
                    <h1 class="text-xl font-bold text-blue-600">AIJA</h1>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('dashboard.bills') }}" class="nav-link {{ request()->routeIs('dashboard.bills*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Bills & Payments
                    </a>

                    <a href="{{ route('dashboard.scholarships') }}" class="nav-link {{ request()->routeIs('dashboard.scholarships*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Scholarships
                    </a>

                    <a href="{{ route('dashboard.profile') }}" class="nav-link {{ request()->routeIs('dashboard.profile*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>

                    @if (Auth::user()->level !== 'mahasiswa')
                        <div class="pt-4 border-t border-gray-200 mt-4">
                            <p class="px-4 text-xs font-semibold text-gray-500 uppercase mb-2">Admin</p>
                            <a href="{{ route('admin.bills.index') }}" class="nav-link {{ request()->routeIs('admin.bills.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Bills
                            </a>
                            <a href="{{ route('admin.scholarships.index') }}" class="nav-link {{ request()->routeIs('admin.scholarships.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Scholarships
                            </a>
                            <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.868 12.683A17.925 17.925 0 0112 21c7.962 0 12-1.21 12-2.683m-12 2.683a17.925 17.925 0 01-7.132-8.317M12 21c4.411 0 8-4.03 8-9s-3.589-9-8-9-8 4.03-8 9a9.06 9.06 0 001.832 5.445"></path>
                                </svg>
                                Notifications
                            </a>
                        </div>
                    @endif

                    <!-- Chatbot Button -->
                    <div class="pt-4 border-t border-gray-200">
                        <button onclick="openChatbot()" class="nav-link w-full flex items-center px-4 py-3 rounded-lg">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            AI Assistant
                        </button>
                    </div>
                </nav>

                <!-- User Menu -->
                <div class="px-4 py-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="ml-3">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-auto">
            <div class="p-8">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Chatbot Modal -->
    <div id="chatbot-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md h-96 flex flex-col">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">AI Assistant</h3>
                    <button onclick="closeChatbot()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="chat-messages" class="flex-1 p-4 overflow-y-auto">
                    <div class="text-center text-gray-500 text-sm">
                        Hi! I'm your AI assistant. How can I help you today?
                    </div>
                </div>
                <div class="p-4 border-t">
                    <div class="flex space-x-2">
                        <input type="text" id="chat-input" placeholder="Type your message..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button onclick="sendMessage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        function openChatbot() {
            document.getElementById('chatbot-modal').classList.remove('hidden');
        }

        function closeChatbot() {
            document.getElementById('chatbot-modal').classList.add('hidden');
        }

        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            input.value = '';
            showTypingIndicator();

            try {
                const response = await fetch('{{ route('chatbot.message') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message })
                });
                const data = await response.json();
                hideTypingIndicator();

                if (data.success) {
                    addMessage(data.response, 'bot');
                } else {
                    addMessage('Maaf, saya tidak bisa merespons saat ini.', 'bot');
                }
            } catch (error) {
                hideTypingIndicator();
                addMessage('Terjadi kesalahan jaringan. Coba lagi.', 'bot');
            }
        }

        function addMessage(text, sender) {
            const messages = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `mb-3 ${sender === 'user' ? 'text-right' : 'text-left'}`;
            messageDiv.innerHTML = `
                <div class="inline-block px-3 py-2 rounded-lg ${sender === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 whitespace-pre-line'}">
                    ${text}
                </div>
            `;
            messages.appendChild(messageDiv);
            messages.scrollTop = messages.scrollHeight;
        }

        function showTypingIndicator() {
            const messages = document.getElementById('chat-messages');
            const indicator = document.createElement('div');
            indicator.id = 'chat-typing';
            indicator.className = 'mb-3 text-left';
            indicator.innerHTML = `
                <div class="inline-block px-3 py-2 rounded-lg bg-gray-100 text-gray-600">
                    AI sedang mengetik...
                </div>
            `;
            messages.appendChild(indicator);
            messages.scrollTop = messages.scrollHeight;
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('chat-typing');
            if (indicator) {
                indicator.remove();
            }
        }
    </script>
</body>
</html>
