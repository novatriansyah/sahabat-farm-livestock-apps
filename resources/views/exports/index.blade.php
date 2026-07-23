@extends('layouts.sidebar')

@section('content')
<div class="py-8 px-4 mx-auto max-w-7xl">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Pusat Export Data & Laporan (Export Center)</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Unduh Canonical Export, Import-Compatible Workbook, dan Laporan Mitra resmi SFI.</p>
    </div>

    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Canonical Full Export -->
        @if(!$isMitra)
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">1. Canonical Full Export</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Seluruh 13 Sheet data ternak, event histori, tag history, dan data quality audit tanpa filter.</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('exports.download', ['product' => 'canonical']) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition">
                    Unduh Canonical XLSX
                </a>
            </div>
        </div>
        @endif

        <!-- 2. Import-Compatible Export -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">2. Import-Compatible (35 Field)</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Export 35 kolom lossless yang kompatibel penuh dengan importer. Dapat difilter per mitra.</p>
            </div>
            <form action="{{ route('exports.download') }}" method="GET" class="mt-6 space-y-3">
                <input type="hidden" name="product" value="import_compatible">
                @if(!$isMitra)
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih Pemilik / Mitra</label>
                    <select name="partner_id" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Semua (ALL 166 Ternak)</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="partner_id" value="{{ Auth::user()->partner_id }}">
                @endif
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition">
                    Unduh Import-Compatible XLSX
                </button>
            </form>
        </div>

        <!-- 3. Partner Report (XLSX / PDF) -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">3. Laporan Mitra (Official Report)</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Laporan resmi perkembangan ternak, populasi, ADG, dan kesehatan mitra dalam format XLSX / PDF.</p>
            </div>
            <form action="{{ route('exports.download') }}" method="GET" class="mt-6 space-y-3">
                <input type="hidden" name="product" value="partner_report">
                @if(!$isMitra)
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Pilih Mitra*</label>
                    <select name="partner_id" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">-- Pilih Mitra --</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="partner_id" value="{{ Auth::user()->partner_id }}">
                @endif
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Format</label>
                    <select name="format" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="xlsx">XLSX (Spreadsheet + Chart)</option>
                        <option value="pdf">PDF (Dokumen Cetak)</option>
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                    Unduh Laporan Mitra
                </button>
            </form>
        </div>
    </div>

    <!-- Additional Tools: Blank Template Download -->
    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Template Blank Import v2.0.0 (35 Kolom)</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Unduh template kosong 35 kolom beserta validasi dropdown untuk entri data ternak baru.</p>
        </div>
        <a href="{{ route('exports.download', ['product' => 'template']) }}" class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 transition whitespace-nowrap">
            Unduh Blank Template v2.0.0
        </a>
    </div>
</div>
@endsection
