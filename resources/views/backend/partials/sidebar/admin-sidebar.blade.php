<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link bg-indigo-800">
        <span class="brand-text font-weight-bold">Ramanthali Committee</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <span class="d-block" style="color: white">{{ auth()->check() ? auth()->user()->username : 'Guest' }}</span>
                <h6 id="current-time" class="text-muted"></h6>
            </div>
        </div>

        <nav class="mt-2 sidebar-nav">
            <ul class="nav nav-pills nav-sidebar flex-column d-flex" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-xs text-muted mt-2">SECTIONS</li>

                <li class="nav-item">
                    <a href="{{ route('house-creations.index') }}" class="nav-link {{ request()->routeIs('house-creations.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home text-success"></i>
                        <p>House Creation</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-plus text-info"></i>
                        <p>Member Creation</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('members.list') }}" class="nav-link {{ request()->routeIs('members.list') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users text-primary"></i>
                        <p>Member List</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('member-reports.index') }}" class="nav-link {{ request()->routeIs('member-reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar text-warning"></i>
                        <p>Member Reports</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('member-reports.yearly-payment.create') }}" class="nav-link {{ request()->routeIs('member-reports.yearly-payment.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hand-holding-usd text-primary"></i>
                        <p>Yearly Payment</p>
                    </a>
                </li>

                @if(auth()->user()?->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield text-danger"></i>
                            <p>Manage Users</p>
                        </a>
                    </li>
                @endif

                @php
                    $lookupType = request()->route('type');
                    $lookupMenuOpen = request()->routeIs('admin.lookups.*');
                @endphp
                <li class="nav-item {{ $lookupMenuOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $lookupMenuOpen ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Lookup Masters
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'relations') }}" class="nav-link {{ $lookupType === 'relations' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-people-arrows text-info"></i>
                                <p>Relations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'occupations') }}" class="nav-link {{ $lookupType === 'occupations' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-briefcase text-warning"></i>
                                <p>Occupations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'qualifications') }}" class="nav-link {{ $lookupType === 'qualifications' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-graduate text-success"></i>
                                <p>Qualifications</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'islamic-qualifications') }}" class="nav-link {{ $lookupType === 'islamic-qualifications' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book-reader text-primary"></i>
                                <p>Islamic Qualifications</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'job-locations') }}" class="nav-link {{ $lookupType === 'job-locations' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-map-marker-alt text-danger"></i>
                                <p>Job Locations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'places') }}" class="nav-link {{ $lookupType === 'places' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-mosque text-success"></i>
                                <p>Mahallus</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.lookups.index', 'house-types') }}" class="nav-link {{ $lookupType === 'house-types' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-home text-info"></i>
                                <p>House Types</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-white bg-danger d-flex align-items-center" style="border: none; font-weight: bold; width: 100%;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p class="ml-2 mb-0">Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .brand-link {
        transition: all 0.3s ease;
    }
    .brand-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    .nav-header {
        font-size: 0.7rem;
        padding: 0.5rem 1rem;
        letter-spacing: 0.5px;
    }
    .nav-item .nav-link.active {
        font-weight: 600;
    }
    .nav-link p {
        margin: 0 0 0 8px;
        display: inline;
    }
    .main-sidebar {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .sidebar {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .sidebar-nav {
        display: flex;
        flex: 1;
        flex-direction: column;
        min-height: 0;
    }
    .nav-sidebar {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .nav-item.mt-auto {
        margin-top: auto !important;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
</style>

<script>
    function updateTime() {
        const now = new Date();
        const time = now.toLocaleTimeString();
        document.getElementById('current-time').textContent = time;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
