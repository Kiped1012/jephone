<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="flex bg-gray-100 min-h-screen">
    @if(session('user_role') === 'admin')
        @include('components.sidebaradmin')
    @else
        @include('components.sidebarkasir')
    @endif

    <div class="flex-1 p-6">
        {{ $slot }}
    </div>
</body>
</html>
