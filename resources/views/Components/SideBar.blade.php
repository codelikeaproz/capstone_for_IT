<!-- Sidebar -->
<div class="sidebar bg-brick-orange text-white w-64 flex flex-col h-full transition-all duration-300 ease-in-out">
    <!-- Logo -->
    <div class="p-4 flex items-center border-b border-orange-700">
        <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center">
            <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-12 h-10 inline-block" loading="lazy">
        </div>
        <span class="logo-text ml-3 text-xl font-bold transition-opacity duration-300">MDRRMO System</span>
    </div>

    <!-- User Info -->
    @auth
    <div class="p-4 border-b border-orange-700">
        <div class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="fas fa-user text-brick-orange"></i>
            </div>
            <div class="nav-text ml-3 transition-opacity duration-300">
                <p class="text-sm font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                <p class="text-xs text-orange-300 capitalize">{{ auth()->user()->role }}</p>
                @if(auth()->user()->municipality)
                    <p class="text-xs text-orange-200">{{ auth()->user()->municipality }}</p>
                @endif
            </div>
        </div>
    </div>
    @endauth

    <!-- Menu Toggle -->
    <div class="menu-toggle p-4 flex items-center justify-between border-b border-orange-700 cursor-pointer hover:bg-orange-700 transition-colors duration-200">
        <div class="flex items-center">
            <i class="fas fa-bars text-lg"></i>
            <span class="nav-text ml-3 transition-opacity duration-300">Collapse Menu</span>
        </div>
        <i class="fas fa-chevron-left nav-text transition-transform duration-300" id="collapse-arrow"></i>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto">
        <div class="p-4">
            <p class="text-orange-300 uppercase text-xs font-bold nav-text transition-opacity duration-300">Main</p>
        </div>
        <ul>
            <!-- Dashboard - Role-based routing -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-home"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">Admin Dashboard</span>
                        </li>
                    </a>
                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-home"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">Staff Dashboard</span>
                        </li>
                    </a>
                @elseif(auth()->user()->role === 'responder')
                    <a href="{{ route('responder.dashboard') }}" class="{{ request()->routeIs('responder.dashboard') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-home"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">Responder Dashboard</span>
                        </li>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-home"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">Dashboard</span>
                        </li>
                    </a>
                @endif
            @else
                <a href="/" class="{{ request()->is('/') ? 'bg-orange-700' : '' }}">
                    <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                        <i class="fas fa-home"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Home</span>
                    </li>
                </a>
            @endauth

            <!-- Analytics -->
            <a href="{{ route('analytics.dashboard') }}" class="{{ request()->routeIs('analytics.dashboard') ? 'bg-orange-700' : '' }}">
                <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text ml-3 transition-opacity duration-300">Analytics</span>
                </li>
            </a>

            <!-- Users Management - Admin Only -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center cursor-pointer"
                        id="users-dropdown">
                        <i class="fas fa-users"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Users</span>
                        <i class="fas fa-chevron-down nav-text ml-auto text-xs transition-transform duration-200"></i>
                    </li>
                    <div class="users-submenu ml-6 mr-2 mt-1 mb-1">
                        <ul class="space-y-1">
                            <li class="px-4 py-2 hover:bg-orange-700 rounded-md flex items-center text-sm cursor-pointer">
                                <i class="fas fa-user-plus"></i>
                                <span class="ml-3">Add Staff</span>
                            </li>
                            <li class="px-4 py-2 hover:bg-orange-700 rounded-md flex items-center text-sm cursor-pointer">
                                <i class="fas fa-user-edit"></i>
                                <span class="ml-3">Edit User</span>
                            </li>
                            <li class="px-4 py-2 hover:bg-orange-700 rounded-md flex items-center text-sm cursor-pointer">
                                <i class="fas fa-user-cog"></i>
                                <span class="ml-3">User Roles</span>
                            </li>
                            <li class="px-4 py-2 hover:bg-orange-700 rounded-md flex items-center text-sm cursor-pointer">
                                <i class="fas fa-user-shield"></i>
                                <span class="ml-3">Permissions</span>
                            </li>
                        </ul>
                    </div>
                @endif
            @endauth

            <!-- Incidents -->
            <a href="{{ route('incidents.index') }}" class="{{ request()->routeIs('incidents.*') ? 'bg-orange-700' : '' }}">
                <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span class="nav-text ml-3 transition-opacity duration-300">Incidents</span>
                    @auth
                        @php
                            $pendingIncidents = \App\Models\Incident::where('status', 'pending')
                                ->when(auth()->user()->role !== 'admin', function($query) {
                                    return $query->where('municipality', auth()->user()->municipality);
                                })
                                ->count();
                        @endphp
                        @if($pendingIncidents > 0)
                            <span class="nav-text ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 transition-opacity duration-300">{{ $pendingIncidents }}</span>
                        @endif
                    @endauth
                </li>
            </a>

            <!-- Vehicles - Staff and Admin only -->
            @auth
                @if(in_array(auth()->user()->role, ['admin', 'staff', 'responder']))
                    <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-truck"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">Vehicles</span>
                        </li>
                    </a>
                @endif
            @endauth

            <!-- Requests -->
            <a href="{{ route('requests.index') }}" class="{{ request()->routeIs('requests.*') ? 'bg-orange-700' : '' }}">
                <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-text ml-3 transition-opacity duration-300">Requests</span>
                    @auth
                        @php
                            $pendingRequests = \App\Models\Request::where('status', 'pending')
                                ->when(auth()->user()->role !== 'admin', function($query) {
                                    return $query->where('municipality', auth()->user()->municipality);
                                })
                                ->count();
                        @endphp
                        @if($pendingRequests > 0)
                            <span class="nav-text ml-auto bg-yellow-500 text-white text-xs rounded-full px-2 py-1 transition-opacity duration-300">{{ $pendingRequests }}</span>
                        @endif
                    @endauth
                </li>
            </a>

            <!-- Heat Maps -->
            <a href="{{ route('heatmaps') }}" class="{{ request()->routeIs('heatmaps') ? 'bg-orange-700' : '' }}">
                <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="nav-text ml-3 transition-opacity duration-300">Heat Maps</span>
                </li>
            </a>

            <!-- Municipalities - Admin only -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center cursor-pointer">
                        <i class="fas fa-map"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Municipalities</span>
                        <span class="nav-text ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1 transition-opacity duration-300">
                            {{ \App\Models\User::distinct('municipality')->count('municipality') }}
                        </span>
                    </li>
                @endif
            @endauth

            <!-- System Logs - Admin only -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('system.logs') }}" class="{{ request()->routeIs('system.logs') ? 'bg-orange-700' : '' }}">
                        <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                            <i class="fas fa-history"></i>
                            <span class="nav-text ml-3 transition-opacity duration-300">System Logs</span>
                            @php
                                $recentLogs = \Illuminate\Support\Facades\DB::table('activity_log')
                                    ->where('created_at', '>=', now()->subHours(24))
                                    ->count();
                            @endphp
                            @if($recentLogs > 0)
                                <span class="nav-text ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-1 transition-opacity duration-300">{{ $recentLogs }}</span>
                            @endif
                        </li>
                    </a>
                @endif
            @endauth

            <!-- Reports -->
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'bg-orange-700' : '' }}">
                <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text ml-3 transition-opacity duration-300">Reports</span>
                </li>
            </a>
        </ul>

        <div class="p-4 mt-4">
            <p class="text-orange-300 uppercase text-xs font-bold nav-text transition-opacity duration-300">Settings</p>
        </div>
        <ul>
            <!-- Settings - Admin and Staff only -->
            @auth
                @if(in_array(auth()->user()->role, ['admin', 'staff']))
                    <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center cursor-pointer">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Settings</span>
                    </li>
                @endif
            @endauth

            <!-- Help -->
            <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center cursor-pointer">
                <i class="fas fa-question-circle"></i>
                <span class="nav-text ml-3 transition-opacity duration-300">Help</span>
            </li>

            <!-- Logout -->
            @auth
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 hover:bg-red-600 rounded-md mx-2 flex items-center text-left transition-colors duration-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'bg-orange-700' : '' }}">
                    <li class="px-4 py-3 hover:bg-orange-700 rounded-md mx-2 flex items-center">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="nav-text ml-3 transition-opacity duration-300">Login</span>
                    </li>
                </a>
            @endauth
        </ul>
    </nav>

    <!-- System Status Footer -->
    @auth
        <div class="p-4 border-t border-orange-700">
            <div class="nav-text transition-opacity duration-300">
                <div class="flex items-center justify-between text-xs text-orange-200">
                    <span>System Status</span>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                        <span>Online</span>
                    </div>
                </div>
                <div class="text-xs text-orange-300 mt-1">
                    Last updated: {{ now()->format('M d, H:i') }}
                </div>
            </div>
        </div>
    @endauth
