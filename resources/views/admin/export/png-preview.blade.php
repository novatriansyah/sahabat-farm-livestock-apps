@extends('layouts.admin') @section('content')
<div class="container mx-auto p-6"><h1 class="text-2xl font-bold mb-4">Preview Laporan {{ $reportType }}</h1>
<p>Filter: {{ json_encode($filters) }}</p>
<div class="mt-4 flex gap-2">
<a href="{{ route('admin.reports.export.download', ['reportType'=>$reportType,'format'=>'pdf']).'?'.http_build_query($filters) }}" class="px-4 py-2 bg-red-600 text-white rounded">Download PDF</a>
<a href="{{ route('admin.reports.export.download', ['reportType'=>$reportType,'format'=>'excel']).'?'.http_build_query($filters) }}" class="px-4 py-2 bg-green-600 text-white rounded">Download Excel</a>
</div></div>@endsection