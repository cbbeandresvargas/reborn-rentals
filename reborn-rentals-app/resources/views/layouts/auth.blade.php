<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - Reborn Rentals')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#CE9704',
                        'primary-dark': '#B8860B',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="m-0 w-full h-full font-sans bg-gray-50">
    @yield('content')
    
    @stack('scripts')
</body>
</html>
