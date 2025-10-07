<!DOCTYPE html>
<html lang="en" data-theme="corporate">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body >
    {{-- header section --}}
    <div class="content flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-search text-gray-500 mr-2"></i>
                <input type="text" placeholder="Search..." class="border-0 focus:outline-none focus:ring-0">
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <i class="fas fa-bell text-gray-500 text-xl cursor-pointer"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </div>
                <div class="dropdown relative">
                    <div class="flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-user text-brick-orange"></i>
                        </div>
                        <span class="ml-4 text-gray-700">Staff</span>
                        <i class="fas fa-chevron-down ml-1 text-gray-500 text-xs"></i>
                    </div>
                </div>
            </div>
        </header>
    </div>
    {{-- end of the header section --}}
</body>

</html>
