<!-- Mobile-Friendly Navigation Bar -->
<nav class="navbar bg-white shadow-lg border-b-2 border-brick-orange">
    <div class="navbar-start">
        <!-- Mobile menu button -->
        <div class="dropdown lg:hidden">
            <label for="mobile-menu" class="btn btn-ghost">
                <i class="fas fa-bars text-xl"></i>
            </label>
            <input type="checkbox" id="mobile-menu" class="hidden">
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a></li>
                        <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle"></i> All Incidents</a></li>
                        <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck"></i> Fleet Management</a></li>
                        <li><a href="{{ route('requests.index') }}"><i class="fas fa-clipboard-list"></i> All Requests</a></li>
                        <li><a href="{{ route('heatmaps') }}"><i class="fas fa-map"></i> Heat Maps</a></li>
                    @elseif(auth()->user()->role === 'staff')
                        <li><a href="{{ route('staff.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Staff Dashboard</a></li>
                        <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle"></i> Incidents</a></li>
                        <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck"></i> Vehicles</a></li>
                        <li><a href="{{ route('requests.index') }}"><i class="fas fa-clipboard-list"></i> Requests</a></li>
                    @elseif(auth()->user()->role === 'responder')
                        <li><a href="{{ route('responder.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Responder Dashboard</a></li>
                        <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle"></i> Active Incidents</a></li>
                        <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck"></i> My Vehicle</a></li>
                    @else
                        <li><a href="{{ route('incidents.create') }}"><i class="fas fa-plus"></i> Report Incident</a></li>
                        <li><a href="{{ route('requests.create') }}"><i class="fas fa-clipboard-list"></i> Submit Request</a></li>
                        <li><a href="{{ route('requests.status-check') }}"><i class="fas fa-search"></i> Check Status</a></li>
                    @endif
                @endauth
            </ul>
        </div>
        
        <!-- Logo and Brand -->
        <a href="{{ auth()->check() ? route('dashboard') : '/' }}" class="btn btn-ghost text-xl">
            <img src="{{ asset('img/logo.png') }}" alt="BukidnonAlert" class="w-8 h-8 mr-2">
            <span class="hidden sm:inline text-brick-orange font-bold">BukidnonAlert</span>
        </a>
    </div>

    <!-- Desktop Navigation -->
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            @auth
                @if(auth()->user()->role === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
                    <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle mr-1"></i> Incidents</a></li>
                    <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck mr-1"></i> Fleet</a></li>
                    <li><a href="{{ route('requests.index') }}"><i class="fas fa-clipboard-list mr-1"></i> Requests</a></li>
                    <li><a href="{{ route('heatmaps') }}"><i class="fas fa-map mr-1"></i> Analytics</a></li>
                @elseif(auth()->user()->role === 'staff')
                    <li><a href="{{ route('staff.dashboard') }}"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
                    <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle mr-1"></i> Incidents</a></li>
                    <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck mr-1"></i> Vehicles</a></li>
                    <li><a href="{{ route('requests.index') }}"><i class="fas fa-clipboard-list mr-1"></i> Requests</a></li>
                @elseif(auth()->user()->role === 'responder')
                    <li><a href="{{ route('responder.dashboard') }}"><i class="fas fa-tachometer-alt mr-1"></i> Dashboard</a></li>
                    <li><a href="{{ route('incidents.index') }}"><i class="fas fa-exclamation-triangle mr-1"></i> Incidents</a></li>
                    <li><a href="{{ route('vehicles.index') }}"><i class="fas fa-truck mr-1"></i> Vehicle</a></li>
                @else
                    <li><a href="{{ route('incidents.create') }}"><i class="fas fa-plus mr-1"></i> Report</a></li>
                    <li><a href="{{ route('requests.create') }}"><i class="fas fa-clipboard-list mr-1"></i> Request</a></li>
                    <li><a href="{{ route('requests.status-check') }}"><i class="fas fa-search mr-1"></i> Status</a></li>
                @endif
            @endauth
        </ul>
    </div>

    <div class="navbar-end">
        @auth
            <!-- Notifications (Mobile & Desktop) -->
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="badge badge-xs badge-primary indicator-item" id="notification-count">3</span>
                    </div>
                </label>
                <div tabindex="0" class="dropdown-content card card-compact w-80 bg-base-100 shadow-xl z-[1]">
                    <div class="card-body">
                        <h3 class="card-title">Notifications</h3>
                        <div class="space-y-2" id="notifications-list">
                            <!-- Notifications will be loaded here -->
                            <div class="text-sm text-gray-500">Loading notifications...</div>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-outline" onclick="markAllAsRead()">Mark All Read</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                    <div class="w-8 rounded-full bg-brick-orange text-white flex items-center justify-center">
                        <span class="text-sm font-semibold">{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                    </div>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li class="px-4 py-2">
                        <div class="text-sm font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }} - {{ auth()->user()->municipality }}</div>
                    </li>
                    <li><hr class="my-1"></li>
                    <li><a href="#" onclick="showProfileModal()"><i class="fas fa-user mr-2"></i>Profile</a></li>
                    <li><a href="#" onclick="showSettingsModal()"><i class="fas fa-cog mr-2"></i>Settings</a></li>
                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('register') }}"><i class="fas fa-user-plus mr-2"></i>Add User</a></li>
                    @endif
                    <li><hr class="my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        @endauth
    </div>
