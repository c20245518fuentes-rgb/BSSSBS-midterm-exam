<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#faf9f7">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>Maison Studio</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="bg-background text-foreground antialiased">
    @inertia
</body>
</html>
