<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Semua Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col gap-4">
                        @forelse($notifications as $notification)
                        <div class="flex items-start justify-between p-4 border rounded-lg {{ $notification->read_at ? 'bg-gray-50 dark:bg-gray-700' : 'bg-blue-50 dark:bg-gray-600 border-blue-200' }}">
                            <div class="flex flex-col">
                                <span class="font-bold text-lg">{{ $notification->data['tag_id'] ?? 'Sistem' }}</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $notification->data['message'] }}</span>
                                <span class="text-sm text-gray-500 mt-2">{{ $notification->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if(isset($notification->data['url']))
                            <a href="{{ route('notifications.read', $notification->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                Lihat Detail
                            </a>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            Tidak ada notifikasi.
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
