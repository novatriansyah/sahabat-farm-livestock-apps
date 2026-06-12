<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::create([
            'title' => 'Tips Sukses Manajemen Pakan Hijauan Ternak Kambing',
            'summary' => 'Manajemen pakan adalah kunci sukses utama dalam peternakan kambing. Pelajari teknik penyimpanan dan formulasi pakan hijauan terbaik.',
            'content' => '<h2>Pentingnya Manajemen Pakan Hijauan</h2><p>Pakan hijauan berkualitas merupakan komponen utama dalam budidaya kambing potong maupun perah. Kurangnya pemahaman tentang cara pemberian dan penyimpanan pakan seringkali menjadi kendala utama peternak lokal.</p><blockquote>Pakan yang baik menentukan kualitas daging dan susu yang dihasilkan. Jangan asal kenyang, tapi perhatikan nutrisinya.</blockquote><h3>Teknik Penyimpanan Hijauan (Silase)</h3><p>Saat musim kemarahan melanda, ketersediaan rumput segar menurun drastis. Solusinya adalah pembuatan silase dengan langkah-langkah berikut:</p><ul><li>Cacah rumput gajah atau tebon jagung menjadi ukuran 3-5 cm.</li><li>Campurkan dengan dedak padi sebagai bahan stimulan bakteri asam laktat.</li><li>Masukkan ke dalam tong silo kedap udara (anaerob).</li><li>Simpan selama 21 hari hingga proses fermentasi selesai.</li></ul><p>Dengan teknik silase, nutrisi pakan tetap terjaga hingga berbulan-bulan lamanya.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        Article::create([
            'title' => 'Mengenal Penyakit Mulut dan Kuku (PMK) & Cara Pencegahannya',
            'summary' => 'Waspadai gejala Penyakit Mulut dan Kuku pada ternak sapi dan kambing Anda. Lakukan tindakan pencegahan secara preventif sesegera mungkin.',
            'content' => '<h2>Apa itu Penyakit Mulut dan Kuku (PMK)?</h2><p>PMK adalah penyakit hewan menular bersifat akut yang menyerang hewan berkuku belah/genap seperti sapi, kerbau, kambing, domba, dan babi. Penyakit ini disebabkan oleh virus tipe A dari keluarga Picornaviridae.</p><h3>Gejala Klinis yang Harus Diwaspadai</h3><p>Kenali tanda-tanda awal infeksi PMK berikut:</p><ul><li>Demam tinggi mencapai 40-41 derajat Celcius.</li><li>Lendir berlebihan di mulut (hipersalivasi) disertai busa.</li><li>Luka lepuh pada lidah, gusi, bibir, serta sela-sela kuku.</li><li>Hewan terlihat pincang dan enggan untuk berdiri.</li></ul><blockquote>Pencegahan terbaik adalah melakukan biosekuriti kandang secara ketat dan penyemprotan disinfektan secara rutin pada setiap sudut area kandang.</blockquote><h3>Langkah Penanganan Darurat</h3><p>Jika ditemukan gejala PMK di kandang Anda, segera lakukan karantina mandiri terhadap ternak yang terinfeksi dan hubungi dokter hewan dinas peternakan setempat untuk pemberian vaksinasi booster.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(1),
        ]);

        Article::create([
            'title' => 'Pentingnya Pencatatan Silsilah (Genealogy) dalam Pembiakan Domba',
            'summary' => 'Mengapa Anda harus memiliki catatan silsilah ternak yang rapi? Simak penjelasan lengkap tentang bahaya inbreeding bagi produktivitas koloni ternak Anda.',
            'content' => '<h2>Bahaya Inbreeding (Perkawinan Sedarah)</h2><p>Perkawinan sedarah dalam dunia peternakan seringkali menurunkan produktivitas genetika ternak Anda. Dampak buruk inbreeding meliputi kemunduran performa pertumbuhan (stunting), penurunan tingkat kesuburan, hingga lahirnya cempe dengan cacat bawaan.</p><h3>Solusi: Pencatatan Silsilah Digital</h3><p>Dengan pencatatan silsilah yang rapi, Anda dapat merencanakan perkawinan silang (cross-breeding) dengan aman. Sistem database digital membantu Anda:</p><ul><li>Melacak silsilah keturunan pejantan (sire) dan induk (dam).</li><li>Mengetahui riwayat genetik untuk mencegah perkawinan kekerabatan dekat.</li><li>Mengidentifikasi gen unggul pembawa sifat pertumbuhan cepat (ADG tinggi).</li></ul><p>Gunakan tag nomor telinga (ear tag) yang terintegrasi dengan dasbor digital untuk mempermudah pemantauan harian.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Article::create([
            'title' => 'Rencana Kerja Bulanan Peternakan Sahabat Farm (Internal Draft)',
            'summary' => 'Jadwal pemberian obat cacing berkala, pembersihan bak pakan koloni, serta rencana sanitasi kandang bulan Juni.',
            'content' => '<h2>Draft Agenda Internal</h2><p>Artikel ini masih bersifat draft internal dan tidak dipublikasikan ke halaman utama pengunjung.</p>',
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
