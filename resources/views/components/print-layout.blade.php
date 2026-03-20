@props(['title', 'type' => 'LAPORAN OPERASIONAL', 'period' => date('F Y')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - Sahabat Farm</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}">

    <style>
        @media print {
            @page { 
                size: A4 portrait !important; 
                margin: 15mm 10mm 15mm 10mm !important; 
            }
            
            body { 
                font-family: 'Inter', sans-serif !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                background: white !important;
                color: black !important;
                font-size: 10pt !important;
                line-height: 1.5 !important;
            }

            /* Hide UI Elements */
            nav, header, aside, .no-print, .btn, button, form, .sidebar-wrapper, [role="navigation"] { 
                display: none !important; 
            }

            /* Reset Layout */
            .min-h-screen { min-height: 0 !important; }
            .bg-gray-50, .bg-gray-100, .bg-slate-50, .bg-white { background-color: white !important; }
            
            /* Main container reset */
            .p-4.sm\:ml-64, .sm\:ml-64 { 
                margin-left: 0 !important; 
                padding: 0 !important; 
                width: 100% !important; 
            }
            
            .p-4.mt-14, .mt-14 { 
                margin-top: 0 !important; 
                padding: 0 !important;
            }

            /* Container elements stabilization */
            .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }
            .rounded-lg, .rounded-xl, .rounded-2xl, .rounded-[2rem], .rounded-3xl, .rounded-[3rem] { border-radius: 0 !important; }
            .overflow-hidden { overflow: visible !important; }
            .border, .border-slate-200, .border-gray-200 { border: 0.5pt solid #eee !important; }

            /* Table Formatting */
            table { 
                width: 100% !important; 
                border-collapse: collapse !important; 
                margin-bottom: 1.5rem !important;
                table-layout: auto !important;
                border: 0.5pt solid #ccc !important;
                border-radius: 0 !important;
            }
            th, td { 
                border: 0.5pt solid #ccc !important; 
                padding: 6px 8px !important; 
                text-align: left;
                word-wrap: break-word !important;
            }
            th { 
                background-color: #f1f5f9 !important; 
                font-weight: bold !important;
                text-transform: uppercase !important;
                font-size: 8pt !important;
                color: #334155 !important;
            }
            thead { display: table-header-group !important; }
            tr { page-break-inside: avoid !important; }
            
            /* Utils */
            .page-break-before { page-break-before: always !important; }
            .no-break { page-break-inside: avoid !important; }
            .text-right { text-align: right !important; }
            .text-center { text-align: center !important; }
            .font-bold { font-weight: bold !important; }
            .text-emerald-600, .text-blue-600, .text-red-600 { color: black !important; }

            /* Custom Grid for Print */
            .grid-print-2 {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 10mm !important;
            }
            .grid-print-4 {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 5mm !important;
            }
        }

        @media screen {
            body.mode-print {
                background-color: #f8fafc;
                padding: 40px 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            /* Buttons should be visible on screen preview */
            body.mode-print .print-controls {
                display: flex !important;
            }
            .mode-print .print-paper {
                background: white;
                width: 210mm;
                min-height: 297mm;
                padding: 25mm 20mm;
                box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.15), 0 0 1px rgba(0,0,0,0.1);
                margin-bottom: 60px;
                position: relative;
                border-radius: 0 !important;
            }
            .mode-print .print-paper *, 
            .mode-print .rounded-lg, 
            .mode-print .rounded-xl, 
            .mode-print .rounded-2xl, 
            .mode-print .rounded-3xl, 
            .mode-print .rounded-[2rem] {
                border-radius: 0 !important;
            }
            .mode-print table {
                border-radius: 0 !important;
                overflow: hidden;
            }
            .print-controls {
                width: 210mm;
                margin-bottom: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }
            .btn-premium-back {
                display: inline-flex;
                items-center;
                padding: 12px 24px;
                background: white;
                border: 1px solid #e2e8f0;
                color: #475569;
                font-weight: 800;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-premium-back:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                color: #0f172a;
                transform: translateY(-1px);
            }
            .btn-premium-print {
                display: inline-flex;
                items-center;
                padding: 12px 32px;
                background: #0f172a;
                color: white;
                font-weight: 800;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .btn-premium-print:hover {
                background: #1e293b;
                box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.3);
                transform: translateY(-1px);
            }
            .print-header-container {
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: center;
                gap: 30px;
                border-bottom: 3pt double #0f172a;
                padding-bottom: 25px;
                margin-bottom: 60px; /* Increased margin for premium feel */
            }
            .print-logo {
                height: 22mm;
                width: auto;
                object-fit: contain;
            }
        }

        /* Fix overlapping logo for print specifically */
        @media print {
            .print-header-container {
                display: grid !important;
                grid-template-columns: 35mm 1fr 60mm !important;
                align-items: center !important;
                gap: 5mm !important;
                border-bottom: 2pt solid #000 !important;
                width: 100% !important;
                margin-bottom: 12mm !important; /* Margin for actual print result */
            }
            .print-logo {
                height: 25mm !important;
                width: auto !important;
                object-fit: contain !important;
                display: block !important;
            }
        }
    </style>
</head>
<body class="antialiased {{ request('mode') === 'print' ? 'mode-print' : '' }}">
    
    @if(request('mode') === 'print')
        <div class="no-print print-controls">
            <a href="{{ url()->previous() }}" class="btn-premium-back group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mr-4">Pratinjau Laporan A4</span>
                <button onclick="window.print()" class="btn-premium-print">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Sekarang
                </button>
            </div>
        </div>
        
        <div class="print-paper">
            <header class="print-header-container">
                <img src="{{ asset('img/logo.png') }}" class="print-logo" alt="Logo">
                <div class="flex flex-col justify-center border-l border-slate-200 pl-8">
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 leading-tight uppercase">Sahabat Farm Indonesia</h1>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mt-1">Modern Livestock Management System</p>
                </div>
                <div class="text-right flex flex-col justify-center">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tighter">{{ $type }}</h2>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-[0.3em] bg-slate-100 px-3 py-1 inline-block ml-auto">{{ $period }}</p>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>

            <footer class="mt-20 pt-10 border-t border-slate-100 flex justify-between items-end no-break">
                <div class="text-[8pt] text-slate-400 font-medium italic">
                    Dihasilkan secara otomatis oleh sistem Sahabat Farm pada {{ date('d/m/Y H:i') }}
                </div>
                <div class="text-center w-64 pb-2">
                    <p class="text-[8pt] font-black text-slate-400 uppercase tracking-[0.3em] mb-20">Otorisasi Manajemen</p>
                    <div class="h-px bg-slate-200 w-full mb-2"></div>
                    <p class="text-[7pt] text-slate-300 font-bold uppercase tracking-widest">Tanda Tangan & Cap Resmi</p>
                </div>
            </footer>
        </div>
    @else
        <x-app-layout>
            {{ $slot }}
        </x-app-layout>
    @endif

</body>
</html>
