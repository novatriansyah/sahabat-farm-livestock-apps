<x-app-layout>
    <div class="max-w-xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-center dark:text-white">Scan QR Code Ternak</h2>

        <div id="reader" width="600px"></div>

        <div class="mt-4 text-center">
             <p class="text-gray-600 dark:text-gray-400">Arahkan kamera ke QR Code di telinga ternak.</p>
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

        try {
            let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        } catch (e) {
            console.error("Scanner initialization failed:", e);
            alert("Scanner initialization failed. Please check camera permissions or connection. Error: " + e.message);
        }
    </script>
</x-app-layout>
