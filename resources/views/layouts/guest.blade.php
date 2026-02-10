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
    <body class="font-sans antialiased bg-background">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo e Título -->
            <div class="text-center mb-8">
                <a href="/" class="inline-block">
                    <div class="flex items-center justify-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-semibold text-primary">SEGMETRE</h1>
                    </div>
                    <p class="text-sm text-muted-foreground">Radiology Information System</p>
                </a>
            </div>

            <!-- Card do Formulário -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-card border border-border rounded-lg shadow-lg">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-muted-foreground">
                    © {{ date('Y') }} SEGMETRE. Todos os direitos reservados.
                </p>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
