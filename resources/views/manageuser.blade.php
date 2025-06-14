@extends('components.layout')
@if (session('success'))
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="fixed right-4 top-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
        x-cloak>
        <div class="flex justify-between items-center">
            <span class="text-sm">{{ session('success') }}</span>
            <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    </div>
@endif
<div x-data="{ show: false, message: '' }" 
    x-show="show"
    x-on:show-success.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
    class="fixed right-4 top-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
    x-cloak>
    <div class="flex justify-between items-center">
        <span class="text-sm" x-text="message"></span>
        <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
    </div>
</div>
@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-[#274C8C] text-white rounded-t-lg px-6 py-4 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-semibold">🙎🏻‍♂️ Manajemen User</h2>
        <p class="text-sm mt-1">Pengaturan / Manajemen User</p>
    </div>
        <a href="{{ route('user.create') }}">
            <button class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                + Entri Data
            </button>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-b-lg shadow p-6">
        <div class="flex justify-between mb-4">
            <div>
                <label for="items" class="mr-2">Show</label>
                <select id="items" class="border rounded px-2 py-1">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <span class="ml-2">items</span>
            </div>
            <div>
                <label for="search" class="mr-2">Cari:</label>
                <input type="text" id="search" class="border px-2 py-1 rounded" placeholder="Search...">
            </div>
        </div>

        <table class="w-full text-left border-t border-b">
            <thead class="border-b">
                <tr>
                    <th class="py-2 px-3">No</th>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">Email</th>
                    <th class="py-2 px-3">Role</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $users = collect(include resource_path('data/user.php'));
                @endphp

                @foreach ($users as $index => $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3">{{ $index + 1 }}</td>
                        <td class="py-2 px-3">{{ $user['username'] }}</td>
                        <td class="py-2 px-3">{{ $user['email'] }}</td>
                        <td class="py-2 px-3">{{ $user['role'] }}</td>
                        <td class="py-2 px-3 flex gap-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('user.edit', $user['id_usr']) }}" title="Edit">
                                <span class="text-blue-600 hover:text-blue-800 text-lg">✏️</span>
                            </a>    

                            <!-- Tombol Hapus -->
                            <form action="{{ route('user.destroy', $user['id_usr']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus">
                                    <span class="text-red-600 hover:text-red-800 text-lg">🗑️</span>
                                </button>
                            </form>
                        </td>           
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
