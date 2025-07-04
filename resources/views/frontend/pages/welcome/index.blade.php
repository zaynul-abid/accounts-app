<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IncomeTracker - Financial Notes</title>
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

        .notes-section {
            padding: 4rem 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .notes-section h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--neon-green);
            text-align: center;
        }

        .notes-content {
            background-color: rgba(20, 20, 20, 0.7);
            padding: 2rem;
            border-left: 3px solid var(--neon-green);
        }

        .notes-content p {
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 1.5rem;
        }

        .notes-content p::before {
            content: "•";
            color: var(--neon-green);
            position: absolute;
            left: 0;
            font-size: 1.5rem;
            line-height: 1;
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
        <a href="{{route('login')}}" class="btn login-btn">
            <i class="fas fa-sign-in-alt me-2"></i> Login
        </a>
    </div>
</header>

<section class="notes-section">
    <div class="container">
        <h2>NOTES</h2>
        <div class="notes-content">
            <p>Track all income sources including sales, services, and investments with detailed categorization for better financial analysis.</p>
            <p>Monitor expenses across departments with automated alerts when budgets are nearing their limits.</p>
            <p>Generate comprehensive reports that provide insights into financial trends and help with forecasting.</p>
            <p>Set up custom notifications for important financial events and milestones.</p>
            <p>Maintain audit trails for all financial transactions to ensure compliance and transparency.</p>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>© 2025 IncomeTracker. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
