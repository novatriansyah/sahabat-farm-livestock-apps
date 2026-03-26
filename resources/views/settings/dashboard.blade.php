<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Tampilan Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Atur komponen yang ingin Anda tampilkan di halaman beranda.
                    </p>


                    <form method="POST" action="{{ route('settings.dashboard.store') }}">
                        @csrf
                        <div class="space-y-4">
                            @foreach($availableComponents as $key => $label)
                                <div class="flex items-center justify-between p-4 border rounded-lg dark:border-gray-700">
                                    <div class="flex items-center">
                                        <input id="check_{{ $key }}" name="components[{{ $key }}][is_visible]" type="checkbox" value="1" 
                                            {{ (!isset($userSettings[$key]) || $userSettings[$key]->is_visible) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="check_{{ $key }}" class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500">Urutan:</span>
                                        <input name="components[{{ $key }}][order]" type="number" value="{{ $userSettings[$key]->order ?? 0 }}"
                                            class="w-16 p-1 text-sm bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center gap-4 text-gray-900 border-gray-900	">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                {{ __('Simpan Pengaturan') }}
                            </button>
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Kembali ke Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
