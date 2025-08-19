@php
    $allowedRoles = [1,2,3];
    if (!auth()->check()) {
        header('Location: ' . route('login'));
        exit;
    }
    $canRegister = in_array(auth()->user()->role_id, $allowedRoles);
    $isLab = auth()->user()->isLab();
    
    // Open when we're on any inventory, donors-with-results, or blood-bags page
    $invOpen = request()->routeIs('inventory.*')
            || request()->routeIs('donors.lab-results*')
            || request()->routeIs('blood-bags.*');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BloodBank') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script src="//unpkg.com/alpinejs" defer></script>

        <style>
            .sidebar-link { display: flex; align-items: center; padding: 0.75rem 1rem; color: #4B5563; border-radius: 0.5rem; transition: all 0.3s ease; }
            .sidebar-link:hover { background-color: #F3F4F6; }
            .sidebar-link.active { background-color: #EFF6FF; color: #3B82F6; }
            .sidebar-link i { margin-right: 0.75rem; font-size: 1.25rem; }
            .notification-badge { background-color: #EF4444; color: white; border-radius: 9999px; padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-left: auto; }
            .main-content { margin-left: 16rem; margin-top: 4rem; }
            .top-bar { position: fixed; top: 0; right: 0; left: 16rem; height: 4rem; z-index: 20; background-color: white; }
            .sidebar { position: fixed; top: 0; left: 0; width: 16rem; height: 100vh; background-color: white; border-right: 1px solid #E5E7EB; z-index: 30; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center h-16 px-6 border-b border-gray-200">
                    <h1 class="text-xl font-semibold text-gray-800">BloodBank</h1>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->user()->role && auth()->user()->role->name !== 'super_admin')
                        <!-- Donors -->
                        <div x-data="{ open: {{ request()->routeIs('donors.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="sidebar-link w-full justify-between {{ request()->routeIs('donors.*') ? 'active' : '' }}">
                                <span class="flex items-center"><i class="ri-heart-line"></i><span>Donors</span></span>
                                <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" class="pl-10 mt-1 space-y-1" x-cloak>
                                @if(!$isLab)
                                    <a href="{{ route('donors.create') }}" class="sidebar-link {{ request()->routeIs('donors.create') ? 'active' : '' }}">Register Donor</a>
                                @endif
                                <a href="{{ route('donors.index') }}" class="sidebar-link {{ request()->routeIs('donors.index') ? 'active' : '' }}">Registered Donors</a>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div x-data="{ open: @json($invOpen) }" class="mb-1">
                            <button @click="open = !open"
                                    class="sidebar-link w-full justify-between"
                                    :class="{ 'active': open }">
                                <span class="flex items-center">
                                    <i class="ri-stock-line"></i>
                                    <span>Inventory</span>
                                </span>
                                <svg :class="{ 'rotate-90': open }" class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <div x-show="open" class="pl-10 mt-1 space-y-1" x-cloak>
                                <a href="{{ route('donors.lab-results.index') }}"
                                   class="sidebar-link {{ request()->routeIs('donors.lab-results*') ? 'active' : '' }}">
                                    Donors (Lab Results)
                                </a>

                                <a href="{{ route('blood-bags.index') }}"
                                   class="sidebar-link {{ request()->routeIs('blood-bags.*') ? 'active' : '' }}">
                                    🧊 Blood Inventory
                                </a>
                            </div>
                        </div>

                        <!-- Other Links -->
                        @if(!$isLab)
                            <a href="{{ route('patients.index') }}"
                               class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                                <i class="ri-user-heart-line"></i><span>Patients</span>
                            </a>

                            <a href="{{ route('sms-campaigns.index') }}"
                               class="sidebar-link {{ request()->routeIs('sms-campaigns.*') ? 'active' : '' }}">
                                <i class="ri-message-2-line"></i><span>SMS Campaign</span>
                            </a>

                            <a href="{{ route('requests.index') }}"
                               class="sidebar-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
                                <i class="ri-drop-line"></i><span>Blood Requests</span>
                            </a>

                            @if(auth()->user()->role && auth()->user()->role->name === 'super_admin')
                                <a href="{{ route('super-admin.hospitals') }}"
                                   class="sidebar-link {{ request()->routeIs('super-admin.hospitals*') ? 'active' : '' }}">
                                    <i class="ri-hospital-line"></i><span>All Hospitals</span>
                                </a>
                            @else
                                <a href="{{ route('hospitals.index') }}"
                                   class="sidebar-link {{ request()->routeIs('hospitals.*') ? 'active' : '' }}">
                                    <i class="ri-hospital-line"></i><span>My Hospital</span>
                                </a>
                            @endif

                            <a href="{{ route('departments.index') }}"
                               class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                <i class="ri-building-2-line"></i><span>Departments</span>
                            </a>
                        @endif
                    @endif

                    <a href="{{ route('reports.index') }}"
                       class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="ri-file-chart-line"></i><span>Reports</span>
                    </a>

                    <!-- Super Admin Section -->
                    @if(auth()->user()->role && auth()->user()->role->name === 'super_admin')
                        <div class="border-t border-gray-200 my-4"></div>

                        <div x-data="{ open: {{ request()->routeIs('super-admin.hospitals.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="sidebar-link w-full justify-between {{ request()->routeIs('super-admin.hospitals.*') ? 'active' : '' }}">
                                <span class="flex items-center"><i class="ri-hospital-line"></i><span>Hospital Management</span></span>
                                <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" class="pl-10 mt-1 space-y-1" x-cloak>
                                <a href="{{ route('super-admin.hospitals') }}" class="sidebar-link {{ request()->routeIs('super-admin.hospitals') ? 'active' : '' }}">All Hospitals</a>
                                <a href="{{ route('super-admin.hospitals.create') }}" class="sidebar-link {{ request()->routeIs('super-admin.hospitals.create') ? 'active' : '' }}">Add Hospital</a>
                            </div>
                        </div>
                    @endif

                    <!-- Configuration -->
                    @if(!$isLab)
                        <div x-data="{ open: @json(
                            request()->routeIs('roles.*') 
                            || request()->routeIs('users.*') 
                            || request()->routeIs('settings.*')
                        ) }" class="mb-1">
                            <button
                                @click="open = !open"
                                class="sidebar-link w-full justify-between"
                                :class="{ 'active': open }"
                            >
                                <span class="flex items-center">
                                    <i class="ri-settings-2-line"></i>
                                    <span>Configuration</span>
                                </span>
                                <svg
                                    :class="{ 'rotate-90': open }"
                                    class="w-4 h-4 ml-2 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <div x-show="open" class="pl-10 mt-1 space-y-1" x-cloak>
                                <a href="{{ route('roles.index') }}"
                                   class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    Roles
                                </a>
                                <a href="{{ route('users.index') }}"
                                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    Users
                                </a>
                                <a href="{{ route('settings.index') }}"
                                   class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    Settings
                                </a>
                            </div>
                        </div>
                    @endif

                    <a href="#"
                    class="sidebar-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                    <i class="ri-notification-3-line"></i><span>Alerts</span>
                    <span class="notification-badge">2</span>
                    </a>
                </nav>

                <!-- User Profile -->
                <div class="border-t border-gray-200 p-4">
                    @if(auth()->check())
                    <div class="flex items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3B82F6&color=fff" 
                             alt="{{ Auth::user()->name }}" 
                             class="w-10 h-10 rounded-full">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link mt-4">
                            <i class="ri-logout-box-line"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </aside>

        <!-- Top Navigation -->
        <header class="top-bar border-b border-gray-200">
            <div class="flex items-center justify-between h-full px-6">
                <div class="flex-1 max-w-xs">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="ri-search-line text-gray-400"></i>
                        </div>
                        <input type="text" class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Search">
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="px-6 py-6">
                @yield('content')
            </div>
        </main>
        @stack('scripts')
    </body>
</html>
