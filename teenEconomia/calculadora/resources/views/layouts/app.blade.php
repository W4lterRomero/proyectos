<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Calculadora de Anualidades Diferidas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: light;
        }
        html.dark {
            color-scheme: dark;
        }
        html.dark body {
            background: #020617;
        }
        html.dark .bg-white,
        html.dark .bg-slate-50 {
            background-color: #0f172a !important;
        }
        html.dark .bg-white\/95 {
            background-color: rgba(15,23,42,0.95) !important;
        }
        html.dark .text-slate-900,
        html.dark .text-gray-800 {
            color: #e5e7eb !important;
        }
        html.dark .text-slate-600,
        html.dark .text-gray-600 {
            color: #cbd5f5 !important;
        }
        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-gray-200,
        html.dark .border-gray-100 {
            border-color: #1e293b !important;
        }
        html.dark .shadow-sm {
            box-shadow: 0 1px 2px rgba(15,23,42,0.7);
        }
    </style>

    {{-- MathJax para mostrar fórmulas en notación LaTeX --}}
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)'], ['$', '$']],
                displayMath: [['\\[', '\\]']],
                processEscapes: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-50 via-slate-100 to-blue-50">
    <div class="min-h-full flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('inicio') }}" class="flex items-center space-x-3">
                            <div class="w-9 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-semibold text-slate-900 tracking-tight">
                                Anualidades diferidas
                            </span>
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex md:items-center md:space-x-4">
                        <a href="{{ route('inicio') }}"
                           class="px-1 py-2 text-sm font-medium border-b-2 border-transparent transition-colors duration-150
                                  {{ request()->routeIs('inicio')
                                      ? 'border-blue-600 text-slate-900'
                                      : 'text-slate-600 hover:text-slate-900' }}">
                            Inicio
                        </a>
                        <a href="{{ route('documentacion') }}"
                           class="px-1 py-2 text-sm font-medium border-b-2 border-transparent transition-colors duration-150
                                  {{ request()->routeIs('documentacion')
                                      ? 'border-blue-600 text-slate-900'
                                      : 'text-slate-600 hover:text-slate-900' }}">
                            Documentación
                        </a>
                        <a href="{{ route('calculadora.form') }}"
                           class="px-1 py-2 text-sm font-medium border-b-2 border-transparent transition-colors duration-150
                                  {{ request()->routeIs('calculadora.*')
                                      ? 'border-blue-600 text-slate-900'
                                      : 'text-slate-600 hover:text-slate-900' }}">
                            Calculadora
                        </a>
                        <button
                            type="button"
                            id="theme-toggle"
                            class="ml-4 inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition"
                        >
                            <span id="theme-toggle-icon" class="mr-1"></span>
                            <span id="theme-toggle-text">Modo noche</span>
                        </button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="hidden md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-100">
                    <a href="{{ route('inicio') }}"
                       class="block px-3 py-2 rounded-lg text-base font-medium
                              {{ request()->routeIs('inicio')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Inicio
                    </a>
                    <a href="{{ route('documentacion') }}"
                       class="block px-3 py-2 rounded-lg text-base font-medium
                              {{ request()->routeIs('documentacion')
                                  ? 'bg-blue-50 text-blue-700'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Documentación
                    </a>
                    <a href="{{ route('calculadora.form') }}"
                       class="block px-3 py-2 rounded-lg text-base font-medium
                              {{ request()->routeIs('calculadora.*')
                                  ? 'bg-blue-600 text-white'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Calculadora
                    </a>
                    <button
                        type="button"
                        id="theme-toggle-mobile"
                        class="mt-1 block w-full text-left px-3 py-2 rounded-lg text-base font-medium border border-slate-200 text-slate-700 bg-white hover:bg-slate-50"
                    >
                        <span id="theme-toggle-mobile-icon" class="mr-1">🌞</span>
                        <span id="theme-toggle-mobile-text">Modo noche</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    Proyecto académico de anualidades diferidas &copy; {{ date('Y') }}
                    <span class="mx-2">•</span>
                    <span class="text-blue-600 font-medium">Universidad de El Salvador</span>
                </p>
            </div>
        </footer>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Tema día / noche
        (function () {
            const root = document.documentElement;
            const stored = localStorage.getItem('tema_anualidades');
            if (stored === 'dark' || (!stored && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                root.classList.add('dark');
            }

            function syncLabels() {
                const isDark = root.classList.contains('dark');
                const icon = document.getElementById('theme-toggle-icon');
                const text = document.getElementById('theme-toggle-text');
                const iconM = document.getElementById('theme-toggle-mobile-icon');
                const textM = document.getElementById('theme-toggle-mobile-text');
                if (icon && text) {
                    icon.textContent = isDark ? '🌙' : '🌞';
                    text.textContent = isDark ? 'Modo día' : 'Modo noche';
                }
                if (iconM && textM) {
                    iconM.textContent = isDark ? '🌙' : '🌞';
                    textM.textContent = isDark ? 'Modo día' : 'Modo noche';
                }
            }

            function toggleTheme() {
                const isDark = root.classList.toggle('dark');
                localStorage.setItem('tema_anualidades', isDark ? 'dark' : 'light');
                syncLabels();
            }

            document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
            document.getElementById('theme-toggle-mobile')?.addEventListener('click', toggleTheme);

            syncLabels();
        })();
    </script>
</body>
</html>

