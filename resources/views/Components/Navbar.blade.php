<nav class="navbar bg-brick-orange text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <i class="fas fa-shield-alt text-2xl"></i>
            <span class="font-bold text-xl logo-text">MDRRMO Bukidnon</span>
        </div>
        <div class="hidden md:flex space-x-6">
            <a href="#about" class="hover:text-orange-200 transition nav-text">About</a>
            <a href="#features" class="hover:text-orange-200 transition nav-text">Features</a>
            <a href="#how-it-works" class="hover:text-orange-200 transition nav-text">How It Works</a>
            <a href="#benefits" class="hover:text-orange-200 transition nav-text">Benefits</a>
            <a href="#impact" class="hover:text-orange-200 transition nav-text">Impact</a>
        </div>
        <button class="md:hidden text-white focus:outline-none" id="mobile-menu-button">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>
    <div class="md:hidden hidden bg-brick-orange" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="#about" class="block px-3 py-2 hover:bg-orange-700 rounded">About</a>
            <a href="#features" class="block px-3 py-2 hover:bg-orange-700 rounded">Features</a>
            <a href="#how-it-works" class="block px-3 py-2 hover:bg-orange-700 rounded">How It Works</a>
            <a href="#benefits" class="block px-3 py-2 hover:bg-orange-700 rounded">Benefits</a>
            <a href="#impact" class="block px-3 py-2 hover:bg-orange-700 rounded">Impact</a>
        </div>
    </div>
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</nav>
