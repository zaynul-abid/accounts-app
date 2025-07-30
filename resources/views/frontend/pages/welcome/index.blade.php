<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Website</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 1s ease-out forwards;
        }
        .accounting-bg {
            background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        .overlay {
            background: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
        }
        .btn-pulse {
            transition: all 0.3s ease;
        }
        .btn-pulse:hover {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.8);
            transform: scale(1.05);
        }
    </style>
</head>
<body class="accounting-bg">
<div class="overlay flex items-center justify-center min-h-screen">
    <div class="text-center px-6 max-w-4xl mx-auto">
        <!-- Main Heading -->
        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight animate-fadeIn">
            Record<br>
            <span class="text-green-400"> Every Transaction</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-200 mb-10 animate-fadeIn" style="animation-delay: 0.2s;">
            Record, analyze, and succeed with our intuitive financial solutions.
        </p>

        <div x-data="{ open: false }">

            <!-- ✅ Welcome Button (opens modal) -->
            <div class="mt-10 animate-fadeIn" style="animation-delay: 0.4s;">
                <button
                    @click="open = true"
                    class="inline-block px-12 py-4 bg-green-500 text-white rounded-lg
                   font-semibold text-lg btn-pulse">
                    Welcome
                </button>
            </div>

            <!-- ✅ Modal Overlay and Box -->
            <div
                x-show="open"
                x-transition
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div
                    @click.away="open = false"
                    class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg"
                >
                    @php
                        use Illuminate\Support\Facades\Crypt;
                    @endphp

                    <form method="GET" action="{{ route('login') }}">
                        <div class="mb-4">
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Select Company</label>
                            <select id="company_id" name="company"
                                    class="block w-full px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                   text-gray-700 text-sm" required>
                                <option value="" disabled selected>-- Choose a Company --</option>
                                @foreach($companies as $company)
                                    <option value="{{ Crypt::encryptString($company->id) }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-base">
                            Go to Login
                        </button>
                    </form>

                </div>
            </div>

        </div>

        <!-- Decorative Financial Icons -->
        <div class="mt-12 flex justify-center space-x-6 animate-fadeIn" style="animation-delay: 0.6s;">
            <svg class="w-8 h-8 text-green-400 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg class="w-8 h-8 text-green-400 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <svg class="w-8 h-8 text-green-400 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>
</div>

</body>
</html>
