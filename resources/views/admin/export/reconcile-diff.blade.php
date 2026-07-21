@extends('layouts.admin') @section('content')
<div class="container mx-auto p-6">
<h1 class="text-2xl font-bold mb-4">Rekonsiliasi Data</h1>
@if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('admin.export.apply-reconciliation') }}">@csrf
<table class="w-full border-collapse"><thead><tr class="bg-gray-100">
<th><input type="checkbox" id="select-all"></th><th>Tag ID</th><th>Aksi</th><th>Field</th><th>Nilai Lama</th><th>Nilai Baru</th></tr></thead>
<tbody>@forelse($diffs as $i => $diff)
<tr><td><input type="checkbox" name="selected[]" value="{{ $i }}" class="row-checkbox"></td>
<td>{{ $diff['tag_id'] }}</td><td><span class="px-2 py-1 rounded text-sm {{ $diff['action']==='CREATE'?'bg-blue-100 text-blue-800':'bg-yellow-100 text-yellow-800' }}">{{ $diff['action'] }}</span></td>
<td colspan="3">@if($diff['action']==='CREATE')<pre>{{ json_encode($diff['changes'], JSON_PRETTY_PRINT) }}</pre>
@else<ul>@foreach($diff['changes'] as $field => $vals)<li><strong>{{ $field }}</strong>: {{ $vals['old']??'-' }} → {{ $vals['new']??'-' }}</li>@endforeach</ul>@endif</td></tr>
@empty<tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada perbedaan ditemukan</td></tr>@endforelse
</tbody></table>
@if(count($diffs)>0)<div class="mt-4"><button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Terapkan Perubahan Terpilih</button></div>@endif
</form></div>
<script>document.getElementById('select-all')?.addEventListener('change',function(){document.querySelectorAll('.row-checkbox').forEach(c=>c.checked=this.checked);});</script>
@endsection