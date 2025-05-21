<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex bg-gray-100 min-h-screen">

    {{-- Sidebar tetap tinggi penuh dan logout di bawah --}}
    @if(session('user_role') === 'admin')
        <div class="w-64 bg-blue-100 flex flex-col justify-between h-screen">
            @include('components.sidebaradmin')
        </div>
    @else
        <div class="w-64 bg-blue-100 flex flex-col justify-between h-screen">
            @include('components.sidebarkasir')
        </div>
    @endif

    {{-- Konten utama scrollable saja --}}
    <main class="flex-1 overflow-y-auto p-6">
        @yield('content')
    </main>

</body>
</html>
