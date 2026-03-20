<?php

$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && strpos($file->getFilename(), '.blade.php') !== false) {
        $files[] = $file->getPathname();
    }
}

$replacements = [
    '>Save<' => '>Simpan<',
    '>Cancel<' => '>Batal<',
    '>Delete<' => '>Hapus<',
    '>Edit<' => '>Ubah<',
    '>View<' => '>Lihat<',
    '>Back<' => '>Kembali<',
    '>Create<' => '>Tambah Baru<',
    '>Add<' => '>Tambah<',
    '>Refresh<' => '>Refresh<', // no change
    '>Search<' => '>Cari<',
    'value="Save"' => 'value="Simpan"',
    'value="Cancel"' => 'value="Batal"',
    '>MALE<' => '>JANTAN<',
    '>FEMALE<' => '>BETINA<',
    "'MALE'" => "'JANTAN'",
    "'FEMALE'" => "'BETINA'",
    ">OWNER<" => ">PEMILIK<",
    ">BREEDER<" => ">PETERNAK<",
    ">STAFF<" => ">STAF<",
    ">PARTNER<" => ">MITRA<",
    "'OWNER'" => "'PEMILIK'",
    "'BREEDER'" => "'PETERNAK'",
    "'STAFF'" => "'STAF'",
    "'PARTNER'" => "'MITRA'",
    ">BOUGHT<" => ">BELI<",
    ">BRED<" => ">HASIL_TERNAK<",
    "'BOUGHT'" => "'BELI'",
    "'BRED'" => "'HASIL_TERNAK'",
    "Add New Animal" => "Tambah Ternak Baru",
    "Add Animal" => "Tambah Ternak",
    "Animals list" => "Daftar Ternak",
    "List of all animals" => "Daftar Semua Ternak",
    "Animal Details" => "Detail Ternak",
    '>Status<' => '>Status<',
    '>Action<' => '>Aksi<',
    '>Actions<' => '>Aksi<',
    '>Name<' => '>Nama<',
    '>Description<' => '>Deskripsi<',
    '>Address<' => '>Alamat<',
    '>Contact<' => '>Kontak<',
    '>Phone<' => '>No HP<',
    '>Location<' => '>Lokasi<',
    '>Date<' => '>Tanggal<',
    '>Notes<' => '>Catatan<',
    '>Type<' => '>Tipe<',
    '>Amount<' => '>Jumlah<',
    '>Price<' => '>Harga<',
    '>Total<' => '>Total<',
    '>Print<' => '>Cetak<',
    '>Detail<' => '>Detail<',
    '>Details<' => '>Detail<',
    '>Settings<' => '>Pengaturan<'
];

$regexReplacements = [
    // Handle things like Add New ...
    '/(>)Add New (.*?)(<)/' => function($match) {
        return $match[1] . 'Tambah ' . $match[2] . ' Baru' . $match[3];
    },
    '/(>)Edit (.*?)(<)/' => function($match) {
        return $match[1] . 'Ubah ' . $match[2] . $match[3];
    },
    '/(>)Delete (.*?)(<)/' => function($match) {
        return $match[1] . 'Hapus ' . $match[2] . $match[3];
    },
    '/(>)View (.*?)(<)/' => function($match) {
        return $match[1] . 'Lihat ' . $match[2] . $match[3];
    }
];


$count = 0;
foreach ($files as $file) {
    if (strpos($file, 'resources/views/layouts/sidebar.blade.php') !== false || 
        strpos($file, 'resources/views/layouts/navigation.blade.php') !== false ||
        strpos($file, 'resources/views/dashboard.blade.php') !== false ||
        strpos($file, 'resources/views/welcome.blade.php') !== false) {
        // Skip already translated ones to prevent double translation issues if any
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;

    // String replacements
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Regex replacements
    foreach ($regexReplacements as $pattern => $callback) {
        $content = preg_replace_callback($pattern, $callback, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Translated: " . basename($file) . "\n";
        $count++;
    }
}

echo "Total files translated: $count\n";

