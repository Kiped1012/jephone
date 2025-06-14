@extends('components.layout')

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
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="bg-blue-900 text-white rounded-t-lg px-6 py-4">
        <h2 class="text-xl font-semibold">🙎🏻‍♂️ {{ isset($isEdit) ? 'Edit' : 'Entri' }} User</h2>
        <p class="text-sm mt-1">Master / Data User / {{ isset($isEdit) ? 'Edit' : 'Entri' }} Data</p>
    </div>

    <form id="formUser" action="{{ isset($isEdit) ? route('user.update', $id) : route('user.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        @if(isset($isEdit)) @method('PUT') @endif

        <!-- Hidden ID -->
        <input type="hidden" name="id_usr" id="idUser" value="{{ isset($user) ? $user['id_usr'] : '' }}">

        <!-- Baris 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Username</label>
                <input type="text" name="username" id="username" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-indigo-400" required value="{{ $user['username'] ?? '' }}">
            </div>
            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-indigo-400" required value="{{ $user['email'] ?? '' }}">
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Role</label>
                <select name="role" id="role" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-indigo-100 focus:ring-2 focus:ring-indigo-400" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="Admin" {{ ($user['role'] ?? '') === 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Kasir" {{ ($user['role'] ?? '') === 'Kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
            </div>
        </div>

        <!-- Password Fields - Hanya tampil jika bukan mode edit -->
        @if(!isset($isEdit))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="passwordField" class="relative">
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 focus:ring-2 focus:ring-indigo-400" required>
                <button type="button" onclick="togglePassword('password', this)" class="absolute top-9 right-3 text-gray-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path id="eye-password" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            <div id="confirmPasswordField" class="relative">
                <label class="block font-semibold mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 focus:ring-2 focus:ring-indigo-400" required>
                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute top-9 right-3 text-gray-500 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path id="eye-confirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <div class="flex justify-end gap-4 pt-4">
            <button id="btnSimpan" type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-md shadow-md">{{ isset($isEdit) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('user.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-md shadow-md">Batal</a>
        </div>
    </form>
</div>

<script>
    window.dataUserTersimpan = @json(include(resource_path('data/user.php')));
    window.editUser = @json($user ?? null);
    window.isEditMode = @json(isset($isEdit));

    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';

        // Ganti warna icon jadi hijau jika aktif
        btn.classList.toggle('text-green-600', !isVisible);
        btn.classList.toggle('text-gray-500', isVisible);
    }
</script>

@vite(['resources/js/entriuser.js'])

@endsection