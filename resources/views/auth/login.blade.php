<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Ramanthali Muslim Jama-ath Committee</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        .serif { font-family: 'Cormorant Garamond', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .mahall-emerald { background-color: #064e3b; }
        .btn-mahall {
            background-color: #064e3b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-mahall:hover {
            background-color: #022c22;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(6, 78, 59, 0.3);
        }
        .input-focus:focus {
            ring-color: #064e3b;
            border-color: transparent;
        }
    </style>
</head>
<body class="font-inter bg-gray-50 text-gray-900 overflow-x-hidden">

<div class="flex min-h-screen">
    <div class="hidden lg:flex flex-1 mahall-emerald relative items-center justify-center p-12 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/islamic-art.png');"></div>

        <div class="relative z-10 text-center">
            <h1 class="serif text-5xl xl:text-6xl text-white mb-4 leading-tight">Ramanthali Muslim<br>Jama-ath Committee</h1>
            <div class="w-20 h-1 bg-[#c5a059] mx-auto mb-6"></div>
            <p class="text-emerald-100 text-xl font-light italic tracking-wide">"Excellence in Service, Unity in Faith"</p>
        </div>

        <div class="absolute bottom-10 text-emerald-200/40 text-xs tracking-widest uppercase font-medium">
            Official Community Portal &copy; {{ date('Y') }}
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6 md:p-12 bg-white">
        <div class="w-full max-w-md">

            <div class="lg:hidden text-center mb-10">
                <h1 class="serif text-3xl sm:text-4xl text-emerald-900 leading-tight">Ramanthali Muslim<br>Jama-ath Committee</h1>
                <div class="w-12 h-0.5 bg-[#c5a059] mx-auto mt-2"></div>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-gray-500">Enter your credentials to access the portal.</p>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-800 focus:border-transparent outline-none transition-all @error('email') border-red-500 @enderror"
                           placeholder="name@email.com" required autofocus value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                    </div>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-800 focus:border-transparent outline-none transition-all @error('password') border-red-500 @enderror"
                           placeholder="••••••••" required>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                <button type="submit" id="loginButton" class="w-full btn-mahall text-white py-4 rounded-xl font-bold text-lg shadow-lg flex justify-center items-center">
                    <span id="buttonText">Sign In</span>
                </button>
            </form>

        </div>
    </div>
</div>

<script>
    // Visual feedback for form submission
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const buttonText = document.getElementById('buttonText');

    loginForm.addEventListener('submit', () => {
        loginButton.disabled = true;
        loginButton.classList.add('opacity-80', 'cursor-not-allowed');
        buttonText.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    });
</script>

</body>
</html>
