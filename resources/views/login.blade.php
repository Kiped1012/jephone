<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - JEPhone</title>
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
                        “Penjualan bukan hanya soal angka, tapi soal komitmen menjaga kualitas dan ketepatan dalam setiap pengiriman sparepart.”
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="w-1/2 p-10">
                <h2 class="text-gray-800 text-lg">Selamat Datang!</h2>
                <h1 class="text-4xl font-extrabold mb-6">Login</h1>

                @if (session('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @elseif (session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Masukkan nama pengguna</label>
                        <input type="text" name="username" placeholder="Nama pengguna"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Masukkan kata sandi</label>
                        <div class="relative">
                            <input type="password" name="password" placeholder="Kata sandi"
                                id="password"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">

                            <!-- Icon show/hide password -->
                            <button type="button" id="togglePassword" class="absolute right-3 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path id="eyeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-right text-sm text-red-500 mt-1">
                            <a href="#">Lupa Kata Sandi</a>
                        </div>
                    </div>
                    <button type="submit" class="bg-[#2d4a8a] hover:bg-[#1e3366] text-white w-full py-2 rounded-lg font-medium shadow-md transition">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