</nav>

<!-- Emergency Quick Actions (Mobile Floating Button) -->
@auth
@if(auth()->user()->role === 'responder')
<div class="fixed bottom-4 right-4 z-50 lg:hidden">
    <div class="dropdown dropdown-top dropdown-end">
        <label tabindex="0" class="btn btn-circle btn-lg bg-red-600 text-white hover:bg-red-700 shadow-lg">
            <i class="fas fa-plus text-xl"></i>
        </label>
        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 mb-2">
            <li><a href="{{ route('incidents.create') }}" class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Report Incident</a></li>
            <li><a href="#" onclick="getCurrentLocation()" class="text-blue-600"><i class="fas fa-map-marker-alt mr-2"></i>Share Location</a></li>
            <li><a href="#" onclick="callEmergency()" class="text-green-600"><i class="fas fa-phone mr-2"></i>Emergency Call</a></li>
        </ul>
    </div>
</div>
@endif
@endauth

<!-- Mobile-specific styles -->
<style>
    .bg-brick-orange { background-color: #c14a09; }
    .text-brick-orange { color: #c14a09; }
    .border-brick-orange { border-color: #c14a09; }
    
    /* Mobile touch targets */
    @media (max-width: 768px) {
        .btn, .menu li > * {
            min-height: 48px; /* Ensure minimum touch target size */
        }
        
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        .dropdown-content {
            max-height: 70vh;
            overflow-y: auto;
        }
    }
</style>

<script>
    // GPS and Location Functions
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Send location to server or use for emergency response
                fetch('/api/responder/location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                });
                
                alert(`Location shared: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    function callEmergency() {
        if (confirm('Call emergency hotline 911?')) {
            window.location.href = 'tel:911';
        }
    }

    // Notification Management
    function loadNotifications() {
        fetch('/api/notifications')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('notifications-list');
                const count = document.getElementById('notification-count');
                
                if (data.length === 0) {
                    list.innerHTML = '<div class="text-sm text-gray-500">No new notifications</div>';
                    count.style.display = 'none';
                } else {
                    list.innerHTML = data.map(notification => 
                        `<div class="alert alert-sm ${notification.type === 'critical' ? 'alert-error' : 'alert-info'}">
                            <span class="text-xs">${notification.message}</span>
                            <time class="text-xs text-gray-500">${notification.created_at}</time>
                        </div>`
                    ).join('');
                    count.textContent = data.length;
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    function markAllAsRead() {
        fetch('/api/notifications/mark-read', { method: 'POST' })
            .then(() => loadNotifications());
    }

    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        @auth
        loadNotifications();
        @endauth
    });

    // Profile and Settings Modals
    function showProfileModal() {
        // Implementation for profile modal
        alert('Profile modal would open here');
    }

    function showSettingsModal() {
        // Implementation for settings modal
        alert('Settings modal would open here');
    }
</script>