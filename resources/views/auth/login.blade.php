<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="font-inter bg-gray-100 text-gray-900">
<div class="flex min-h-screen">
    <div class="hidden lg:flex flex-1 bg-gradient-to-br from-indigo-600 to-indigo-500 text-white p-8 items-center justify-center">
        <div class="max-w-md">
            <h1 class="text-3xl font-bold mb-4">Welcome Back</h1>
            <p class="text-lg">Access your account and manage your companies with ease.</p>
        </div>
    </div>
    @if(session('show_company_error'))
        <div x-data="{ open: true }"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">

            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="open = false"
                 class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-gray-200 dark:border-gray-700 transition-all transform">

                <!-- Header with icon -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-white/20 backdrop-blur-sm mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Company Verification Required</h2>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-4">
                    <p class="text-gray-700 dark:text-gray-300 text-center">
                        You must select your assigned company to continue. Please return to the welcome page and choose the correct company from the dropdown menu.
                    </p>

                    <div class="flex justify-center pt-2">
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all hover:scale-[1.02]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Return to Welcome Page
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700/30 px-4 py-3 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Need help? Contact support@yourcompany.com
                    </p>
                </div>
            </div>
        </div>
    @endif


    <div class="flex-1 flex items-center justify-center p-8">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8">
            <h2 class="text-center text-2xl font-bold mb-6">Sign in to your account</h2>
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    @if($company)
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                    @endif
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email address</label>
                    <input type="email" id="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('email') border-red-500 @enderror" placeholder="you@example.com" required autofocus onblur="fetchCompanies(this.value)" value="{{ old('email') }}">
                    @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                    @error('password')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="w-full p-3 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-500 transition disabled:opacity-60 disabled:cursor-not-allowed" id="loginButton">
                    <span id="buttonText">Sign in</span>
                </button>
            </form>
        </div>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-[9999]"></div>
</body>
</html>
