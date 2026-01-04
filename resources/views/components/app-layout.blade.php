<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}">

    <style>
        /* Global: Hide number spinners */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    
    {{ $styles ?? '' }}
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">

    @include('layouts.navigation')

    @include('layouts.sidebar')

    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error!</span> 
                    @if(is_array(session('error')))
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach(session('error') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ session('error') }}
                    @endif
                </div>
            @endif
            
            <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            {{ $slot }}
        </div>
    </div>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Global: Enforce min="0" on number inputs
            document.body.addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT' && e.target.type === 'number' && e.target.min >= 0) {
                    if (e.target.value < 0) {
                        e.target.value = Math.abs(e.target.value); // Convert to positive
                    }
                }
            });

            document.body.addEventListener('keydown', (e) => {
                if (e.target.tagName === 'INPUT' && e.target.type === 'number' && e.target.min >= 0) {
                    // Prevent minus sign
                    if (e.key === '-' || e.key === 'Minus') {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>
