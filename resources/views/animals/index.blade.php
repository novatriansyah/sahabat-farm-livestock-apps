<x-app-layout>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Animals</h2>
        <a href="{{ route('animals.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add New Animal</a>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tag ID</th>
                    <th scope="col" class="px-6 py-3">Breed</th>
                    <th scope="col" class="px-6 py-3">Gender</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Location</th>
                    <th scope="col" class="px-6 py-3">ADG (kg/day)</th>
                    <th scope="col" class="px-6 py-3">HPP (IDR)</th>
                    <th scope="col" class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($animals as $animal)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $animal->tag_id }}
                    </th>
                    <td class="px-6 py-4">{{ $animal->breed->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $animal->gender }}</td>
                    <td class="px-6 py-4">
                        @if($animal->health_status == 'HEALTHY')
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Healthy</span>
                        @elseif($animal->health_status == 'SICK')
                            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Sick</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ $animal->health_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $animal->location->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ number_format($animal->daily_adg, 3) }}</td>
                    <td class="px-6 py-4">{{ number_format($animal->current_hpp, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $animals->links() }}
        </div>
    </div>
</x-app-layout>