</div>

<script>
    // Enhanced Sidebar toggle functionality with comprehensive error handling and debugging
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 Sidebar initialization started...');
        
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const usersDropdown = document.getElementById('users-dropdown');
        
        // Find content area more reliably with multiple fallback strategies
        const content = document.querySelector('.content-wrapper') || 
                       document.querySelector('.content') || 
                       document.querySelector('main') || 
                       document.querySelector('[class*="content"]') ||
                       document.querySelector('body > div > div:last-child') ||
                       document.querySelector('.container') ||
                       document.querySelector('#app');

        console.log('🔍 Sidebar Debug Info:', {
            menuToggle: !!menuToggle,
            sidebar: !!sidebar,
            content: !!content,
            sidebarClasses: sidebar?.className,
            contentClasses: content?.className,
            fontAwesome: !!window.FontAwesome || document.querySelector('link[href*="font-awesome"]'),
            viewportWidth: window.innerWidth
        });

        // Force FontAwesome icon verification
        function verifyFontAwesome() {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-home';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const computed = window.getComputedStyle(testIcon);
            const fontFamily = computed.getPropertyValue('font-family');
            document.body.removeChild(testIcon);
            
            console.log('📝 FontAwesome Status:', {
                fontFamily: fontFamily,
                isLoaded: fontFamily.includes('Font Awesome') || fontFamily.includes('FontAwesome')
            });
            
            return fontFamily.includes('Font Awesome') || fontFamily.includes('FontAwesome');
        }

        // Sidebar collapse/expand functionality with enhanced debugging
        if (menuToggle && sidebar) {
            console.log('✅ Sidebar elements found, attaching event listeners...');
            
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const currentlyCollapsed = sidebar.classList.contains('collapsed');
                console.log('🔄 Toggle clicked:', {
                    currentState: currentlyCollapsed ? 'collapsed' : 'expanded',
                    willBecome: currentlyCollapsed ? 'expanded' : 'collapsed'
                });
                
                // Toggle the collapsed class
                sidebar.classList.toggle('collapsed');
                const newState = sidebar.classList.contains('collapsed');
                
                // Visual feedback with arrow rotation
                const arrow = document.getElementById('collapse-arrow');
                if (arrow) {
                    if (newState) {
                        arrow.classList.add('rotate-180');
                    } else {
                        arrow.classList.remove('rotate-180');
                    }
                }
                
                // Apply styles with forced updates and animations
                if (newState) {
                    // Collapsing
                    sidebar.style.width = '80px';
                    sidebar.style.minWidth = '80px';
                    sidebar.style.maxWidth = '80px';
                    
                    // Hide nav text elements with transition
                    document.querySelectorAll('.nav-text, .logo-text').forEach(el => {
                        el.style.opacity = '0';
                        setTimeout(() => {
                            el.style.display = 'none';
                        }, 150);
                    });
                    
                    // Update menu toggle text
                    const toggleText = menuToggle.querySelector('.nav-text');
                    if (toggleText) {
                        toggleText.textContent = 'Expand Menu';
                    }
                } else {
                    // Expanding
                    sidebar.style.width = '256px';
                    sidebar.style.minWidth = '256px';
                    sidebar.style.maxWidth = '256px';
                    
                    // Show nav text elements with transition
                    document.querySelectorAll('.nav-text, .logo-text').forEach(el => {
                        el.style.display = '';
                        setTimeout(() => {
                            el.style.opacity = '1';
                        }, 50);
                    });
                    
                    // Update menu toggle text
                    const toggleText = menuToggle.querySelector('.nav-text');
                    if (toggleText) {
                        toggleText.textContent = 'Collapse Menu';
                    }
                }

                // No margin adjustments needed - flexbox handles layout automatically
                // The content-wrapper will automatically expand/contract with sidebar changes
                console.log('🎯 Flexbox layout - no manual margin adjustments needed');

                // Store sidebar state persistently
                localStorage.setItem('sidebarCollapsed', newState);
                
                console.log('✅ Toggle completed:', {
                    finalState: newState ? 'collapsed' : 'expanded',
                    sidebarWidth: sidebar.style.width,
                    usingFlexbox: 'Content auto-adjusts via flexbox'
                });
                
                // Visual feedback for successful toggle
                menuToggle.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
                setTimeout(() => {
                    menuToggle.style.backgroundColor = '';
                }, 200);
            });

            // Restore sidebar state from localStorage with validation
            try {
                const savedState = localStorage.getItem('sidebarCollapsed');
                console.log('💾 Restoring saved state:', savedState);
                
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    sidebar.style.width = '80px';
                    sidebar.style.minWidth = '80px';
                    sidebar.style.maxWidth = '80px';
                    
                    document.querySelectorAll('.nav-text, .logo-text').forEach(el => {
                        el.style.display = 'none';
                        el.style.opacity = '0';
                    });
                    
                    // No margin adjustments needed with flexbox layout
                    
                    const arrow = document.getElementById('collapse-arrow');
                    if (arrow) arrow.classList.add('rotate-180');
                    
                    const toggleText = menuToggle.querySelector('.nav-text');
                    if (toggleText) toggleText.textContent = 'Expand Menu';
                }
            } catch (error) {
                console.warn('⚠️ Error restoring sidebar state:', error);
                localStorage.removeItem('sidebarCollapsed');
            }
        } else {
            console.error('❌ Sidebar elements not found:', { 
                menuToggle: !!menuToggle, 
                sidebar: !!sidebar 
            });
            
            // Emergency fallback: try to find elements after a delay
            setTimeout(() => {
                const delayedMenuToggle = document.querySelector('.menu-toggle');
                const delayedSidebar = document.querySelector('.sidebar');
                
                if (delayedMenuToggle && delayedSidebar) {
                    console.log('🔄 Delayed sidebar elements found, retrying initialization...');
                    location.reload(); // Reload to reinitialize properly
                }
            }, 1000);
        }

        // Users dropdown functionality
        if (usersDropdown) {
            usersDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = document.querySelector('.users-submenu');
                const chevron = this.querySelector('.fa-chevron-down');
                if (submenu && chevron) {
                    submenu.classList.toggle('show');
                    chevron.classList.toggle('rotate-180');
                }
            });
        }

        // Active link highlighting
        const currentPath = window.location.pathname;
        const sidebarLinks = document.querySelectorAll('.sidebar a');

        sidebarLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                const listItem = link.querySelector('li');
                if (listItem) {
                    listItem.classList.add('bg-orange-700');
                }
            }
        });

        // Auto-refresh counters every 30 seconds (only on dashboard pages)
        if (window.location.pathname.includes('dashboard')) {
            setInterval(function() {
                const counters = document.querySelectorAll('.sidebar .bg-red-500, .sidebar .bg-yellow-500');
                if (counters.length > 0) {
                    fetch('/api/dashboard/notifications')
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            // Update pending incidents counter
                            const incidentCounter = document.querySelector('.sidebar .bg-red-500');
                            if (incidentCounter && data.pending_incidents !== undefined) {
                                if (data.pending_incidents > 0) {
                                    incidentCounter.textContent = data.pending_incidents;
                                    incidentCounter.style.display = 'inline-block';
                                } else {
                                    incidentCounter.style.display = 'none';
                                }
                            }

                            // Update pending requests counter
                            const requestCounter = document.querySelector('.sidebar .bg-yellow-500');
                            if (requestCounter && data.pending_requests !== undefined) {
                                if (data.pending_requests > 0) {
                                    requestCounter.textContent = data.pending_requests;
                                    requestCounter.style.display = 'inline-block';
                                } else {
                                    requestCounter.style.display = 'none';
                                }
                            }

                            // Update system logs counter
                            const logsCounter = document.querySelector('.sidebar .bg-purple-500');
                            if (logsCounter && data.recent_logs !== undefined) {
                                if (data.recent_logs > 0) {
                                    logsCounter.textContent = data.recent_logs;
                                    logsCounter.style.display = 'inline-block';
                                } else {
                                    logsCounter.style.display = 'none';
                                }
                            }
                        })
                        .catch(error => {
                            console.log('Counter refresh failed:', error);
                        });
                }
            }, 30000); // Refresh every 30 seconds
        }

        // Tooltip functionality for collapsed sidebar
        function initTooltips() {
            const sidebarItems = document.querySelectorAll('.sidebar li');

            sidebarItems.forEach(item => {
                const text = item.querySelector('span')?.textContent;
                if (text) {
                    item.setAttribute('title', text);
                }
            });
        }

        initTooltips();

        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth < 768) {
                if (sidebar) {
                    sidebar.classList.add('collapsed');
                    sidebar.style.width = '80px';
                    document.querySelectorAll('.nav-text, .logo-text').forEach(el => {
                        el.style.display = 'none';
                    });
                    if (content) {
                        content.style.marginLeft = '80px';
                    }
                }
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initial check
        
        // Emergency reset function for debugging (can be called from console)
        window.resetSidebar = function() {
            if (sidebar) {
                sidebar.classList.remove('collapsed');
                sidebar.style.width = '256px';
                document.querySelectorAll('.nav-text, .logo-text').forEach(el => {
                    el.style.display = '';
                });
                // No margin reset needed - flexbox handles it
                localStorage.removeItem('sidebarCollapsed');
                console.log('Sidebar reset to expanded state - flexbox layout');
            }
        };
        
        // Add keyboard shortcut for toggle (Ctrl + B)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                if (menuToggle) {
                    menuToggle.click();
                }
            }
        });
    });
</script>
</script>
