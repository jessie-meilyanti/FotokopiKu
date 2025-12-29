<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicons - pakai logo.svg agar lebih tajam dan besar di semua device -->
        <link rel="icon" type="image/svg+xml" sizes="any" href="/images/logo.svg">
        <link rel="alternate icon" type="image/png" sizes="196x196" href="/images/Logo.png">
        <link rel="apple-touch-icon" href="/images/Logo.png">
        <meta name="theme-color" content="#ffffff">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-indigo-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-950">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @if (session('success') || session('error'))
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    @if (session('success'))
                        <div class="mb-3 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-3 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            @if (isset($header))
                <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="py-6">
                {{ $slot }}
            </main>
        </div>

        <!-- Floating WhatsApp Button -->
        <x-whatsapp-button />
    </body>
</html>
