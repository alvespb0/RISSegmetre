<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SEGMETRE') }} - Radiology Information System</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen overflow-hidden bg-background">
            @include('components.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden">
                @include('components.header')

                <main class="flex-1 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Toast Notifications -->
        @include('components.toast-notifications')

        @stack('scripts')
    </body>
</html>
