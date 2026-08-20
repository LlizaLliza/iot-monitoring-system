<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IoT Monitoring' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50">
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>

</html>