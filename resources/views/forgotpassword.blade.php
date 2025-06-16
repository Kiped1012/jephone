<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - JEPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cover bg-center font-sans" style="background-image: url('/images/background.jpg');">

    <div class="min-h-screen flex items-center justify-center">
        <div class="flex bg-white rounded-3xl overflow-hidden shadow-xl w-[950px]">
            
            <!-- Kiri -->
            <div class="w-1/2 p-10 flex flex-col justify-between bg-cover bg-center" style="background-image: url('/images/gradient.jpg');">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ asset('images/jephone.png') }}" alt="Logo" class="h-25 mb-6">
                    <p class="text-black text-lg font-medium leading-relaxed max-w-xs">
                        "Reset password Anda dengan mudah dan aman. Kami akan mengirimkan link reset ke email Anda."
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="w-1/2 p-10">
                <div class="mb-4">
                    <a href="{{ route('login') }}" class="text-[#2d4a8a] hover:text-[#1e3366] text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Login
                    </a>
                </div>

                <h2 class="text-gray-800 text-lg">Lupa Password?</h2>
                <h1 class="text-4xl font-extrabold mb-2">Reset Password</h1>
                <p class="text-gray-600 text-sm mb-6">Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>

                @if (session('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @elseif (session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('forgot.password.submit') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" placeholder="Masukkan email Anda" required
                               value="{{ old('email') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="bg-[#2d4a8a] hover:bg-[#1e3366] text-white w-full py-2 rounded-lg font-medium shadow-md transition mb-4">
                        Kirim Link Reset Password
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah ingat password Anda? 
                        <a href="{{ route('login') }}" class="text-[#2d4a8a] hover:text-[#1e3366] font-medium">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>