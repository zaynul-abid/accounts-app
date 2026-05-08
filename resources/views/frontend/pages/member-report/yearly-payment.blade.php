<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yearly Subscription Payment</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-5xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">Yearly Subscription Payment</h1>
            <p class="text-sm text-slate-500 mt-1">Enter yearly subscription receipt details</p>
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

    <form action="{{ route('member-reports.yearly-payment.store') }}" method="POST" class="space-y-6">
        @csrf

        <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 space-y-5">
            <h2 class="text-lg font-semibold text-slate-900">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-600 mr-2"></i>Payment Fields
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Receipt No</label>
                    <input type="text" name="receipt_no" value="{{ old('receipt_no') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="Enter receipt number">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 text-slate-900">
                        <i class="fa-solid fa-magnifying-glass text-emerald-600 mr-2"></i>Member Search
                    </label>
                    <div class="relative">
                        <input type="text" id="member_search" placeholder="Type member name or house name..." class="w-full rounded-lg border-2 border-slate-300 px-4 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition">
                        <div id="search_results" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-slate-300 rounded-lg shadow-lg max-h-60 overflow-y-auto z-10"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        <i class="fa-solid fa-circle-info"></i> Type member name or house name to search
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-slate-900">
                        <i class="fa-solid fa-user text-emerald-600 mr-2"></i>Select Member
                    </label>
                    <select name="member_id" id="member_id" required class="w-full rounded-lg border-2 border-slate-300 px-4 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none transition">
                        <option value="" selected disabled>-- Choose a member --</option>
                        @foreach($members as $member)
                            <option
                                value="{{ $member->id }}"
                                data-place="{{ $member->house?->place?->name ?? '' }}"
                                data-house-no="{{ $member->house?->house_no ?? '' }}"
                                data-house-name="{{ $member->house?->house_name ?? 'N/A' }}"
                                data-member-name="{{ $member->name }}"
                                data-subscription-type="{{ $member->subscription_type ?? '' }}"
                                data-subscription-amount="{{ $member->subscription_amount ?? 0 }}"
                                data-search-text="{{ strtolower($member->name . ' ' . ($member->house?->house_name ?? '') . ' ' . ($member->house?->house_no ?? '') . ' ' . ($member->house?->jamath_house_no ?? '') . ' ' . ($member->house?->house_owner ?? '') . ' ' . ($member->house?->place?->name ?? '')) }}"
                                {{ (string) old('member_id', $selectedMember?->id) === (string) $member->id ? 'selected' : '' }}
                            >
                                {{ $member->name }} — {{ $member->house?->house_name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">
                        <i class="fa-solid fa-circle-info"></i> Select from subscription-enabled members
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Place</label>
                    <input type="text" id="place_display" readonly class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">House No</label>
                    <input type="text" id="house_no_display" readonly class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Posting Year</label>
                    <input type="text" name="posting_year" value="{{ old('posting_year', date('Y')) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="e.g. 2026">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="0.00">
                </div>

                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm font-medium">
                        <input type="checkbox" id="include_previous_due" name="include_previous_due" value="1" {{ old('include_previous_due') ? 'checked' : '' }} class="rounded border-slate-300">
                        Enable Previous Due
                    </label>
                </div>

                <div id="due_amount_wrap" class="{{ old('include_previous_due') ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium mb-1">Due Amount</label>
                    <input type="number" step="0.01" min="0" id="due_amount" name="due_amount" value="{{ old('due_amount') }}" readonly class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-slate-50 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Received From</label>
                    <input type="text" id="received_from" name="received_from" value="{{ old('received_from') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="Name">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Receipt Mode</label>
                    <select name="receipt_mode" id="receipt_mode" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" selected disabled>Select mode</option>
                        @foreach($receiptModes as $mode)
                            <option value="{{ $mode }}" {{ old('receipt_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Receipt A/C</label>
                    <select name="receipt_account_id" id="receipt_account_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" selected disabled>Select receipt account</option>
                        @foreach($receiptAccounts as $account)
                            <option value="{{ $account->id }}" {{ (string) old('receipt_account_id') === (string) $account->id ? 'selected' : '' }}>
                                {{ $account->name }}{{ $account->account_number ? ' - A/C ' . $account->account_number : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="button" id="show_add_account" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium hover:bg-slate-50">
                        <i class="fa-solid fa-plus"></i> Create Receipt A/C
                    </button>
                </div>
            </div>

            <div id="add_account_box" class="hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-800 mb-3">Create Receipt Account</p>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <input type="text" id="new_account_name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Account holder name">
                    </div>
                    <div>
                        <input type="text" id="new_account_number" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="A/C number">
                    </div>
                    <div>
                        <input type="text" id="new_account_description" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Description (optional)">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="save_new_account" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                        <button type="button" id="cancel_add_account" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">
                            Cancel
                        </button>
                    </div>
                </div>
                <p id="account_message" class="text-sm mt-2 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Narration</label>
                <textarea name="narration" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="Enter narration">{{ old('narration') }}</textarea>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-white font-semibold hover:bg-emerald-700">
                <i class="fa-solid fa-floppy-disk"></i> Save Payment
            </button>
            <a href="{{ route('member-reports.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 text-slate-700 font-semibold hover:bg-slate-50">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    function summaryUrl(memberId) {
        return "{{ route('member-reports.summary', ['member' => '__MEMBER__']) }}".replace('__MEMBER__', memberId);
    }

    function searchMembersUrl(query) {
        return "{{ route('member-reports.yearly-payment.search-members') }}?q=" + encodeURIComponent(query);
    }

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function ensureMemberOption(member) {
        const value = String(member.id);
        let $option = $('#member_id option[value="' + value + '"]');

        if (!$option.length) {
            $option = $('<option>', { value: value });
            $('#member_id').append($option);
        }

        const memberName = member.name || '';
        const houseName = member.house_name || 'N/A';
        const place = member.place || '';
        const houseNo = member.house_no || '';
        const jamathHouseNo = member.jamath_house_no || '';
        const houseOwner = member.house_owner || '';
        const subscriptionType = member.subscription_type || '';
        const searchText = [
            memberName,
            houseName,
            houseNo,
            jamathHouseNo,
            houseOwner,
            subscriptionType,
            place
        ].join(' ').toLowerCase();

        $option
            .text(memberName + ' - ' + houseName)
            .data('place', place)
            .data('house-no', houseNo)
            .data('house-name', houseName)
            .data('member-name', memberName)
            .data('subscription-type', subscriptionType)
            .data('subscription-amount', member.subscription_amount || 0)
            .data('search-text', searchText)
            .attr({
                'data-place': place,
                'data-house-no': houseNo,
                'data-house-name': houseName,
                'data-member-name': memberName,
                'data-subscription-type': subscriptionType,
                'data-subscription-amount': member.subscription_amount || 0,
                'data-search-text': searchText
            });

        return $option;
    }

    function updateMemberDetails() {
        const selected = $('#member_id option:selected');
        const memberId = $('#member_id').val();
        const place = selected.data('place') || '';
        const houseNo = selected.data('house-no') || '';
        const memberName = selected.data('member-name') || '';
        const defaultAmount = parseFloat(selected.data('subscription-amount') || 0);

        $('#place_display').val(place);
        $('#house_no_display').val(houseNo);

        if (!$('#received_from').val()) {
            $('#received_from').val(memberName);
        }

        if (!$('#amount').val() && defaultAmount > 0) {
            $('#amount').val(defaultAmount.toFixed(2));
        }

        if (!memberId) {
            if ($('#include_previous_due').is(':checked')) {
                $('#due_amount').val('0.00');
            }
            return;
        }

        $.get(summaryUrl(memberId), function(summary) {
            const due = parseFloat(summary.balance || 0);
            if ($('#include_previous_due').is(':checked')) {
                $('#due_amount').val(due.toFixed(2));
            }
        });
    }

    function toggleDueAmount() {
        const enabled = $('#include_previous_due').is(':checked');
        $('#due_amount_wrap').toggleClass('hidden', !enabled);
        if (enabled) {
            updateMemberDetails();
        } else {
            $('#due_amount').val('');
        }
    }

    let memberSearchRequest = null;

    function searchAndDisplayMembers() {
        const query = ($('#member_search').val() || '').toLowerCase().trim();
        const $results = $('#search_results');

        if (!query) {
            $results.addClass('hidden');
            return;
        }

        if (memberSearchRequest) {
            memberSearchRequest.abort();
        }

        $results.html(`
            <div class="px-4 py-3 text-slate-500 text-center">
                <i class="fa-solid fa-spinner fa-spin"></i> Searching...
            </div>
        `).removeClass('hidden');

        memberSearchRequest = $.get(searchMembersUrl(query), function(members) {
            let matchedHtml = '';

            members.forEach(function(member) {
                ensureMemberOption(member);

                const memberName = escapeHtml(member.name);
                const houseName = escapeHtml(member.house_name || 'N/A');
                const houseNo = escapeHtml(member.house_no || '');
                const jamathHouseNo = escapeHtml(member.jamath_house_no || '');
                const houseOwner = escapeHtml(member.house_owner || '');
                const subscriptionType = escapeHtml(member.subscription_type || '');
                const place = escapeHtml(member.place || '');

                matchedHtml += `
                    <div class="px-4 py-3 hover:bg-emerald-50 cursor-pointer search-result-item border-b border-slate-100 last:border-b-0 transition" data-value="${member.id}">
                        <div class="font-semibold text-slate-900">${memberName}</div>
                        <div class="text-xs text-slate-500 mt-1">
                            <span class="inline-block mr-3"><i class="fa-solid fa-house"></i> ${houseName}</span>
                            ${houseNo ? `<span class="inline-block mr-3">House No: ${houseNo}</span>` : ''}
                            ${jamathHouseNo ? `<span class="inline-block mr-3">Jamath No: ${jamathHouseNo}</span>` : ''}
                            ${houseOwner ? `<span class="inline-block mr-3">Owner: ${houseOwner}</span>` : ''}
                            ${subscriptionType ? `<span class="inline-block mr-3">Type: ${subscriptionType}</span>` : ''}
                            ${place ? `<span class="inline-block"><i class="fa-solid fa-map-pin"></i> ${place}</span>` : ''}
                        </div>
                    </div>
                `;
            });

            if (members.length > 0) {
                $results.html(matchedHtml).removeClass('hidden');
            } else {
                $results.html(`
                    <div class="px-4 py-3 text-slate-500 text-center">
                        <i class="fa-solid fa-magnifying-glass text-lg mb-2 block"></i>
                        No subscription members found matching "${escapeHtml(query)}"
                    </div>
                `).removeClass('hidden');
            }
        }).fail(function(xhr) {
            if (xhr.statusText === 'abort') {
                return;
            }

            $results.html(`
                <div class="px-4 py-3 text-red-600 text-center">
                    Unable to search members. Please try again.
                </div>
            `).removeClass('hidden');
        });
    }

    // Member search with real-time server lookup
    $('#member_search').on('input', searchAndDisplayMembers);

    // Click on search result to select
    $(document).on('click', '.search-result-item', function() {
        const value = $(this).data('value');
        $('#member_id').val(value);
        $('#member_search').val('');
        $('#search_results').addClass('hidden');
        updateMemberDetails();
        $('#member_id').focus();
    });

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#member_search, #search_results').length) {
            $('#search_results').addClass('hidden');
        }
    });

    // Open search results when clicking on search input
    $('#member_search').on('focus', function() {
        if ($(this).val().trim() !== '') {
            searchAndDisplayMembers();
        }
    });

    // Member select change
    $('#member_id').on('change', updateMemberDetails);

    // Include previous due toggle
    $('#include_previous_due').on('change', toggleDueAmount);

    // Show/hide receipt account creation form
    $('#show_add_account').on('click', function() {
        $('#add_account_box').removeClass('hidden');
        $('#account_message').addClass('hidden').text('');
        $('#new_account_name').focus();
    });

    $('#cancel_add_account').on('click', function() {
        $('#add_account_box').addClass('hidden');
        $('#new_account_name').val('');
        $('#new_account_number').val('');
        $('#new_account_description').val('');
    });

    // Save new receipt account
    $('#save_new_account').on('click', function() {
        const name = $('#new_account_name').val().trim();
        const accountNumber = $('#new_account_number').val().trim();
        const description = $('#new_account_description').val().trim();
        const $message = $('#account_message');
        $message.removeClass('hidden text-green-700 text-red-700').text('');

        if (!name) {
            $message.addClass('text-red-700').text('Account name is required.');
            return;
        }

        $.ajax({
            url: "{{ route('member-reports.receipt-accounts.store') }}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                name: name,
                account_number: accountNumber,
                description: description
            },
            success: function(response) {
                if (response && response.success && response.account) {
                    const accountText = response.account.account_number
                        ? response.account.name + ' - A/C ' + response.account.account_number
                        : response.account.name;

                    $('#receipt_account_id').append(
                        $('<option>', {
                            value: response.account.id,
                            text: accountText,
                            selected: true
                        })
                    );
                    $('#new_account_name').val('');
                    $('#new_account_number').val('');
                    $('#new_account_description').val('');
                    $message.addClass('text-green-700').text('Receipt account created and selected.');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Unable to create receipt account.';
                $message.addClass('text-red-700').text(msg);
            }
        });
    });

    // Initialize
    updateMemberDetails();
    toggleDueAmount();
});
</script>
</body>
</html>
