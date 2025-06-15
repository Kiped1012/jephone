@extends('components.layout')

{{-- Notifikasi sukses --}}
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

{{-- Notifikasi sukses dengan event listener --}}
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

{{-- Notifikasi error --}}
<div x-data="{ show: false, message: '' }"
    x-show="show"
    x-on:show-error.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
    class="fixed right-4 top-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
    x-cloak>
    <div class="flex justify-between items-center">
        <span class="text-sm" x-text="message"></span>
        <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
    </div>
</div>

@section('content')
<div class="p-6">
    {{-- Tabel User Management dengan Header Tergabung --}}
    <div class="bg-white shadow-md rounded-xl">
        {{-- Header --}}
        <div class="bg-[#274C8C] text-white rounded-t-xl px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">🙎🏻‍♂️ Manajemen User</h2>
                <p class="text-sm mt-1 opacity-80">Pengaturan / Manajemen User</p>
            </div>
            <a href="{{ route('user.create') }}">
                <button class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                    + Entri Data
                </button>
            </a>
        </div>

        {{-- Controls Section --}}
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                {{-- Show Entries --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Show</label>
                    <select id="items" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="text-sm text-gray-600">entries</label>
                </div>

                {{-- Search --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Search:</label>
                    <input type="text" id="search" placeholder="Cari user..." 
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $users = collect(include resource_path('data/user.php'))->sortBy('username')->values();
                    @endphp

                    @foreach ($users as $index => $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $index + 1 }}</td>
                            <td class="py-2 px-3">{{ $user['username'] }}</td>
                            <td class="py-2 px-3">{{ $user['email'] }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded-xl text-xs font-semibold 
                                    {{ $user['role'] === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($user['role']) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 flex gap-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('user.edit', $user['id_usr']) }}" title="Edit">
                                    <span class="text-blue-600 hover:text-blue-800 text-lg cursor-pointer">✏️</span>
                                </a>    

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('user.destroy', $user['id_usr']) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus">
                                        <span class="text-red-600 hover:text-red-800 text-lg cursor-pointer">🗑️</span>
                                    </button>
                                </form>
                            </td>           
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination akan ditambahkan oleh JavaScript --}}
    </div>
</div>

{{-- Include JavaScript file --}}
@vite(['resources/js/manageuser.js'])
@endsection