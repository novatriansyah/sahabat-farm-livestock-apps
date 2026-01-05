# Sahabat Farm Indonesia - Panduan Pengguna (User Manual)

## 1. Ikhtisar Peran (Role Overview)
Sistem ini dibagi menjadi 4 peran khusus, masing-masing dengan tingkat akses yang berbeda:

| Peran (Role) | Tingkat Akses | Fitur Utama |
| :--- | :--- | :--- |
| **OWNER** | **Admin Penuh** | Keuangan, Admin, Laporan, Breeding, Stok, Manajemen Mitra. |
| **BREEDER** | **Tingkat Tinggi** | Breeding, Kesehatan, Stok, Gudang, Invoice, Laporan. |
| **PARTNER** | **Investor (Lihat Saja)** | Dashboard (ROI), Ternak Saya, Laporan. |
| **STAFF** | **Operator** | Log Pakan, Perawatan Harian, Scan QR. |

---

## 2. Panduan OWNER & BREEDER
*(Manajer & Pembuat Keputusan)*

### A. Dashboard & Pemantauan
1.  **Login**: Masuk ke sistem melalui `/login`.
2.  **Dashboard**:
    *   **Statistik Cepat**: Total Populasi, Ternak Sakit, Penjualan Bulan Ini.
    *   **Notifikasi (Alerts)**: Periksa bagian "Notifications" atau "Pending Tasks" untuk:
        *   **Vaksin Jatuh Tempo**: Ternak yang perlu vaksin hari ini/besok.
        *   **Siap Sapih (Weaning)**: Cempe usia >40 hari yang siap dipisahkan.
    *   **Keuangan**: Grafik penjualan dan keuntungan real-time (Khusus Owner).

### B. Manajemen Ternak (Data Ternak)
*   **Tambah Ternak Baru**:
    1.  Buka **Data Ternak** > **+ Tambah Baru**.
    2.  Pilih **Sumber**: "Lahir di Farm" atau "Beli".
    3.  **Penting**: Status otomatis diterapkan untuk ternak baru lahir (<40 hari = Menyusui).
*   **Update Status**:
    *   **Sapih**: Sistem otomatis mengubah status saat usia 40 hari, tapi lokasi kandang perlu dipindah manual.
    *   **Edit**: Gunakan tombol Edit untuk mengubah detail data.
    *   **Keluar (Mati/Jual/Potong)**: Gunakan tombol "Exit" (Keluar) untuk mencatat kematian atau pemotongan.
*   **Breeding (Perkawinan)**:
    1.  Buka tab **Breeding** pada profil ternak.
    2.  Catat Data Kawin. Sistem otomatis menghitung Estimasi Lahir (+150 hari).

### C. Gudang & Keuangan
*   **Inventory (Pakan & Obat)**:
    *   **Stok Masuk**: Tambahkan karung pakan atau obat baru via menu **Gudang & Pakan**.
    *   **Pemakaian**: Biasanya dicatat oleh STAFF, tapi Anda bisa mengoreksi stok di sini.
*   **Invoices (Penjualan)**:
    1.  Buka **Invoices** > **Create New**.
    2.  **Pilih Pelanggan**: Atau buat baru (Alamat & Pajak sudah aktif).
    3.  **Tambah Item**: Pilih ternak spesifik (berdasarkan Tag ID) yang akan dijual. Harga otomatis terisi (bisa diedit).
    4.  **Uang Muka (DP)**: Masukkan jumlah DP jika ada.
    5.  **Status**: Tandai sebagai **PAID** (Lunas) untuk otomatis memindahkan status ternak menjadi "SOLD" (Terjual) dan menghapusnya dari stok aktif.

### D. Laporan (Reports)
*   **Stok & Populasi**: Jumlah ternak saat ini per Kandang dan Gender. Gunakan "Cetak (Print All)" untuk cetak A4.
*   **Performa (ADG)**: Analisis pertumbuhan bobot. Cek "Top 10" untuk melihat genetik terbaik.
*   **Penjualan**: Omset bulanan dan profit/margin (Khusus Owner).
*   **Reproduksi**: Melacak produktivitas induk (Jumlah anak, interval kelahiran).
*   **Laporan Mitra**: Laporan khusus untuk Investor (ROI & Nilai Aset).

---

## 3. Panduan PARTNER
*(Investor / Mitra)*

### A. Mengakses Data Anda
1.  **Login**: Gunakan kredensial yang diberikan oleh Admin SFI.
2.  **Dashboard Partner**: Halaman utama khusus Anda.
    *   **Nilai Aset**: Estimasi total nilai ternak Anda saat ini.
    *   **Populasi**: Jumlah ternak yang Anda miliki.
    *   **Pertumbuhan**: Tren kenaikan bobot ternak spesifik Anda.

### B. Ternak Saya
*   Buka menu **Data Ternak**. Anda HANYA akan melihat ternak milik Anda.
*   **Cari**: Masukkan Tag ID untuk mencari ternak tertentu.
*   **Detail**: Klik pada ternak untuk melihat riwayat bobot, foto, dan catatan kesehatan.

---

## 4. Panduan STAFF
*(Operator Lapangan / Anak Kandang)*

### A. Rutinitas Harian
1.  **Scan QR**: Gunakan menu **Scan QR** untuk identifikasi cepat ternak di kandang (perlu izin kamera).
    *   Profil ternak akan langsung terbuka.
2.  **Pakan (Feeding)**:
    *   Buka menu **Feeding / Usage** (jika diberi akses).
    *   Catat jumlah pakan (karung/kg) yang diambil dari gudang ke kandang.
3.  **Cek Kesehatan**:
    *   Segera laporkan ternak yang terlihat sakit atau lesu kepada Breeder/Manajer.

---

## 5. Administrasi & Master Data (Khusus Admin)

### A. Manajemen User
*   **Buat User**: Buka **Admin Area** > **Manajemen User**.
*   **Atur Role**: Hati-hati saat memberikan role OWNER atau BREEDER.
*   **Link Partner**: Untuk Investor, pilih Role "PARTNER" lalu pilih Nama Mitra mereka dari dropdown.

### B. Master Data (Pengaturan Farm)
*   **Akses**: Buka **Admin Area** > **Pengaturan Farm**.
*   **Fitur**:
    *   **Breeds (Ras)**: Tambah/Edit ras hewan (contoh: Dorper, Merino).
    *   **Locations (Lokasi)**: Kelola nama kandang/pen.
    *   **Diseases (Penyakit)**: Daftar penyakit umum untuk pencatatan kesehatan.
    *   **Items (Barang)**: Daftarkan jenis pakan dan obat (contoh: Konsentrat A, Vitamin B).
    *   **Categories (Kategori)**: Kelola kategori hewan dan inventaris.

---

## 6. Deployment & Perawatan Sistem (Teknis)

### A. Backup & Update
*   **Backup Database**: Export file SQL dari phpMyAdmin setiap minggu.
*   **Update Sistem**:
    *   Upload file baru.
    *   Jalankan `php artisan migrate --force` jika ada perubahan database.


