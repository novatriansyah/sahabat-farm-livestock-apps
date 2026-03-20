<x-print-layout 
    title="Laporan Koloni Kawin" 
    type="LAPORAN DATA KOLONI KAWIN" 
    :period="date('F Y')"
>
    <!-- Screen Header -->
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Koloni Kawin & Breeding
        </h2>
        <a href="{{ route('reports.mating', ['mode' => 'print']) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan
        </a>
    </div>
    @endif

    <!-- Active Mating Colonies -->
    <div class="mb-12 no-break">
        <h3 class="text-sm font-black mb-6 text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] border-l-4 border-blue-500 pl-4">
            Koloni Kawin Aktif (Status Saat Ini)
        </h3>
        
        @if($colonies->count() > 0)
        <!-- Grid on Screen, List on Print -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 grid-print-2">
            @foreach($colonies as $colony)
            <div class="p-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm no-break hover:border-blue-200 transition-all">
                <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
                    <div>
                        <h4 class="text-lg font-black text-blue-800 dark:text-blue-300">{{ $colony->name }}</h4>
                        <p class="text-xs font-bold text-slate-400 uppercase mt-1 tracking-widest">{{ $colony->location->name ?? 'No Location' }}</p>
                    </div>
                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black uppercase tracking-widest border border-blue-200">
                        {{ $colony->members->count() }} Betina
                    </span>
                </div>
                
                <div class="mb-6 bg-slate-50 dark:bg-slate-900/50 p-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pejantan Utama (Sire)</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $colony->sire->tag_id }} <span class="text-slate-400 font-medium ml-2">({{ $colony->sire->breed->name ?? '-' }})</span></p>
                </div>

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Daftar Anggota Betina (Dam)</p>
                    <div class="border border-slate-100">
                        <table class="w-full text-xs border-collapse">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-2 py-1 text-left">Tag ID</th>
                                    <th class="px-2 py-1 text-left">Ras</th>
                                    <th class="px-2 py-1 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($colony->members as $member)
                                <tr>
                                    <td class="px-2 py-1 font-bold text-slate-900 dark:text-white">{{ $member->animal->tag_id }}</td>
                                    <td class="px-2 py-1">{{ $member->animal->breed->name ?? '-' }}</td>
                                    <td class="px-2 py-1 text-[9px] font-black">{{ $member->animal->physStatus->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-200 text-center text-slate-500 italic font-medium">
            Tidak ada koloni kawin aktif.
        </div>
        @endif
    </div>

    <!-- Mating History / Active Events -->
    <div class="mt-12 no-break">
        <h3 class="text-sm font-black mb-6 text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] border-l-4 border-emerald-500 pl-4">
            Estimasi Jadwal Kelahiran (Waiting Room)
        </h3>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left">Indukan (Dam)</th>
                        <th class="px-6 py-4 text-left">Pejantan (Sire)</th>
                        <th class="px-6 py-4 text-center">Tgl Kawin</th>
                        <th class="px-6 py-4 text-center">Perkiraan Lahir</th>
                        <th class="px-6 py-4 text-right">Status Countdown</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeEvents as $event)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $event->dam->tag_id }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $event->sire->tag_id }}</td>
                        <td class="px-6 py-4 text-center">{{ $event->mating_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center font-black text-blue-600">{{ $event->est_birth_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            @php $days = now()->diffInDays($event->est_birth_date, false); @endphp
                            @if($days <= 7 && $days >= 0)
                                <span class="bg-rose-100 text-rose-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-200 animate-pulse">
                                    {{ number_format($days, 0) }} Hari Lagi (HAMPIR)
                                </span>
                            @elseif($days < 0)
                                <span class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-200">
                                    Lewat {{ number_format(abs($days), 0) }} Hari
                                </span>
                            @else
                                <span class="text-xs font-bold text-slate-500">{{ number_format($days, 0) }} Hari</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
