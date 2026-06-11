@extends('backend.layouts.app')
@section('title','Dashboard')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header','Dashboard')
@section('sub-header','Overview')
@section('content')
    @php
        $balance = ($totalDebit ?? 0) - ($totalCredit ?? 0);
        $quickActions = [
            [
                'title' => 'House Creation',
                'subtitle' => 'Register and manage household details',
                'route' => route('house-creations.index'),
                'icon' => 'fa-home',
                'color' => 'green',
            ],
            [
                'title' => 'Member Creation',
                'subtitle' => 'Add members and subscription details',
                'route' => route('members.index'),
                'icon' => 'fa-user-plus',
                'color' => 'cyan',
            ],
            [
                'title' => 'Member List',
                'subtitle' => 'Search houses, owners, and members',
                'route' => route('members.list'),
                'icon' => 'fa-address-book',
                'color' => 'blue',
            ],
            [
                'title' => 'Member Reports',
                'subtitle' => 'Review debit, credit, and balances',
                'route' => route('member-reports.index'),
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'amber',
            ],
            [
                'title' => 'Yearly Payment',
                'subtitle' => 'Post subscription receipt payments',
                'route' => route('member-reports.yearly-payment.create'),
                'icon' => 'fa-hand-holding-usd',
                'color' => 'indigo',
            ],
            [
                'title' => 'Lookup Masters',
                'subtitle' => 'Maintain relations, places, and types',
                'route' => route('admin.lookups.index', 'relations'),
                'icon' => 'fa-cogs',
                'color' => 'slate',
            ],
        ];

        if (auth()->user()?->isAdmin()) {
            $quickActions[] = [
                'title' => 'Manage Users',
                'subtitle' => 'Create users, update email, password, and remove access',
                'route' => route('admin.users.index'),
                'icon' => 'fa-user-shield',
                'color' => 'blue',
            ];
        }
    @endphp

    <section class="content mahallu-dashboard">
        <div class="container-fluid">
            <div class="dashboard-hero">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h2>Ramanthali Muslim Jama-ath Committee</h2>
                    <div class="hero-copy">
                        <p>Kerala State Wakaf Board Reg No: *165/RA/1961- Society</p>
                        <p>Reg No: *79/1988- PH- 8289982285</p>
                        <p>Ramanthali, Vadakkumbad P.O.</p>
                        <p>Kannur District, Kerala</p>
                        <p>India - 670308</p>
                    </div>
                </div>
                <div class="hero-balance">
                    <span>Current Balance</span>
                    <strong>₹{{ number_format($balance, 2) }}</strong>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-building"></i></div>
                    <div>
                        <span>Total Houses</span>
                        <strong>{{ number_format($totalHouses ?? 0) }}</strong>
                        <small>{{ number_format($activeHouses ?? 0) }} active</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div>
                        <span>Total Members</span>
                        <strong>{{ number_format($totalMembers ?? 0) }}</strong>
                        <small>{{ number_format($activeMembers ?? 0) }} active</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon indigo"><i class="fas fa-user-check"></i></div>
                    <div>
                        <span>Subscriptions</span>
                        <strong>{{ number_format($subscribedMembers ?? 0) }}</strong>
                        <small>enabled members</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="fas fa-receipt"></i></div>
                    <div>
                        <span>Reports</span>
                        <strong>{{ number_format($totalReports ?? 0) }}</strong>
                        <small>{{ number_format($pendingReports ?? 0) }} pending</small>
                    </div>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="panel-header">
                    <div>
                        <h3>Quick Actions</h3>
                        <p>Open the most-used sections directly from here.</p>
                    </div>
                    <a href="{{ route('member-reports.index') }}" class="panel-link">
                        View reports <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-grid">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['route'] }}" class="action-card {{ $action['color'] }}">
                            <span class="action-icon"><i class="fas {{ $action['icon'] }}"></i></span>
                            <span class="action-text">
                                <strong>{{ $action['title'] }}</strong>
                                <small>{{ $action['subtitle'] }}</small>
                            </span>
                            <i class="fas fa-chevron-right action-arrow"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="finance-grid">
                <div class="finance-card debit">
                    <span>Total Debit</span>
                    <strong>₹{{ number_format($totalDebit ?? 0, 2) }}</strong>
                </div>
                <div class="finance-card credit">
                    <span>Total Credit</span>
                    <strong>₹{{ number_format($totalCredit ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>
    </section>

    <style>
        .mahallu-dashboard {
            color: #1f2937;
        }
        .dashboard-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }
        .eyebrow {
            margin: 0 0 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .dashboard-hero h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
        }
        .hero-copy {
            margin: 8px 0 0;
            color: #64748b;
        }
        .hero-copy p {
            margin: 0 0 4px;
        }
        .hero-copy p:last-child {
            margin-bottom: 0;
        }
        .hero-balance {
            min-width: 220px;
            padding: 16px 18px;
            border-radius: 8px;
            background: #0f766e;
            color: #ffffff;
        }
        .hero-balance span,
        .finance-card span,
        .stat-card span {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: inherit;
            opacity: 0.78;
        }
        .hero-balance strong {
            display: block;
            margin-top: 4px;
            font-size: 24px;
        }
        .stats-grid,
        .action-grid,
        .finance-grid {
            display: grid;
            gap: 16px;
        }
        .stats-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 20px;
        }
        .stat-card {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 18px;
            min-height: 112px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
        }
        .stat-card strong {
            display: block;
            font-size: 26px;
            line-height: 1.1;
            color: #111827;
        }
        .stat-card small {
            color: #64748b;
            font-weight: 600;
        }
        .stat-icon,
        .action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 46px;
            height: 46px;
            border-radius: 8px;
            color: #ffffff;
        }
        .green { background: #16a34a; }
        .blue { background: #2563eb; }
        .cyan { background: #0891b2; }
        .indigo { background: #4f46e5; }
        .amber { background: #d97706; }
        .slate { background: #475569; }
        .dashboard-panel {
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }
        .panel-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
        }
        .panel-header p {
            margin: 4px 0 0;
            color: #64748b;
        }
        .panel-link {
            color: #0f766e;
            font-weight: 700;
        }
        .action-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .action-card {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 96px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #1f2937;
            background: #f8fafc;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .action-card:hover {
            color: #111827;
            text-decoration: none;
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }
        .action-text {
            flex: 1;
            min-width: 0;
        }
        .action-text strong,
        .action-text small {
            display: block;
        }
        .action-text small {
            margin-top: 3px;
            color: #64748b;
        }
        .action-arrow {
            color: #94a3b8;
        }
        .finance-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 20px;
        }
        .finance-card {
            padding: 20px;
            border-radius: 8px;
            color: #ffffff;
        }
        .finance-card strong {
            display: block;
            margin-top: 4px;
            font-size: 26px;
        }
        .finance-card.debit { background: #92400e; }
        .finance-card.credit { background: #047857; }

        @media (max-width: 991px) {
            .stats-grid,
            .action-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 767px) {
            .dashboard-hero,
            .panel-header {
                align-items: stretch;
                flex-direction: column;
            }
            .hero-balance {
                min-width: 0;
            }
            .stats-grid,
            .action-grid,
            .finance-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
