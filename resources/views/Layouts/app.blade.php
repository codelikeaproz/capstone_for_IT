<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'BukidnonAlert')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('styles/app_layout/app.css') }}">

    <!-- FontAwesome Icons - Multiple sources for reliability -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>


    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Sidebar CSS -->
    <style>
        .sidebar {
            transition: all 0.3s ease;
            width: 256px !important; /* Force width */
            min-width: 256px;
            max-width: 256px;
        }

        .sidebar.collapsed {
            width: 80px !important; /* Force collapsed width */
            min-width: 80px;
            max-width: 80px;
        }

        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text {
            display: none !important;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .sidebar.collapsed .menu-toggle {
            justify-content: center;
        }

        .sidebar.collapsed .users-submenu {
            display: none !important;
        }

        .sidebar .nav-text,
        .sidebar .logo-text {
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        .content {
            transition: all 0.3s ease;
            flex: 1;
            min-width: 0; /* Important: allows flex item to shrink below content size */
        }

        /* Ensure proper flex layout - no margins needed */
        .layout-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Content should expand to fill available space */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
            width: 100%; /* Ensure full width usage */
        }

        /* Ensure main content uses full available width */
        .content-wrapper main {
            width: 100%;
        }

        .content-wrapper main > div {
            width: 100%;
            max-width: none !important; /* Override any max-width constraints */
        }

        .users-submenu {
            transition: all 0.3s ease;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }

        .users-submenu.show {
            max-height: 200px;
            opacity: 1;
        }

        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        .bg-brick-orange {
            background-color: #c14a09 !important;
        }

        .text-brick-orange {
            color: #c14a09 !important;
        }

        .border-brick-orange {
            border-color: #c14a09 !important;
        }

        /* Force override for menu toggle button */
        .menu-toggle {
            cursor: pointer;
        }

        .menu-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }

        /* Make sure icons are visible and load properly */
        .sidebar i {
            display: inline-block !important;
            min-width: 20px;
            text-align: center;
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome", sans-serif !important;
            font-weight: 900 !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Force icon font loading */
        .fas, .far, .fab {
            font-family: "Font Awesome 6 Free" !important;
        }

        /* Ensure sidebar elements are properly aligned */
        .sidebar .flex.items-center {
            align-items: center !important;
        }

        /* Additional debug styles for testing */
        .sidebar.debug {
            border: 2px solid red !important;
        }

        .menu-toggle.debug {
            background-color: yellow !important;
            color: black !important;


        }

    </style>
</head>
<body class="min-h-screen bg-base-200">
    <div class="layout-container">
        @include('Components.SideBar')
        <div class="content-wrapper">
            <main class="flex-1 overflow-y-auto p-0">
                <div class="w-full mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @include("Components.Footer")

    <!-- Leaflet JS for maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <!-- Global Toast Functions -->
    <script>
        // Global toast notification functions
        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        function showInfoToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        function showWarningToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        function showErrorToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast toast-end z-[9999]';
            toast.innerHTML = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        // Auto-display Laravel session flash messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccessToast("{{ session('success') }}");
            @endif

            @if(session('error'))
                showErrorToast("{{ session('error') }}");
            @endif

            @if(session('warning'))
                showWarningToast("{{ session('warning') }}");
            @endif

            @if(session('info'))
                showInfoToast("{{ session('info') }}");
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
