<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Filter Laporan</h3>
    <form method="GET" action="{{ route('admin.reports.export.download', ['reportType' => $reportType ?? 'population', 'format' => 'excel']) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium">Periode</label><select name="period" class="mt-1 block w-full rounded border-gray-300"><option value="harian">Harian</option><option value="mingguan">Mingguan</option><option value="bulanan">Bulanan</option><option value="tahunan">Tahunan</option><option value="custom">Custom</option></select></div>
        <div><label class="block text-sm font-medium">Dari</label><input type="date" name="from" class="mt-1 block w-full rounded border-gray-300"></div>
        <div><label class="block text-sm font-medium">Sampai</label><input type="date" name="to" class="mt-1 block w-full rounded border-gray-300"></div>
        <div><label class="block text-sm font-medium">Mitra</label><select name="partner_id" class="mt-1 block w-full rounded border-gray-300"><option value="">Semua</option></select></div>
        <div><label class="block text-sm font-medium">Kandang</label><select name="location_id" class="mt-1 block w-full rounded border-gray-300"><option value="">Semua</option></select></div>
        <div><label class="block text-sm font-medium">Status</label><select name="status" class="mt-1 block w-full rounded border-gray-300"><option value="">Semua</option><option value="aktif">Aktif</option><option value="nonaktif">Non-Aktif</option></select></div>
        <div class="md:col-span-3 flex gap-2 pt-4 border-t">
            <button type="submit" name="format" value="pdf" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">PDF</button>
            <button type="submit" name="format" value="excel" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Excel</button>
            <button type="submit" name="format" value="ppt" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">PPT</button>
            <button type="submit" name="format" value="png" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">PNG</button>
            <button type="submit" name="format" value="csv" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">CSV</button>
        </div>
    </form>
</div>