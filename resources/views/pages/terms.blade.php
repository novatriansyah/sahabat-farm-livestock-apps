@extends('layouts.guest')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="mb-16">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">Syarat & Ketentuan</h1>
            <p class="text-slate-500 font-medium italic">Terakhir diperbarui: 20 Maret 2026</p>
        </div>

        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none space-y-10">
            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">1. Penerimaan Ketentuan</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Dengan mengakses atau menggunakan platform Sahabat Farm, Anda dianggap telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan bagian apapun dari ketentuan ini, Anda dilarang menggunakan layanan kami.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">2. Layanan Platform</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Sahabat Farm menyediakan sistem manajemen peternakan berbasis cloud. Kami berhak untuk mengubah, menangguhkan, atau menghentikan aspek apa pun dari layanan kapan saja tanpa pemberitahuan sebelumnya.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">3. Keamanan Akun</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Anda bertanggung jawab penuh untuk menjaga kerahasiaan informasi akun dan kata sandi Anda. Anda menyetujui untuk segera memberitahu kami jika terjadi penggunaan ilegal atau pelanggaran keamanan lainnya terhadap akun Anda.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold dark:text-white mb-4">4. Penggunaan yang Diizinkan</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Anda setuju untuk menggunakan Sahabat Farm hanya untuk tujuan yang sah dan sesuai dengan hukum yang berlaku di Republik Indonesia. Anda tidak diperbolehkan menggunakan sistem ini untuk menyimpan data palsu atau melakukan penipuan.
                </p>
            </section>

            <section class="p-8 rounded-3xl bg-slate-100 dark:bg-slate-800 border-l-4 border-emerald-500">
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-2">Penting:</p>
                <p class="text-slate-600 dark:text-slate-400 italic">"Sahabat Farm tidak bertanggung jawab atas kerugian finansial atau kematian hewan yang disebabkan oleh kesalahan input data atau kegagalan infrastruktur internet pengguna."</p>
            </section>
        </div>
    </div>
</div>
@endsection
