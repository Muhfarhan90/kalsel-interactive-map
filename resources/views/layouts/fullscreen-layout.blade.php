<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Admin | Peta Wisata Kalimantan Selatan</title>
    <script>
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                toggle() {
                    const dark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                }
            });
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-outfit">
    @yield('content')
</body>
</html>
