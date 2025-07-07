<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IncomeTracker - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pure-black: #000000;
            --dark-matte: #0a0a0a;
            --neon-green: #00ff88;
            --soft-green: #00cc6a;
            --light-text: #f0f0f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--pure-black);
            color: var(--light-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.7;
        }

        .header {
            background-color: var(--dark-matte);
            padding: 5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(0, 255, 136, 0.1);
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--neon-green);
            margin-bottom: 1.5rem;
        }

        .header p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        .login-btn {
            background: var(--neon-green);
            color: var(--pure-black);
            border: none;
            padding: 0.75rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: var(--soft-green);
            transform: translateY(-2px);
        }

        .company-select-section {
            padding: 4rem 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .company-select-section h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--neon-green);
            text-align: center;
        }

        .company-form {
            background-color: rgba(20, 20, 20, 0.7);
            padding: 2rem;
            border-left: 3px solid var(--neon-green);
        }

        .form-control, .form-select {
            background-color: var(--dark-matte);
            color: var(--light-text);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--dark-matte);
            color: var(--light-text);
            border-color: var(--neon-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 255, 136, 0.25);
        }

        .invalid-feedback {
            color: #ff4d4d;
        }

        .footer {
            background-color: var(--dark-matte);
            text-align: center;
            padding: 2rem 1rem;
            margin-top: auto;
            border-top: 1px solid rgba(0, 255, 136, 0.1);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container">
        <h1>INCOMETRACKER</h1>
        <p class="mb-4">Financial management system for tracking income and expenses</p>
    </div>
</header>

<section class="company-select-section">
    <div class="container">
        <h2>SELECT COMPANY</h2>
        <div class="company-form">
            <form id="companySelectForm" action="{{ route('welcome.redirect') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="company_id" class="form-label">Choose a Company</label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Select a company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>© 2025 IncomeTracker. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        console.log("✅ jQuery is loaded!");

        $('#companySelectForm').on('submit', function (e) {
            const companyId = $('#company_id').val();
            if (!companyId) {
                e.preventDefault();
                $('#company_id').addClass('is-invalid');
                $('#company_id').next('.invalid-feedback').text('Please select a company.');
            }
        });

        $('#company_id').on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').text('');
            }
        });
    });
</script>
</body>
</html>
