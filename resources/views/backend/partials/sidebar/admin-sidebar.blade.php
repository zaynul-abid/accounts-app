<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link bg-indigo-800">
        <img src="{{ asset('backend/assets/dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">Smart Pay</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</a>
                <a href="#" class="d-block">{{ auth()->user()->company?->name ?? 'NULL' }}</a>
                <small class="text-muted">{{ auth()->check() ? ucfirst(auth()->user()->usertype) : '' }}</small><br>
                <h6 id="current-time" class="text-muted"></h6>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column d-flex" style="height: 100%;" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-xs text-muted mt-2">TRANSACTIONS</li>
                <li class="nav-item">
                    <a href="{{ route('incomes.index') }}" class="nav-link {{ request()->routeIs('incomes.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-arrow-circle-down text-success"></i>
                        <p>Income Entry</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-arrow-circle-up text-danger"></i>
                        <p>Expense Entry</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('suppliers.details') }}" class="nav-link {{ request()->routeIs('suppliers.details') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list text-warning"></i>
                        <p>Supplier Payment</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-xs text-muted mt-2">REPORTS</li>
                <li class="nav-item">
                    <a href="{{ route('income-expense-summary') }}?report=income" class="nav-link {{ request()->routeIs('income-expense-summary') && request()->query('report') === 'income' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line text-success"></i>
                        <p>Income Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('income-expense-summary') }}?report=expense" class="nav-link {{ request()->routeIs('income-expense-summary') && request()->query('report') === 'expense' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line text-danger"></i>
                        <p>Expense Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('income-expense-summary') }}" class="nav-link {{ request()->routeIs('income-expense-summary') && !request()->query('report') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie text-info"></i>
                        <p>Summary</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index') && !request()->query('report') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt text-pink"></i>
                        <p>Account Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('suppliers.reports') }}" class="nav-link {{ request()->routeIs('suppliers.reports') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie text-orange"></i>
                        <p>Supplier Summary</p>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-xs text-muted mt-2">SETTINGS</li>
                @php
                    $typeRoutes = ['income-types.index', 'expense-types.index', 'bank-accounts.index', 'suppliers.index'];
                @endphp
                <li class="nav-item {{ in_array(Route::currentRouteName(), $typeRoutes) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $typeRoutes) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Voucher Creation
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('income-types.index') }}" class="nav-link {{ request()->routeIs('income-types.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-circle-notch text-success"></i>
                                <p>Income Creation</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expense-types.index') }}" class="nav-link {{ request()->routeIs('expense-types.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-circle-notch text-danger"></i>
                                <p>Expense Creation</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bank-accounts.index') }}" class="nav-link {{ request()->routeIs('bank-accounts.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university text-primary"></i>
                                <p>Bank Accounts Creation</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-tie text-warning"></i>
                                <p>Suppliers Creation</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User Management</p>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('opening-balances.index') }}" class="nav-link {{ request()->routeIs('opening-balances.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-landmark"></i>
                        <p>Opening Balances</p>
                    </a>
                </li>

                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
                    <li class="nav-item">
                        <a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Company Management</p>
                        </a>
                    </li>
                @endif

                <li class="nav-item mt-auto">
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 text-left nav-link text-white" style="font-weight: bold;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <span class="ml-2">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Custom CSS -->
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
    }
    .sidebar {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .nav-sidebar {
        flex: 1;
    }
    .nav-item.mt-auto {
        margin-top: auto !important;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    .text-orange {
        color: orange !important;
    }
</style>

<!-- JS for Time -->
<script>
    function updateTime() {
        const now = new Date();
        const time = now.toLocaleTimeString();
        document.getElementById('current-time').textContent = time;
    }
    setInterval(updateTime, 1000);
    updateTime(); // initial call
</script>
