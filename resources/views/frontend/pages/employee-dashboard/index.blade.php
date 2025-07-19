<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .grid-button {
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .grid-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            transition: all 0.3s;
            margin-top: 0;
        }
        .sidebar-collapsed {
            width: 64px;
        }
        .sidebar-collapsed .profile-section {
            display: none;
        }
        .sidebar-collapsed .logout-text {
            display: none;
        }
        .sidebar-collapsed .logout-button {
            justify-content: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: absolute;
                z-index: 50;
                height: 100vh;
            }
            .sidebar-open {
                transform: translateX(0);
            }
            .sidebar-collapsed {
                width: 64px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar - Profile Only -->
    <div class="sidebar bg-white w-64 border-r border-gray-200 flex flex-col">
        <!-- Collapse Button -->
        <div class="p-4 border-b border-gray-200 flex justify-center" style="margin-top: 0; padding-top: 0;">
            <button id="collapseToggle" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
        </div>
        <!-- Profile Section -->
        <div class="profile-section p-6 flex-1">
            <div class="flex flex-col items-center">


                    <h3 class="text-xl font-semibold text-gray-800">{{auth()->user()->name}}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{auth()->user()->usertype ?? '-' }}</p>


                    <div class="mt-6 w-full">
                        <div class="mb-3">
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm text-gray-700">{{ auth()->user()->email ?? '-' }}</p>

                        </div>
                        <div class="mb-3">
                            <p class="text-xs text-gray-500">Company</p>
                            <p class="text-sm text-gray-700">
                                {{ auth()->user()->company?->name ?? '-' }}
                            </p>
                        </div>

                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button flex items-center justify-center p-2 rounded-lg text-gray-700 hover:bg-gray-100 w-full">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span class="logout-text">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Content Area - Grid Buttons -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl font-semibold text-gray-800 mb-8">ACCOUNTS MANAGEMENT</h2>

                <!-- Grid of Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Incomes -->
                    <a href="{{route('incomes.index')}}" class="grid-button bg-white rounded-xl p-6 text-center flex flex-col items-center border border-gray-200 hover:border-indigo-300">
                        <div class="bg-indigo-100 p-4 rounded-full text-indigo-600 mb-4">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Incomes</h3>
                        <p class="text-gray-500 text-sm">View and manage your income records</p>
                    </a>

                    <!-- Expenses -->
                    <a href="{{route('expenses.index')}}" class="grid-button bg-white rounded-xl p-6 text-center flex flex-col items-center border border-gray-200 hover:border-indigo-300">
                        <div class="bg-green-100 p-4 rounded-full text-green-600 mb-4">
                            <i class="fas fa-file-invoice-dollar text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Expenses</h3>
                        <p class="text-gray-500 text-sm">Track and submit expense reports</p>
                    </a>

                    <!-- Income-Expense Summary -->
{{--                    <a href="{{route('income-expense-summary')}}" class="grid-button bg-white rounded-xl p-6 text-center flex flex-col items-center border border-gray-200 hover:border-indigo-300">--}}
{{--                        <div class="bg-blue-100 p-4 rounded-full text-blue-600 mb-4">--}}
{{--                            <i class="fas fa-chart-line text-2xl"></i>--}}
{{--                        </div>--}}
{{--                        <h3 class="font-semibold text-gray-800 mb-2">Income-Expense Summary</h3>--}}
{{--                        <p class="text-gray-500 text-sm">View financial summaries</p>--}}
{{--                    </a>--}}

                    <!-- Suppliers Details -->
                    <a href="{{route('suppliers.details')}}" class="grid-button bg-white rounded-xl p-6 text-center flex flex-col items-center border border-gray-200 hover:border-indigo-300">
                        <div class="bg-amber-100 p-4 rounded-full text-amber-600 mb-4">
                            <i class="fas fa-user-tie text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Suppliers Summary</h3>
                        <p class="text-gray-500 text-sm">Manage supplier </p>
                    </a>


                    <a href="{{route('suppliers.reports')}}" class="grid-button bg-white rounded-xl p-6 text-center flex flex-col items-center border border-gray-200 hover:border-indigo-300">
                        <div class="bg-amber-100 p-4 rounded-full text-amber-600 mb-4">
                            <i class="fas fa-industry text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Suppliers Details</h3>
                        <p class="text-gray-500 text-sm">View supplier information</p>
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Toggle sidebar collapse
    document.getElementById('collapseToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        const collapseIcon = this.querySelector('i');
        sidebar.classList.toggle('sidebar-collapsed');

        // Update collapse icon
        if (sidebar.classList.contains('sidebar-collapsed')) {
            collapseIcon.classList.remove('fa-chevron-left');
            collapseIcon.classList.add('fa-chevron-right');
        } else {
            collapseIcon.classList.remove('fa-chevron-right');
            collapseIcon.classList.add('fa-chevron-left');
        }
    });
</script>
</body>
</html>
