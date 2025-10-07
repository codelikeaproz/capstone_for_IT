<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDRRMO Bukidnon - Accident Reporting & Vehicle Utilization System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('styles/landing_page.css') }}">
    <style>
        .hero-overlay {
            background-color: rgba(0, 0, 0, 0.6);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .how-it-works-step {
            position: relative;
        }
        .how-it-works-step:not(:last-child):after {
            content: "";
            position: absolute;
            top: 50%;
            right: -40px;
            width: 30px;
            height: 2px;
            background: #3b82f6;
        }
        @media (max-width: 768px) {
            .how-it-works-step:not(:last-child):after {
                display: none;
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800">
    <!-- Navigation -->
    <nav class="bg-orange-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-10 h-10 logo-img">
                <span class="font-bold text-xl">MDRRMO Bukidnon</span>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="#about" class="hover:text-white-200 transition">About</a>
                <a href="#features" class="hover:text-blue-200 transition">Features</a>
                <a href="#how-it-works" class="hover:text-blue-200 transition">How It Works</a>
                <a href="#benefits" class="hover:text-blue-200 transition">Benefits</a>
                <a href="#impact" class="hover:text-blue-200 transition">Impact</a>
            </div>
            <button class="md:hidden text-white focus:outline-none" id="mobile-menu-button">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <!-- Mobile menu -->
        <div class="md:hidden hidden bg-orange-600" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#about" class="block px-3 py-2 hover:bg-orange-700 rounded">About</a>
                <a href="#features" class="block px-3 py-2 hover:bg-orange-700 rounded">Features</a>
                <a href="#how-it-works" class="block px-3 py-2 hover:bg-orange-700 rounded">How It Works</a>
                <a href="#benefits" class="block px-3 py-2 hover:bg-orange-700 rounded">Benefits</a>
                <a href="#impact" class="block px-3 py-2 hover:bg-orange-700 rounded">Impact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white hero-section">
        <div class="absolute inset-0 hero-overlay blur-xs">
            <img src="{{ asset('img/hero_img.jpg') }}"
                 alt="Emergency responders in action" class="w-full h-full object-cover" loading="lazy">
        </div>
        <!-- Gradient Overlay -->
        <div class="hero-section_gradient"></div>
        <div class="relative container mx-auto px-4 py-24 md:py-32 z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-7xl font-bold mb-4"> Modernizing <span class="text-red-600"> Emergency </span>Response in Bukidnon</h1>
                <p class="text-xl md:text-2lg mb-8 mt-7">A secure and centralized platform for faster accident reporting, real-time vehicle tracking, and smarter decision-making for MDRRMO operations.</p>
                <a href="{{ route('login') }}" class= "text-white border-1 border-white btn btn-outline btn-lg btn-primary">
                    Login Now
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-30 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">From Manual Logs to <span class="text-orange-600">Digital</span> Efficiency</h2>
                <div class="w-50 h-1 bg-orange-600 mx-auto"></div>
            </div>
            <div class="max-w-4xl mx-auto">
                <p class="text-lg text-gray-700 mb-6">
                    For years, MDRRMO offices in Maramag, Valencia, and Quezon relied on paper forms, spreadsheets, and fragmented records, causing delays and errors during critical incidents.
                </p>
                <p class="text-lg text-gray-700">
                    Our <span class="text-orange-600">Web-Based Accident Reporting & Vehicle Utilization System</span> replaces these outdated processes with a centralized, real-time, and secure platform.
                     It integrates incident reporting, vehicle tracking, and data analytics all in one place to improve coordination and response times.
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4"> <span class="text-orange-600">Key</span> Features</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Designed to streamline emergency response operations</p>
                <div class="w-50 h-1 bg-orange-600 mx-auto"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-file-alt text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Instant Incident Reporting</h3>
                    <p class="text-gray-600">Record and update accident reports from any device with internet access.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-ambulance text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Vehicle Deployment Tracking</h3>
                    <p class="text-gray-600">Monitor vehicle assignments, fuel usage, and personnel in real-time.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-chart-line text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Analytics & Trends</h3>
                    <p class="text-gray-600">View accident patterns to support better planning and resource allocation.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-file-pdf text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">One-Click PDF Reports</h3>
                    <p class="text-gray-600">Generate official, printable reports instantly for documentation.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-lock text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Role-Based Access Control</h3>
                    <p class="text-gray-600">Secure login with different permission levels for Admins and Staff.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-6 rounded-lg shadow-md feature-card transition duration-300 border border-gray-100">
                    <div class="text-orange-600 mb-4">
                        <i class="fas fa-map-marked-alt text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Geospatial Visualization</h3>
                    <p class="text-gray-600">View incidents on an interactive map for better situational awareness.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">How It <span class="text-orange-600">Works</span></h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Simple workflow for maximum efficiency</p>
                <div class="w-50 h-1 bg-orange-600 mx-auto"></div>
            </div>

            <div class="flex flex-col md:flex-row justify-center items-center md:items-start space-y-8 md:space-y-0 md:space-x-8 lg:space-x-16">
                <!-- Step 1 -->
                <div class="how-it-works-step text-center max-w-xs">
                    <div class="bg-orange-100 text-orange-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Report</h3>
                    <p class="text-gray-600">Staff enter accident details in real-time through the web interface.</p>
                </div>

                <!-- Step 2 -->
                <div class="how-it-works-step text-center max-w-xs">
                    <div class="bg-orange-100 text-orange-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Track</h3>
                    <p class="text-gray-600">Admins oversee vehicle use and responder status through the dashboard.</p>
                </div>

                <!-- Step 3 -->
                <div class="how-it-works-step text-center max-w-xs">
                    <div class="bg-orange-100 text-orange-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Respond</h3>
                    <p class="text-gray-600">Coordinated actions improve response time and accuracy.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-50 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-orange-600 mb-4">Benefits</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Positive impacts across all stakeholders</p>
                <div class="w-50 h-1 bg-orange-600 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- For MDRRMO -->
                <div class="bg-orange-50 p-6 rounded-lg border border-orange-100">
                    <h3 class="text-xl font-bold mb-4 text-orange-800 flex items-center">
                        <i class="fas fa-building mr-2"></i> For MDRRMO
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Faster, more accurate reporting</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Centralized and secure data storage</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Improved coordination between offices</span>
                        </li>
                    </ul>
                </div>

                <!-- For LGUs -->
                <div class="bg-orange-50 p-6 rounded-lg border border-orange-100">
                    <h3 class="text-xl font-bold mb-4 text-orange-800 flex items-center">
                        <i class="fas fa-landmark mr-2"></i> For LGUs
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Reliable reports for policy-making and planning</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Data insights for resource optimization</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Transparent operations and accountability</span>
                        </li>
                    </ul>
                </div>

                <!-- For Communities -->
                <div class="bg-orange-50 p-6 rounded-lg border border-orange-100">
                    <h3 class="text-xl font-bold mb-4 text-orange-800 flex items-center">
                        <i class="fas fa-users mr-2"></i> For Communities
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Quicker emergency assistance</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Improved service delivery</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                            <span>Increased public safety awareness</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Impact Section -->
    <section id="impact" class="py-30 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4"> <span class="text-orange-600">Global</span> Impact</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Supporting the United Nations Sustainable Development Goals (SDGs)</p>
                <div class="w-50 h-1 bg-orange-600 mx-auto"></div>
            </div>

            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- SDG 11 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="bg-green-100 text-green-800 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="font-bold">11</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-center text-gray-800">Sustainable Cities & Communities</h3>
                    <p class="text-gray-600 text-center">Improved preparedness and safety for Bukidnon communities.</p>
                </div>

                <!-- SDG 9 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="bg-green-100 text-green-800 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="font-bold">9</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-center text-gray-800">Industry, Innovation & Infrastructure</h3>
                    <p class="text-gray-600 text-center">Modernizing public service systems through technology.</p>
                </div>

                <!-- SDG 16 -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="bg-green-100 text-green-800 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="font-bold">16</span>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-center text-gray-800">Peace, Justice & Strong Institutions</h3>
                    <p class="text-gray-600 text-center">Promoting transparency and accountability in emergency services.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->


    <!-- Footer -->
    <footer class="bg-orange-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Contact MDRRMO Offices</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-orange-400"></i>
                            <span>Maramag MDRRMO Office, Bukidnon</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-orange-400"></i>
                            <span>Valencia MDRRMO Office, Bukidnon</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-orange-400"></i>
                            <span>Quezon MDRRMO Office, Bukidnon</span>
                        </li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#about" class="hover:text-orange-400 transition">About the System</a></li>
                        <li><a href="#features" class="hover:text-orange-400 transition">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-orange-400 transition">How It Works</a></li>
                        <li><a href="#benefits" class="hover:text-orange-400 transition">Benefits</a></li>
                        <li><a href="#impact" class="hover:text-orange-400 transition">Global Impact</a></li>
                    </ul>
                </div>

                <!-- Project Credits -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Project Credits</h3>
                    <p class="mb-2">Developed in partnership with:</p>
                    <p class="text-white">Central Mindanao University</p>
                    <p class="mt-4 text-sm text-white">© 2023 MDRRMO Bukidnon. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });

                    // Close mobile menu if open
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</body>
</html>
