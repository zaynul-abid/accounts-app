<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @include('partials.sweet-alert')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">Report Details</h1>
            <p class="text-sm text-slate-500 mt-1">Receipt: {{ $memberReport->receipt_no }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('member-reports.show-member', $memberReport->member_id) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('member-reports.edit', $memberReport->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 text-sm font-medium hover:bg-emerald-700">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
        </div>
    </div>

    <!-- Member Info -->
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-user text-emerald-600 mr-2"></i>Member Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Member Name</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->member->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">House</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->member->house?->house_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Contact</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->member->mobile_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Report Details -->
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-receipt text-sky-600 mr-2"></i>Transaction Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Receipt Number</p>
                <p class="text-base font-mono font-semibold text-emerald-600">{{ $memberReport->receipt_no }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Date</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Transaction Type</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->transaction_type }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Posting Year</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->posting_year ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Payment Method</p>
                <p class="text-base font-semibold text-slate-900">{{ $memberReport->payment_method ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-500 mb-1">Status</p>
                <div class="mt-1">
                    @if($memberReport->status === 'completed')
                        <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-3 py-1">Completed</span>
                    @elseif($memberReport->status === 'pending')
                        <span class="inline-flex rounded-full bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1">Pending</span>
                    @else
                        <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-3 py-1">Cancelled</span>
                    @endif
                </div>
            </div>
        </div>

        @if($memberReport->description)
            <div class="mt-6 pt-6 border-t border-slate-200">
                <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Description</p>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $memberReport->description }}</p>
            </div>
        @endif
    </div>

    <!-- Financial Details -->
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-money-bill text-amber-600 mr-2"></i>Financial Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                <p class="text-xs font-semibold uppercase text-red-600 mb-1">Debit (Amount Owed)</p>
                <p class="text-3xl font-bold text-red-600">₹ {{ number_format($memberReport->debit, 2) }}</p>
            </div>
            <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                <p class="text-xs font-semibold uppercase text-green-600 mb-1">Credit (Amount Paid)</p>
                <p class="text-3xl font-bold text-green-600">₹ {{ number_format($memberReport->credit, 2) }}</p>
            </div>
            <div class="rounded-xl {{ $memberReport->balance > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-emerald-50 border border-emerald-200' }} p-4">
                <p class="text-xs font-semibold uppercase {{ $memberReport->balance > 0 ? 'text-amber-600' : 'text-emerald-600' }} mb-1">Balance</p>
                <p class="text-3xl font-bold {{ $memberReport->balance > 0 ? 'text-amber-600' : 'text-emerald-600' }}">₹ {{ number_format($memberReport->balance, 2) }}</p>
            </div>
        </div>
    </div>

    @if($memberReport->remarks)
        <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-note-sticky text-purple-600 mr-2"></i>Remarks</h2>
            <p class="text-slate-700 whitespace-pre-wrap">{{ $memberReport->remarks }}</p>
        </div>
    @endif

    <!-- Audit Trail -->
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-history text-slate-600 mr-2"></i>Audit Trail</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
            <div>
                <p class="font-medium text-slate-900">Created At</p>
                <p>{{ $memberReport->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <p class="font-medium text-slate-900">Last Updated</p>
                <p>{{ $memberReport->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('member-reports.edit', $memberReport->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-5 py-2.5 font-semibold hover:bg-emerald-700">
            <i class="fa-solid fa-pen"></i> Edit Report
        </a>
        @if(auth()->user()?->isAdmin())
            <form action="{{ route('member-reports.destroy', $memberReport->id) }}" method="POST" class="inline" data-confirm="Are you sure you want to delete this report?" data-confirm-button="Yes, delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 text-red-600 px-5 py-2.5 font-semibold hover:bg-red-50">
                    <i class="fa-solid fa-trash"></i> Delete Report
                </button>
            </form>
        @endif
        <a href="{{ route('member-reports.show-member', $memberReport->member_id) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 font-semibold hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Back to Member Reports
        </a>
    </div>
</div>
</body>
</html>
