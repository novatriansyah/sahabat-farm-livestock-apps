<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
        <a href="{{ Auth::user()->role == 'PEMILIK' ? route('dashboard') : route('scan.index') }}" class="flex ms-2 md:ms-24">
          <img src="{{ asset('img/logo.png') }}" class="h-8 me-3" alt="Sahabat Farm Logo" />
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Sahabat Farm Indonesia</span>
        </a>

        @if(Auth::user()->role == 'STAF')
        <div class="hidden md:flex ml-4 gap-4">
             <a href="{{ route('scan.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-700 dark:text-gray-300 dark:hover:text-white">Scan QR</a>
             <a href="{{ route('operator.inventory.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-700 dark:text-gray-300 dark:hover:text-white">Pakan/Stok</a>
        </div>
        @endif
      </div>
      <div class="flex items-center">
          
          <!-- Notifications Bell -->
          <div class="flex items-center ms-3">
              <button type="button" data-dropdown-toggle="notification-dropdown" class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 relative">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 20">
                  <path d="M12.133 10.632v-1.8A5.006 5.006 0 0 0 7.5 3.95V3a1.5 1.5 0 0 0-3 0v.95a5.006 5.006 0 0 0-4.633 4.882v1.8a1 1 0 0 0-.293.707v3.536a1 1 0 0 0 1 1h12.5a1 1 0 0 0 1-1v-3.536a1 1 0 0 0-.293-.707Z" />
                  <path d="M4.5 16.5a1.5 1.5 0 0 0 3 0V16h-3v.5Z" />
                </svg>
                @if(Auth::user()->unreadNotifications->count() > 0)
                <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -right-1 dark:border-gray-900">{{ Auth::user()->unreadNotifications->count() }}</div>
                @endif
              </button>
              
              <!-- Dropdown menu -->
              <div class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:divide-gray-600 dark:bg-gray-700" id="notification-dropdown">
                <div class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50 dark:bg-gray-800 dark:text-white">
                    Notifikasi
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-600 max-h-80 overflow-y-auto">
                  @forelse(Auth::user()->notifications()->take(5)->get() as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 dark:bg-gray-600' }}">
                      <div class="w-full pl-3">
                          <div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">
                              <span class="font-semibold text-gray-900 dark:text-white">{{ $notification->data['tag_id'] ?? 'Sistem' }}</span>: {{ $notification->data['message'] ?? '' }}
                          </div>
                          <div class="text-xs text-blue-600 dark:text-blue-500">{{ $notification->created_at->diffForHumans() }}</div>
                      </div>
                    </a>
                  @empty
                    <div class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">Belum ada notifikasi</div>
                  @endforelse
                </div>
                <a href="{{ route('notifications.index') }}" class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">
                  <div class="inline-flex items-center">
                    Lihat Semua
                  </div>
                </a>
              </div>
          </div>

          <div class="flex items-center ms-3">
            <div>
              <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                <span class="sr-only">Open user menu</span>
                <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center text-white">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
              </button>
            </div>
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
              <div class="px-4 py-3" role="none">
                <p class="text-sm text-gray-900 dark:text-white" role="none">
                  {{ Auth::user()->name ?? 'Guest' }}
                </p>
                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                  {{ Auth::user()->email ?? '' }}
                </p>
              </div>
              <ul class="py-1" role="none">
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Keluar</a>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </div>
  </div>
</nav>
