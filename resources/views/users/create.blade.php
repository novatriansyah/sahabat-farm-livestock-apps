<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Register New User</h2>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-1">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                    <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="STAFF" {{ (isset($preselectedRole) && $preselectedRole == 'STAFF') ? 'selected' : '' }}>Staff (Operator)</option>
                        <option value="BREEDER" {{ (isset($preselectedRole) && $preselectedRole == 'BREEDER') ? 'selected' : '' }}>Breeder</option>
                        <option value="OWNER" {{ (isset($preselectedRole) && $preselectedRole == 'OWNER') ? 'selected' : '' }}>Owner (Manager)</option>
                        <option value="PARTNER" {{ (isset($preselectedRole) && $preselectedRole == 'PARTNER') ? 'selected' : '' }}>Partner (Investor)</option>
                    </select>
                </div>
                
                <!-- Partner Select (Hidden by Default) -->
                <div id="partner-select-wrapper" style="display: {{ (isset($preselectedRole) && $preselectedRole == 'PARTNER') ? 'block' : 'none' }};">
                    <label for="partner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Assign Partner Profile</label>
                    <select id="partner_id" name="partner_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">Select Partner entity...</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ (isset($preselectedPartnerId) && $preselectedPartnerId == $partner->id) ? 'selected' : '' }}>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <script>
                    document.getElementById('role').addEventListener('change', function() {
                        const wrapper = document.getElementById('partner-select-wrapper');
                        wrapper.style.display = (this.value === 'PARTNER') ? 'block' : 'none';
                    });
                </script>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Register User</button>
        </form>
    </div>
</x-app-layout>
