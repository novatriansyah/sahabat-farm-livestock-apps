<x-print-layout 
    title="Laporan Induk Menyusui" 
    type="LAPORAN DATA INDUK MENYUSUI & CEMPE" 
    :period="date('F Y')"
>
    <!-- Screen Header -->
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Induk Menyusui & Cempe
        </h2>
        <a href="{{ route('reports.nursing', ['mode' => 'print']) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan
        </a>
</div>
    @endif

    <!-- Nursing List -->
    <div class="no-break mb-12">
        <h3 class="text-sm font-black mb-6 text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] border-l-4 border-emerald-500 pl-4">
            Daftar Induk Aktif Menyusui (Nursing Period)
        </h3>
        
        @if($nursingAnimals->count() > 0)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left">Induk (Tag ID)</th>
                        <th class="px-6 py-4 text-left">Ras / Breed</th>
                        <th class="px-6 py-4 text-left">Lokasi Kandang</th>
                        <th class="px-6 py-4 text-left">Cempe (Offspring)</th>
                        <th class="px-6 py-4 text-right">Durasi Menyusui</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nursingAnimals as $dam)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-black text-slate-900 dark:text-white">{{ $dam->tag_id }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $dam->breed->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $dam->location->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <ul class="space-y-1">
                                @forelse($dam->offspring as $kid)
                                    <li class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-900 text-[10px] font-bold text-slate-700 dark:text-slate-300 border border-slate-200 mr-1 mb-1">
                                        {{ $kid->tag_id }} <span class="ml-1 opacity-50">{{ $kid->gender == 'JANTAN' ? '♂' : '♀' }}</span>
                                    </li>
                                @empty
                                    <span class="text-slate-400 font-medium italic">Data tidak ditemukan</span>
                                @endforelse
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($dam->offspring->count() > 0)
                                @php $latestKid = $dam->offspring->first(); $days = number_format($latestKid->birth_date->diffInDays(now()), 0); @endphp
                                <span class="text-slate-900 font-black">{{ $days }} <span class="text-xs font-bold text-slate-400">Hari</span></span>
                                @if($days >= 60)
                                    <span class="block mt-1 text-[9px] font-black text-rose-600 uppercase tracking-widest border border-rose-200 bg-rose-50 px-2 rounded-full text-center">SIAP SAPIH (WEANING)</span>
                                @endif
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-200 text-center text-slate-500 italic font-medium">
            Tidak ada induk dalam status menyusui saat ini.
        </div>
        @endif
    </div>

    <!-- Weaning Rules (Print Only / Info) -->
    <div class="bg-blue-50 border border-blue-100 p-6 no-print dark:bg-blue-900/20 dark:border-blue-800/50">
        <h4 class="text-xs font-black text-blue-800 dark:text-blue-300 uppercase tracking-widest mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Panduan Manajemen Sapih
        </h4>
        <p class="text-[11px] text-blue-700 dark:text-blue-400 font-medium leading-relaxed">
            Status <span class="font-bold">"Siap Sapih"</span> otomatis muncul jika usia cempe termuda telah mencapai minimal 60 hari. Segera lakukan pemindahan cempe ke kandang pembesaran dan pemulihan kondisi induk.
        </p>
    </div>
</x-print-layout>
