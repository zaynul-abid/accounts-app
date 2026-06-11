<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $memberInfo->name }} - Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('partials.sweet-alert')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-6xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">{{ $memberInfo->name }}'s Reports</h1>
            <p class="text-sm text-slate-500 mt-1">House: {{ $memberInfo->house?->house_name ?? 'N/A' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('member-reports.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('member-reports.yearly-payment.create', ['member_id' => $memberInfo->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700">
                <i class="fa-solid fa-hand-holding-dollar"></i> Yearly Payment
            </a>
            <a href="{{ route('member-reports.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 text-sm font-medium hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Add Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">
            <p class="text-green-700"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Total Debit</p>
            <p class="text-2xl font-bold text-red-600">₹ {{ number_format($totalDebit, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">Amount Owed</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Total Credit</p>
            <p class="text-2xl font-bold text-green-600">₹ {{ number_format($totalCredit, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">Amount Paid</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Balance</p>
            <p class="text-2xl font-bold {{ $balance > 0 ? 'text-amber-600' : 'text-green-600' }}">
                ₹ {{ number_format($balance, 2) }}
            </p>
            <p class="text-xs text-slate-500 mt-2">{{ $balance > 0 ? 'Due' : 'Credit' }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-4">
            <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Total Transactions</p>
            <p class="text-2xl font-bold text-slate-900">{{ $reports->total() }}</p>
            <p class="text-xs text-slate-500 mt-2">Records</p>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Receipt No</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Transaction Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Posting Year</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-700">Debit</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-700">Credit</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-700">Balance</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr class="border-b border-slate-200 hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-emerald-600">{{ $report->receipt_no }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $report->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $report->transaction_type }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $report->posting_year ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">
                                {{ $report->debit ? '₹ ' . number_format($report->debit, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">
                                {{ $report->credit ? '₹ ' . number_format($report->credit, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                @if($report->balance > 0)
                                    <span class="text-amber-600">₹ {{ number_format($report->balance, 2) }}</span>
                                @else
                                    <span class="text-green-600">₹ {{ number_format($report->balance, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($report->status === 'completed')
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5">Completed</span>
                                @elseif($report->status === 'pending')
                                    <span class="inline-flex rounded-full bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5">Pending</span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('member-reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-700 text-xs" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('member-reports.edit', $report->id) }}" class="text-emerald-600 hover:text-emerald-700 text-xs" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if(auth()->user()?->isAdmin())
                                        <form action="{{ route('member-reports.destroy', $report->id) }}" method="POST" class="inline" data-confirm="Are you sure you want to delete this report?" data-confirm-button="Yes, delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                <i class="fa-solid fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No reports for this member. <a href="{{ route('member-reports.create') }}" class="text-emerald-600 hover:text-emerald-700">Create one</a></p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($reports->hasPages())
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif
</div>
</body>
</html>
