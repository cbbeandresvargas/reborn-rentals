<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Reborn Rentals')</title>
    
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
                        'bg-dark': '#4A4A4A',
                        'bg-cart': '#2F2F2F',
                        'bg-light': '#BBBBBB',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="m-0 w-full h-full font-sans bg-gray-50">
    @include('admin.sidebar')
    
    <!-- Main Content -->
    <main class="min-h-screen transition-all duration-300 ease-in-out">
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>
