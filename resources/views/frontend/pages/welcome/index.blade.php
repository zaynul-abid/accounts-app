<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramanthali Muslim Jama-ath Committee | Official Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --mahall-emerald: #064e3b;
            --mahall-gold: #c5a059;
            --mahall-dark: #022c22;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Jost', sans-serif;
            background-color: var(--mahall-dark);
        }

        /* Background Image with Dark Overlay */
        .hero-section {
            background: linear-gradient(rgba(2, 44, 34, 0.8), rgba(2, 44, 34, 0.8)),
                        url('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .serif-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.75rem, 3.5vw, 2.85rem);
            color: #ffffff;
            letter-spacing: 0;
            line-height: 1.15;
            max-width: 760px;
            margin-inline: auto;
            text-wrap: balance;
        }

        .sub-text {
            color: var(--mahall-gold);
            letter-spacing: 5px;
            text-transform: uppercase;
            font-weight: 400;
            font-size: 0.9rem;
        }

        /* Custom Bootstrap Button Styling */
        .btn-mahall {
            background-color: transparent;
            color: var(--mahall-gold);
            border: 1px solid var(--mahall-gold);
            padding: 12px 50px;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.4s ease;
            border-radius: 0; /* Square edge for a more formal look */
        }

        .btn-mahall:hover {
            background-color: var(--mahall-gold);
            color: var(--mahall-dark);
            box-shadow: 0 0 20px rgba(197, 160, 89, 0.4);
            transform: translateY(-2px);
        }

        .divider {
            width: 60px;
            height: 2px;
            background-color: var(--mahall-gold);
            margin: 20px auto;
        }

        /* Subtle Fade In Animation */
        .fade-in-up {
            animation: fadeInUp 1.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<section class="hero-section text-center text-white">
    <div class="container fade-in-up">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <p class="sub-text mb-2">The Official Portal of</p>
                <h3 class="serif-title mb-0">
                    Ramanthali Muslim<br>
                    Jama-ath Committee
                </h3>
                <div class="divider"></div>

                <p class="lead mb-5 opacity-75 italic text-light" style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem;">
                    "Unity in Faith, Excellence in Service"
                </p>

               <div class="mt-4">
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-mahall shadow-sm">
            Go to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-mahall shadow-sm">
            Member Login
        </a>
    @endauth
</div>
            </div>
        </div>
    </div>

    <div class="position-absolute bottom-0 start-50 translate-middle-x pb-4">
        <p class="small opacity-50 mb-0" style="letter-spacing: 2px;">VADAKKUMBAD, KANNUR, KERALA</p>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
