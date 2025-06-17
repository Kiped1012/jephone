<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - JEPhone</title>
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
                        "Buat password baru yang kuat untuk melindungi akun Anda."
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="w-1/2 p-10">
                <h2 class="text-gray-800 text-lg">Reset Password</h2>
                <h1 class="text-4xl font-extrabold mb-2">Password Baru</h1>
                <p class="text-gray-600 text-sm mb-6">Masukkan password baru Anda.</p>

                @if (session('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @elseif (session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('reset.password.submit') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" placeholder="Masukkan password baru" required
                                   id="new_password" minlength="8"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <button type="button" id="toggleNewPassword" class="absolute right-3 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru" required
                                   id="confirm_password" minlength="8"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="bg-[#2d4a8a] hover:bg-[#1e3366] text-white w-full py-2 rounded-lg font-medium shadow-md transition mb-4">
                        Reset Password
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Kembali ke 
                        <a href="{{ route('login') }}" class="text-[#2d4a8a] hover:text-[#1e3366] font-medium">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const newPasswordInput = document.getElementById('new_password');

        toggleNewPassword.addEventListener('click', function() {
            const isPassword = newPasswordInput.type === 'password';
            newPasswordInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('text-green-500', isPassword);
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');

        toggleConfirmPassword.addEventListener('click', function() {
            const isPassword = confirmPasswordInput.type === 'password';
            confirmPasswordInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('text-green-500', isPassword);
        });
    </script>


</body>
</html>
