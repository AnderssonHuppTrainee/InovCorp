<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Amazing Library') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased text-base-content min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
    <x-banner />

    <div class="min-h-screen flex flex-col">
        @livewire('navigation-menu')

        <!-- Page Heading 
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif-->

        <!-- Page Content -->
        <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('modals')

    <!-- Footer -->
    <footer class="footer footer-center p-4 bg-base-200 text-base-content mt-auto">
        <aside>
            <p>© {{ date('Y') }} Amazing Library. Todos os direitos reservados.</p>
        </aside>
    </footer>
    @livewireScripts
</body>


</html>