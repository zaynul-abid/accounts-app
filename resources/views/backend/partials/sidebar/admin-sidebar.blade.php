<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{{ asset('backend/assets/dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Accounts</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->profile_image ?? asset('backend/assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name ?? 'Guest' }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{route('admin.dashboard')}}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a href="{{route('users.index')}}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Users</p>
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item">
                    <a href="{{route('income-types.index')}}" class="nav-link {{ request()->routeIs('income-types.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Income Types</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('expense-types.index')}}" class="nav-link {{ request()->routeIs('expense-types.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Expense Types</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('incomes.index')}}" class="nav-link {{ request()->routeIs('incomes.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Income</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('expenses.index')}}" class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Expense</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route('income-expense-summary')}}" class="nav-link {{ request()->routeIs('income-expense-summary') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p> Summary</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('opening-balances.index')}}" class="nav-link {{ request()->routeIs('opening-balances.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p> Opening Balance</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('bank-accounts.index')}}" class="nav-link {{ request()->routeIs('bank-accounts.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Bank Accounts</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('suppliers.index')}}" class="nav-link {{ request()->routeIs('suppliers.index.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p> Suppliers</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('suppliers.details')}}" class="nav-link {{ request()->routeIs(' suppliers.details') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p> Suppliers Details</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('companies.index')}}" class="nav-link {{ request()->routeIs('expense-types.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Companies</p>
                    </a>
                </li>




                {{--                @php--}}
{{--                    $productRoutes = ['products.index', 'categories.index', 'sizes.index', 'brands.index', 'colors.index'];--}}
{{--                @endphp--}}

{{--                    <!-- Categories (with Sub-Menu) -->--}}
{{--                <li class="nav-item {{ in_array(Route::currentRouteName(), $productRoutes) ? 'menu-open' : '' }}">--}}
{{--                    <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $productRoutes) ? 'active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-tags"></i>--}}
{{--                        <p>--}}
{{--                            Products--}}
{{--                            <i class="right fas fa-angle-left"></i>--}}
{{--                        </p>--}}
{{--                    </a>--}}
{{--                    <ul class="nav nav-treeview">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('products.index')}}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>View Products</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('categories.index')}}" class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p> Categories</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('sizes.index')}}" class="nav-link {{ request()->routeIs('sizes.index') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>Size</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('brands.index')}}" class="nav-link {{ request()->routeIs('brands.index') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>Brand</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{route('colors.index')}}" class="nav-link {{ request()->routeIs('colors.index') ? 'active' : '' }}">--}}
{{--                                <i class="far fa-circle nav-icon"></i>--}}
{{--                                <p>Color</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
                <!-- Orders -->
{{--                <li class="nav-item">--}}
{{--                    <a href="" class="nav-link {{ request()->routeIs('images.index') ? 'active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-shopping-cart"></i>--}}
{{--                        <p>Orders</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
