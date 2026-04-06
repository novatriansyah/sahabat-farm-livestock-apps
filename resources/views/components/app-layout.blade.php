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
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">

    @include('layouts.navigation')

    @include('layouts.sidebar')

    <div class="sm:ml-64 transition-all duration-300">
        <div class="p-4 mt-14 animate-fade-in">
            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-emerald-800 rounded-2xl bg-emerald-50/50 border border-emerald-100 backdrop-blur-sm dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800 shadow-soft" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-bold">Sukses!</span> 
                    </div>
                    <div class="mt-1 ms-7">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-2xl bg-red-50/50 border border-red-100 backdrop-blur-sm dark:bg-red-900/30 dark:text-red-300 dark:border-red-800 shadow-soft" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span class="font-bold">Error!</span> 
                    </div>
                    <div class="mt-1 ms-7">
                        @if(is_array(session('error')))
                            <ul class="list-disc list-inside">
                                @foreach(session('error') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ session('error') }}
                        @endif
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-6 text-sm text-orange-800 rounded-2xl bg-orange-50/50 border border-orange-100 backdrop-blur-sm dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800 shadow-soft" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="font-bold">Perhatian!</span>
                    </div>
                    <ul class="mt-1 ms-7 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="mt-2">
                {{ $slot }}
            </div>
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
