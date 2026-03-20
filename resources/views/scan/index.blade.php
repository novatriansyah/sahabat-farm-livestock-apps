<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                Smart Scan QR
            </h2>
            <p class="mt-3 text-lg text-gray-500 dark:text-gray-400">
                Pindai QR Code pada telinga ternak untuk akses cepat data & riwayat.
            </p>
        </div>

        <!-- Scanner Container Card -->
        <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <!-- Mode Toggle / Tabs -->
            <div class="flex border-b border-gray-100 dark:border-gray-700">
                <button id="btn-mode-camera" onclick="switchMode('camera')" class="flex-1 py-4 text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2 border-b-4 border-indigo-600 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kamera Langsung
                </button>
                <button id="btn-mode-gallery" onclick="switchMode('gallery')" class="flex-1 py-4 text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2 border-b-4 border-transparent text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Unggah Galeri
                </button>
            </div>

            <!-- Scanning Area -->
            <div class="p-6">
                <!-- Camera View -->
                <div id="camera-section" class="relative">
                    <div id="reader-container" class="relative bg-gray-900 rounded-2xl overflow-hidden aspect-square flex items-center justify-center group">
                        <div id="reader" class="w-full h-full"></div>
                        
                        <!-- Scanning Animation Overlay -->
                        <div id="scan-overlay" class="absolute inset-0 pointer-events-none opacity-0 transition-opacity duration-300">
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-2xl opacity-50 animate-pulse"></div>
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent shadow-[0_0_15px_rgba(79,70,229,1)] animate-scanner-line"></div>
                        </div>

                        <!-- Placeholder / Start Button -->
                        <div id="reader-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-gray-900 bg-opacity-70 transition-all duration-300 z-10">
                            <div class="bg-indigo-600 p-4 rounded-full mb-4 shadow-lg active:scale-95 cursor-pointer transition-transform" onclick="startCamera()">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-medium">Klik untuk Aktifkan Kamera</p>
                        </div>
                    </div>

                    <div id="camera-controls" class="hidden mt-4 flex justify-center gap-4">
                        <button onclick="stopCamera()" class="px-6 py-2 bg-red-100 text-red-700 font-bold rounded-xl active:bg-red-200 transition-colors">
                            Hentikan Kamera
                        </button>
                    </div>
                </div>

                <!-- Gallery Section -->
                <div id="gallery-section" class="hidden text-center py-10">
                    <div class="border-3 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl p-10 hover:border-indigo-400 transition-all duration-300 group cursor-pointer" onclick="document.getElementById('qr-file-input').click()">
                        <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="handleFileUpload(event)">
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <h4 class="text-xl font-bold dark:text-white mb-2">Pilih Foto QR Code</h4>
                        <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto">Klik untuk memilih gambar dari galeri Anda. Pastikan QR code terlihat jelas.</p>
                    </div>
                </div>

                <!-- Result Message (Hidden by default) -->
                <div id="scan-message" class="mt-6 p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-100 text-sm hidden">
                    <div class="flex items-center gap-3">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-indigo-600 border-t-transparent"></div>
                        <p id="message-text">Memproses QR Code...</p>
                    </div>
                </div>
            </div>

            <!-- Footer Help -->
            <div class="px-6 pb-6 text-center">
                <div class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-4 py-2 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Posisi QR Code di telinga sebelah kanan ternak
                </div>
            </div>
        </div>
    </div>

    <!-- Styles for Animation -->
    <style>
        @keyframes scannerLine {
            0% { top: 0; }
            50% { top: 100%; opacity: 1; }
            100% { top: 0; }
        }
        .animate-scanner-line {
            animation: scannerLine 3s ease-in-out infinite;
        }
        .animate-scanner-line {
            animation: scannerLine 2s linear infinite;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;
        let currentMode = 'camera';

        // Initialize scanner on load
        document.addEventListener("DOMContentLoaded", () => {
            html5QrCode = new Html5Qrcode("reader");
        });

        function switchMode(mode) {
            currentMode = mode;
            const btnCam = document.getElementById('btn-mode-camera');
            const btnGal = document.getElementById('btn-mode-gallery');
            const camSec = document.getElementById('camera-section');
            const galSec = document.getElementById('gallery-section');

            if (mode === 'camera') {
                btnCam.classList.add('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btnCam.classList.remove('border-transparent', 'text-gray-500');
                btnGal.classList.add('border-transparent', 'text-gray-500');
                btnGal.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                camSec.classList.remove('hidden');
                galSec.classList.add('hidden');
            } else {
                btnGal.classList.add('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btnGal.classList.remove('border-transparent', 'text-gray-500');
                btnCam.classList.add('border-transparent', 'text-gray-500');
                btnCam.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                galSec.classList.remove('hidden');
                camSec.classList.add('hidden');
                stopCamera();
            }
        }

        async function startCamera() {
            document.getElementById('reader-placeholder').classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('scan-overlay').classList.remove('opacity-0');
            document.getElementById('camera-controls').classList.remove('hidden');
            
            const config = { fps: 15, qrbox: { width: 250, height: 250 } };

            try {
                await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
            } catch (err) {
                console.error("Camera access failed", err);
                alert("Gagal mengakses kamera. Mohon pastikan izin kamera telah diberikan.");
                stopCamera();
            }
        }

        async function stopCamera() {
            if (html5QrCode && html5QrCode.getState() === 2) {
                await html5QrCode.stop();
            }
            document.getElementById('reader-placeholder').classList.remove('opacity-0', 'pointer-events-none');
            document.getElementById('scan-overlay').classList.add('opacity-0');
            document.getElementById('camera-controls').classList.add('hidden');
        }

        function onScanSuccess(decodedText, decodedResult) {
            showMessage("Berhasil! Mengalihkan ke data ternak...");
            // Redirect after a short delay for feedback
            setTimeout(() => {
                window.location.href = decodedText;
            }, 800);
        }

        async function handleFileUpload(event) {
            if (event.target.files.length === 0) return;
            
            const imageFile = event.target.files[0];
            showMessage("Membaca file gambar...");

            try {
                const decodedText = await html5QrCode.scanFile(imageFile, true);
                onScanSuccess(decodedText);
            } catch (err) {
                console.error("Scan from file failed", err);
                alert("QR Code tidak ditemukan pada gambar. Pastikan gambar jelas dan terang.");
                hideMessage();
            }
        }

        function showMessage(text) {
            document.getElementById('scan-message').classList.remove('hidden');
            document.getElementById('message-text').innerText = text;
        }

        function hideMessage() {
            document.getElementById('scan-message').classList.add('hidden');
        }
    </script>
</x-app-layout>
