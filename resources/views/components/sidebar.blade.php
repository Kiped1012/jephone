<aside class="w-64 bg-[#CBD1F8] shadow-lg flex flex-col px-4 py-6 justify-between min-h-screen">
    <div>
        <div class="flex items-center space-x-3 mb-6 px-2">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full border">
            <div>
                <p class="font-bold text-lg text-[#293462]">Administrator</p>
                <p class="text-sm text-[#293462] font-semibold">Administrator</p>
            </div>
        </div>

        <nav class="flex flex-col text-sm space-y-1 text-black font-semibold">
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Dashboard
            </a>

            <p class="text-xs text-gray-700 font-bold uppercase px-3 mt-4 mb-2">Master</p>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Barang
            </a>

            <p class="text-xs text-gray-700 font-bold uppercase px-3 mt-4 mb-2">Transaksi</p>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m0 0l-4-4m4 4l4-4M4 4h16v16H4z"/>
                </svg>
                Barang Masuk
            </a>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 20V4m0 0l-4 4m4-4l4 4M4 20h16V4H4z"/>
                </svg>
                Barang Keluar
            </a>

            <p class="text-xs text-gray-700 font-bold uppercase px-3 mt-4 mb-2">Laporan</p>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 2h6a2 2 0 0 1 2 2v2h-2a2 2 0 0 0-2 2H9a2 2 0 0 0-2-2H5V4a2 2 0 0 1 2-2zM5 8h14v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/>
                </svg>
                Laporan Stock
            </a>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m2 8H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2z"/>
                </svg>
                Laporan Barang Masuk
            </a>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m2 8H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2z"/>
                </svg>
                Laporan Barang Keluar
            </a>

            <p class="text-xs text-gray-700 font-bold uppercase px-3 mt-4 mb-2">Pengaturan</p>
            <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/>
                </svg>
                Manajemen User
            </a>
        </nav>
    </div>

    <!-- Logout -->
    <div class="mt-auto">
        <a href="#" class="flex items-center py-2 px-3 rounded hover:bg-[#293462] hover:text-white transition-colors text-black font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
            Logout
        </a>
    </div>
</aside>
