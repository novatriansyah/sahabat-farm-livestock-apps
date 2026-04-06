<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $diseases = [
            [
                'name' => 'Orf (Bengkak Mulut)',
                'category' => 'Viral',
                'symptoms' => 'Luka keropeng pada mulut, hidung, dan terkadang puting susu.',
                'description' => 'Penyakit menular pada kambing/domba yang disebabkan oleh virus parapox.',
            ],
            [
                'name' => 'Pink Eye (Sakit Mata)',
                'category' => 'Bakteri',
                'symptoms' => 'Mata merah, berair, dan sensitif terhadap cahaya.',
                'description' => 'Infeksi mata yang biasanya disebabkan oleh bakteri Moraxella bovis atau Chlamydia.',
            ],
            [
                'name' => 'Kembung (Bloat/Tympani)',
                'category' => 'Pakan',
                'symptoms' => 'Perut sebelah kiri membesar, ternak gelisah, sulit bernapas.',
                'description' => 'Akumulasi gas yang berlebihan di dalam rumen akibat pakan hijauan muda atau leguminosa.',
            ],
            [
                'name' => 'Cacingan (Helminthiasis)',
                'category' => 'Parasit',
                'symptoms' => 'Ternak kurus, bulu kusam, diare, rahang bawah bengkak (bottle jaw).',
                'description' => 'Infeksi parasit cacing internal pada saluran pencernaan atau hati.',
            ],
            [
                'name' => 'Scabies (Kudis/Gudig)',
                'category' => 'Parasit',
                'symptoms' => 'Gatal-gatal, kulit menebal dan berkerak, bulu rontok.',
                'description' => 'Infeksi kulit yang disebabkan oleh tungau Sarcoptes scabiei.',
            ],
            [
                'name' => 'Pneumonia (Radang Paru)',
                'category' => 'Bakteri/Viral',
                'symptoms' => 'Batuk, pilek, sesak napas, demam tinggi.',
                'description' => 'Infeksi saluran pernapasan bawah yang sering dipicu oleh stres atau cuaca buruk.',
            ],
            [
                'name' => 'Kuku Busuk (Foot Rot)',
                'category' => 'Bakteri',
                'symptoms' => 'Pincang, kuku berbau busuk, luka di sela kuku.',
                'description' => 'Infeksi bakteri pada kuku yang biasanya terjadi di lingkungan lembap dan kotor.',
            ],
            [
                'name' => 'Mastitis (Radang Ambing)',
                'category' => 'Bakteri',
                'symptoms' => 'Ambing bengkak, panas, susu pecah atau bercampur darah.',
                'description' => 'Peradangan pada kelenjar susu akibat infeksi bakteri.',
            ],
            [
                'name' => 'Mencret (Colibacillosis/Diare)',
                'category' => 'Bakteri',
                'symptoms' => 'Feses cair, lemas, dehidrasi.',
                'description' => 'Gangguan pencernaan yang sering menyerang cempe (anak domba/kambing).',
            ],
            [
                'name' => 'Enterotoxemia',
                'category' => 'Bakteri',
                'symptoms' => 'Kematian mendadak, kejang, perut sangat kembung.',
                'description' => 'Penyakit yang disebabkan oleh racun dari bakteri Clostridium perfringens tipe D.',
            ],
        ];

        foreach ($diseases as $disease) {
            DB::table('master_diseases')->updateOrInsert(
                ['name' => $disease['name']],
                array_merge($disease, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not deleting to preserve historical records in exit_logs if rolled back
    }
};
