<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/alpinejs" defer></script>
    <title>JePhone</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        {{-- Sidebar tetap (sticky) --}}
        <div class="w-64 bg-blue-100 flex flex-col justify-between h-screen fixed left-0 top-0">
            @if(session('role') === 'Admin')
                @include('components.sidebaradmin')
            @else
                @include('components.sidebarkasir')
            @endif
        </div>

        {{-- Konten utama geser kanan dan scrollable --}}
        <div class="flex-1 ml-64 p-6 relative z-10">
            <div class="min-h-screen">
                @yield('content')
            </div>
        </div>
    </div>

</body>
</html>