<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Member Report</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-4xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">Create Member Report</h1>
            <p class="text-sm text-slate-500 mt-1">Add new financial transaction for member</p>
        </div>
        <a href="{{ route('member-reports.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-700 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('member-reports.store') }}" method="POST" class="space-y-6">
        @csrf

        <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 space-y-5">
            <h2 class="text-lg font-semibold text-slate-900"><i class="fa-solid fa-receipt text-emerald-600 mr-2"></i>Report Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 required">Member</label>
                    <select name="member_id" id="member_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" selected disabled>Select member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->house?->house_name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Receipt No (Auto)</label>
                    <input type="text" id="receipt_no_display" readonly class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-600" placeholder="Auto-generated">
                    <input type="hidden" name="receipt_no" id="receipt_no">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 required">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 required">Transaction Type</label>
                    <select name="transaction_type" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" selected disabled>Select type</option>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type }}" {{ old('transaction_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Posting Year</label>
                    <input type="text" name="posting_year" value="{{ old('posting_year') }}" placeholder="e.g., 2023-2024" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 required">Status</label>
                    <select name="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Transaction details..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ old('description') }}</textarea>
            </div>
        </section>

        <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 space-y-5">
            <h2 class="text-lg font-semibold text-slate-900"><i class="fa-solid fa-money-bill text-sky-600 mr-2"></i>Financial Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Debit (Amount Owed)</label>
                    <input type="number" step="0.01" name="debit" id="debit" value="{{ old('debit') }}" placeholder="0.00" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Credit (Amount Paid)</label>
                    <input type="number" step="0.01" name="credit" id="credit" value="{{ old('credit') }}" placeholder="0.00" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-600">Balance</label>
                    <input type="text" id="balance_display" readonly class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-600 font-semibold" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                        <option value="">Select method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Remarks</label>
                    <input type="text" name="remarks" value="{{ old('remarks') }}" placeholder="Additional notes..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-white font-semibold hover:bg-emerald-700">
                <i class="fa-solid fa-floppy-disk"></i> Save Report
            </button>
            <a href="{{ route('member-reports.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 text-slate-700 font-semibold hover:bg-slate-50">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Calculate balance on load
    function updateBalance() {
        const debit = parseFloat($('#debit').val()) || 0;
        const credit = parseFloat($('#credit').val()) || 0;
        const balance = debit - credit;
        $('#balance_display').val(balance.toFixed(2));
    }

    // Generate receipt number on member selection
    function generateReceiptNo() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        const receiptNo = `MR-${year}${month}${day}-${random}`;
        $('#receipt_no_display').val(receiptNo);
        $('#receipt_no').val(receiptNo);
    }

    $('#debit, #credit').on('change', updateBalance);
    $('#member_id').on('change', generateReceiptNo);

    // Initial calls
    updateBalance();
    generateReceiptNo();
});
</script>
</body>
</html>
