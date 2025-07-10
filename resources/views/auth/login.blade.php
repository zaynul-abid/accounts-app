<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <div class="flex-1 flex items-center justify-center p-8">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8">
            <h2 class="text-center text-2xl font-bold mb-6">Sign in to your account</h2>
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email address</label>
                    <input type="email" id="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('email') border-red-500 @enderror" placeholder="you@example.com" required autofocus onblur="fetchCompanies(this.value)" value="{{ old('email') }}">
                    @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div id="companyInfoContainer" class="mb-6 hidden animate-fade-in">
                    <div id="companySelectContainer" class="hidden">
                        <label for="company_id" class="block text-sm font-medium text-gray-900 mb-2">Company</label>
                        <select id="company_id" name="company_id" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-[url('data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"%236b7280\"%3E%3Cpath stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/%3E%3C/svg%3E')] bg-no-repeat bg-[right_0.75rem_center] bg-[length:1.25rem] disabled:opacity-60 disabled:cursor-not-allowed disabled:bg-[url('data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"%239ca3af\"%3E%3Cpath stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/%3E%3C/svg%3E')]">
                        <option value="">Select your company</option>
                        </select>
                        @error('company_id')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="companyNameContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Company</label>
                        <div id="company_name" class="text-sm font-medium text-gray-900"></div>
                    </div>
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

<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `p-3 mb-2 rounded-lg text-white text-sm shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        toast.textContent = message;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    async function fetchCompanies(email) {
        if (!email) return;

        const companyInfoContainer = document.getElementById('companyInfoContainer');
        const companySelectContainer = document.getElementById('companySelectContainer');
        const companyNameContainer = document.getElementById('companyNameContainer');
        const companySelect = document.getElementById('company_id');
        const companyName = document.getElementById('company_name');

        companyInfoContainer.style.display = 'block';
        if (companySelectContainer) companySelectContainer.classList.add('hidden');
        if (companyNameContainer) companyNameContainer.classList.add('hidden');

        try {
            const response = await fetch('/get-companies?email=' + encodeURIComponent(email), {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok && data.usertype) {
                if (['admin', 'superadmin'].includes(data.usertype) && data.companies.length > 0) {
                    if (companySelectContainer) {
                        companySelectContainer.classList.remove('hidden');
                        companySelect.disabled = true;
                        companySelect.innerHTML = '<option value="">Select your company</option>';
                        data.companies.forEach(company => {
                            const option = document.createElement('option');
                            option.value = company.id;
                            option.textContent = company.name;
                            if (company.id == data.current_company_id) option.selected = true;
                            companySelect.appendChild(option);
                        });
                        companySelect.disabled = false;
                    }
                } else if (data.company_name) {
                    if (companyNameContainer) {
                        companyNameContainer.classList.remove('hidden');
                        companyName.textContent = data.company_name;
                    }
                } else {
                    companyInfoContainer.style.display = 'none';
                    showToast('No company information available', 'error');
                }
            } else {
                companyInfoContainer.style.display = 'none';
                showToast(data.message || 'No companies found', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Error loading company information', 'error');
        }
    }

    document.getElementById('company_id')?.addEventListener('change', async function () {
        const email = document.getElementById('email').value;
        const companyId = this.value;
        if (!email || !companyId) return;

        try {
            const response = await fetch('/update-company', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email, company_id: companyId })
            });

            const data = await response.json();
            if (data.success) {
                showToast('Company updated');
            } else {
                showToast(data.message || 'Update failed', 'error');
            }
        } catch (err) {
            showToast('Update error', 'error');
        }
    });
</script>
</body>
</html>
