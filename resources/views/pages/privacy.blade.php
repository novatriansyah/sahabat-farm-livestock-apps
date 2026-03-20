@extends('layouts.guest')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="mb-16">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">Kebijakan Privasi</h1>
            <p class="text-slate-500 font-medium italic">Privasi Anda adalah prioritas kami.</p>
        </div>

        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">Informasi yang Kami Kumpulkan</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Kami mengumpulkan data yang Anda berikan secara langsung, seperti nama peternakan, alamat email, nomor telepon, dan data teknis peternakan (populasi ternak, lokasi kandang). Kami juga mengumpulkan data log secara otomatis saat Anda menggunakan aplikasi kami.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">Penggunaan Data</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Data yang kami kumpulkan digunakan untuk:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                    <li>Menyediakan informasi analitik yang akurat bagi peternakan Anda.</li>
                    <li>Meningkatkan fungsionalitas dan pengalaman pengguna aplikasi Sahabat Farm.</li>
                    <li>Mengirimkan notifikasi penting terkait kesehatan ternak atau pembaruan sistem.</li>
                    <li>Keperluan audit internal dan kepatuhan hukum yang berlaku.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">Keamanan Data</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Sahabat Farm menggunakan enkripsi berstandar industri untuk melindungi data Anda saat transit dan saat disimpan. Kami tidak akan pernah menjual data pribadi atau data peternakan Anda kepada pihak ketiga tanpa izin eksplisit dari Anda.
                </p>
            </section>

            <section class="p-8 rounded-3xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
                <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400 mb-2">Hak Anda</p>
                <p class="text-slate-600 dark:text-slate-400">Anda berhak untuk meminta salinan data Anda, memperbarui informasi yang salah, atau meminta penghapusan akun Anda kapan saja melalui tim support kami.</p>
            </section>
        </div>
    </div>
</div>
@endsection
