<x-app-layout>
    <div class="max-w-xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-center dark:text-white">Scan QR Code Ternak</h2>

        <div id="reader" width="600px"></div>

        <div class="mt-4 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-4">Arahkan kamera ke QR Code di telinga ternak.</p>

            <button id="request-permission-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition hidden">
                Izinkan Kamera
            </button>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Handle the scanned code as you like, for example:
            console.log(`Code matched = ${decodedText}`, decodedResult);
            // Assuming the QR code contains the URL to the operator page
            window.location.href = decodedText;
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // for example:
            // console.warn(`Code scan error = ${error}`);
        }

        function startScanner() {
            try {
                let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false);
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);

                // Hide button if successful
                document.getElementById('request-permission-btn').classList.add('hidden');
            } catch (e) {
                console.error("Scanner initialization failed:", e);
                // Show button if failed (likely due to permission)
                document.getElementById('request-permission-btn').classList.remove('hidden');
                alert("Gagal memulai kamera. Pastikan izin kamera diberikan.");
            }
        }

        // Attempt to start immediately
        startScanner();

        // Button handler to retry
        document.getElementById('request-permission-btn').addEventListener('click', function() {
            // Explicitly request permissions via API if possible, or just retry initialization
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    startScanner();
                }
            }).catch(err => {
                alert("Izin kamera ditolak atau tidak ada kamera yang ditemukan.");
            });
        });
    </script>
</x-app-layout>
