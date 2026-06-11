
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Member Creation</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('partials.sweet-alert')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-7xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">Member Creation</h1>
            <p class="text-sm text-slate-500 mt-1">Create members with house-linked details</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
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

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <form id="memberForm" action="{{ route('members.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" id="house_id" name="house_id" value="{{ $selectedHouse->id ?? old('house_id') }}">

                <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h2 class="text-lg font-semibold text-slate-900"><i class="fa-solid fa-house text-emerald-600 mr-2"></i>House Selection</h2>
                        <button type="button" id="changeHouseBtn" class="{{ $selectedHouse ? '' : 'hidden' }} rounded-lg border border-red-300 text-red-600 px-3 py-1.5 text-xs font-semibold hover:bg-red-50">Clear Selection</button>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium mb-2">Search House</label>
                        <input id="houseSearch" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="Search by house name, house no, jamath no" autocomplete="off">
                        <div id="searchLoading" class="hidden absolute right-3 top-11 text-emerald-600"><i class="fa-solid fa-spinner fa-spin"></i></div>
                        <div id="houseSuggestions" class="hidden absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                            <div id="suggestionsContent" class="max-h-72 overflow-y-auto"></div>
                        </div>
                        <p id="noResults" class="hidden mt-2 text-sm text-amber-700">No houses found.</p>
                    </div>

                    <div id="houseDetailsSection" class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Mahallu</label>
                            <input id="place_name" type="text" readonly value="{{ $selectedHouse->place->name ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">House No</label>
                            <input id="house_no" type="text" readonly value="{{ $selectedHouse->house_no ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Jamath No</label>
                            <input id="jamath_house_no" type="text" readonly value="{{ $selectedHouse->jamath_house_no ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">House Name</label>
                            <input id="house_name" type="text" readonly value="{{ $selectedHouse->house_name ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">House Owner</label>
                            <input id="house_owner" type="text" readonly value="{{ $selectedHouse->house_owner ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Phone Number</label>
                            <input id="phone" type="text" readonly value="{{ $selectedHouse->phone ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Mobile No</label>
                            <input id="mobile" type="text" readonly value="{{ $selectedHouse->mobile ?? '' }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 space-y-5">
                    <h2 class="text-lg font-semibold text-slate-900"><i class="fa-solid fa-user text-sky-600 mr-2"></i>Member Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Name</label>
                            <input type="text" name="name" id="member_name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Adhar Number</label>
                            <input type="text" name="adhar_number" id="adhar_number" value="{{ old('adhar_number') }}" maxlength="20" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Father Name</label>
                            <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Mother Name</label>
                            <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Marital Status</label>
                            <select name="marital_status" id="marital_status" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                <option value="">Select status</option>
                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            </select>
                        </div>
                        <div id="spouse_name_wrapper">
                            <label class="block text-sm font-medium mb-1">Spouse Name</label>
                            <input type="text" name="spouse_name" id="spouse_name" value="{{ old('spouse_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Relation</label>
                            <div class="flex gap-2">
                                <select id="relation_id" name="relation_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                    <option value="" selected disabled>Select relation</option>
                                    @foreach($relations as $relation)
                                        <option value="{{ $relation->id }}" {{ old('relation_id') == $relation->id ? 'selected' : '' }}>{{ $relation->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="addRelationModal" class="rounded-lg border border-slate-300 px-3 hover:bg-slate-50"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Age</label>
                            <input type="number" id="age" name="age" min="0" value="{{ old('age') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                            <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" id="manual_age" class="rounded border-slate-300" {{ old('age') ? 'checked' : '' }}> Enter manually
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Gender</label>
                            <select id="gender" name="gender" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                <option value="" selected disabled>Select gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Blood Group</label>
                            <select id="blood_group" name="blood_group" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                <option value="" selected disabled>Select blood group</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                                    <option value="{{ $group }}" {{ old('blood_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Mobile Number</label>
                            <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Whatsapp No</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Islamic Qualification</label>
                            <div class="flex gap-2">
                                <select id="islamic_qualification_id" name="islamic_qualification_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($islamicQualifications as $item)
                                        <option value="{{ $item->id }}" {{ old('islamic_qualification_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="addIslamicQualificationModal" class="rounded-lg border border-slate-300 px-3 hover:bg-slate-50"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Qualification</label>
                            <div class="flex gap-2">
                                <select id="qualification_id" name="qualification_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($qualifications as $item)
                                        <option value="{{ $item->id }}" {{ old('qualification_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="addQualificationModal" class="rounded-lg border border-slate-300 px-3 hover:bg-slate-50"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Occupation</label>
                            <div class="flex gap-2">
                                <select id="occupation_id" name="occupation_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($occupations as $item)
                                        <option value="{{ $item->id }}" {{ old('occupation_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="addOccupationModal" class="rounded-lg border border-slate-300 px-3 hover:bg-slate-50"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Job Location</label>
                            <div class="flex gap-2">
                                <select id="job_location_id" name="job_location_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                                    <option value="" selected disabled>Select</option>
                                    @foreach($jobLocations as $item)
                                        <option value="{{ $item->id }}" {{ old('job_location_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="addJobLocationModal" class="rounded-lg border border-slate-300 px-3 hover:bg-slate-50"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-4">
                        <label class="inline-flex items-center gap-2 text-sm font-medium">
                            <input class="rounded border-slate-300" type="checkbox" id="subscription" name="subscription" value="1" {{ old('subscription') ? 'checked' : '' }}>
                            Member Subscription Enabled
                        </label>

                        <div id="subscriptionDetails" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-2">
                                <input class="rounded border-slate-300" type="checkbox" id="default_subscription" name="default_subscription" value="1" {{ old('default_subscription') ? 'checked' : '' }}>
                                <label for="default_subscription" class="text-sm font-medium">Default</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Amount</label>
                                <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount', old('subscription_amount')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Type</label>
                                <div class="flex items-center gap-4 pt-2">
                                    <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="subscription_type" value="Monthly" {{ old('subscription_type') == 'Monthly' ? 'checked' : '' }}> Monthly</label>
                                    <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="subscription_type" value="Yearly" {{ old('subscription_type') == 'Yearly' ? 'checked' : '' }}> Yearly</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Narration</label>
                            <textarea id="narration" name="narration" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ old('narration') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Opening Amount</label>
                            <input type="number" step="0.01" id="op_amount" name="op_amount" value="{{ old('op_amount') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm font-medium">
                                <input type="checkbox" id="active" name="active" value="1" class="rounded border-slate-300" {{ old('active', 1) ? 'checked' : '' }}>
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <input type="hidden" name="date" value="{{ old('date', date('Y-m-d')) }}">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-white font-semibold hover:bg-emerald-700">
                            <i class="fa-solid fa-floppy-disk"></i> Save Member
                        </button>
                    </div>
                </section>
            </form>
        </div>

        <div>
            <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 sticky top-5">
                <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-users mr-2 text-indigo-600"></i>House Members</h2>
                <div id="memberList" class="space-y-3 text-sm text-slate-600">
                    <p class="text-slate-500">Select a house to view members.</p>
                </div>
            </section>
        </div>
    </div>
</div>
<div id="addRelationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5">
        <h3 class="text-lg font-semibold mb-3">Add Relation</h3>
        <input id="newRelationName" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2.5" placeholder="Enter relation name">
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" data-close-modal="addRelationModal" class="rounded-lg border border-slate-300 px-4 py-2">Cancel</button>
            <button type="button" id="saveRelationBtn" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Add</button>
        </div>
    </div>
</div>

<div id="addQualificationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5">
        <h3 class="text-lg font-semibold mb-3">Add Qualification</h3>
        <input id="newQualificationName" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2.5" placeholder="Enter qualification name">
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" data-close-modal="addQualificationModal" class="rounded-lg border border-slate-300 px-4 py-2">Cancel</button>
            <button type="button" id="saveQualificationBtn" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Add</button>
        </div>
    </div>
</div>

<div id="addIslamicQualificationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5">
        <h3 class="text-lg font-semibold mb-3">Add Islamic Qualification</h3>
        <input id="newIslamicQualificationName" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2.5" placeholder="Enter islamic qualification name">
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" data-close-modal="addIslamicQualificationModal" class="rounded-lg border border-slate-300 px-4 py-2">Cancel</button>
            <button type="button" id="saveIslamicQualificationBtn" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Add</button>
        </div>
    </div>
</div>

<div id="addOccupationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5">
        <h3 class="text-lg font-semibold mb-3">Add Occupation</h3>
        <input id="newOccupationName" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2.5" placeholder="Enter occupation name">
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" data-close-modal="addOccupationModal" class="rounded-lg border border-slate-300 px-4 py-2">Cancel</button>
            <button type="button" id="saveOccupationBtn" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Add</button>
        </div>
    </div>
</div>

<div id="addJobLocationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5">
        <h3 class="text-lg font-semibold mb-3">Add Job Location</h3>
        <input id="newJobLocationName" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2.5" placeholder="Enter job location name">
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" data-close-modal="addJobLocationModal" class="rounded-lg border border-slate-300 px-4 py-2">Cancel</button>
            <button type="button" id="saveJobLocationBtn" class="rounded-lg bg-emerald-600 text-white px-4 py-2">Add</button>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    let searchTimeout;

    function calculateAgeFromDob(dobValue) {
        if (!dobValue) return '';
        const dobDate = new Date(dobValue);
        if (Number.isNaN(dobDate.getTime())) return '';
        const today = new Date();
        let age = today.getFullYear() - dobDate.getFullYear();
        const monthDiff = today.getMonth() - dobDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) age--;
        return age >= 0 ? age : '';
    }

    function toggleManualAgeMode() {
        const isManual = $('#manual_age').is(':checked');
        $('#age').prop('readonly', !isManual).toggleClass('bg-slate-100', !isManual);
        if (!isManual) {
            $('#age').val(calculateAgeFromDob($('#dob').val()));
        }
    }

    function toggleSubscriptionDetails() {
        const enabled = $('#subscription').is(':checked');
        if (enabled) {
            $('#subscriptionDetails').show();
        } else {
            $('#subscriptionDetails').hide();
            $('#default_subscription').prop('checked', false);
            $('#amount').val('');
            $('input[name="subscription_type"]').prop('checked', false);
        }
    }

    function toggleSpouseField() {
        if ($('#marital_status').val() === 'Married') {
            $('#spouse_name_wrapper').show();
        } else {
            $('#spouse_name_wrapper').hide();
            $('#spouse_name').val('');
        }
    }

    function openModal(modalId) {
        $('#' + modalId).removeClass('hidden').addClass('flex');
    }

    function closeModal(modalId) {
        $('#' + modalId).addClass('hidden').removeClass('flex');
    }

    $(document).on('click', '[data-modal-target]', function() {
        openModal($(this).data('modal-target'));
    });

    $(document).on('click', '[data-close-modal]', function() {
        closeModal($(this).data('close-modal'));
    });

    $(document).on('click', '.fixed.inset-0', function(e) {
        if ($(e.target).is('.fixed.inset-0')) {
            $(this).addClass('hidden').removeClass('flex');
        }
    });

    $('#manual_age').on('change', toggleManualAgeMode);
    $('#dob').on('change', function() {
        if (!$('#manual_age').is(':checked')) {
            $('#age').val(calculateAgeFromDob($(this).val()));
        }
    });
    $('#subscription').on('change', toggleSubscriptionDetails);
    $('#marital_status').on('change', toggleSpouseField);

    toggleManualAgeMode();
    toggleSubscriptionDetails();
    toggleSpouseField();

    $(document).on('input', '#houseSearch', function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);
        $('#noResults').addClass('hidden');
        $('#suggestionsContent').html('');

        if (query.length < 2) {
            $('#houseSuggestions').addClass('hidden');
            $('#searchLoading').addClass('hidden');
            return;
        }

        $('#searchLoading').removeClass('hidden');

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: "{{ route('members.searchHouses') }}",
                type: 'GET',
                data: { q: query },
                success: function(data) {
                    $('#searchLoading').addClass('hidden');

                    if (data.length === 0) {
                        $('#noResults').removeClass('hidden');
                        $('#houseSuggestions').addClass('hidden');
                        return;
                    }

                    const suggestions = $('#suggestionsContent');
                    suggestions.html('');

                    data.forEach((house) => {
                        const placeText = house.place ? house.place.name : 'N/A';
                        suggestions.append(`
                            <button type="button" class="house-option w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100" data-id="${house.id}">
                                <p class="font-semibold text-slate-900">${house.house_name || 'N/A'}</p>
                                <p class="text-xs text-slate-500 mt-1">House No: ${house.house_no || 'N/A'} | Jamath No: ${house.jamath_house_no || 'N/A'}</p>
                                <p class="text-xs text-slate-500">Mahallu: ${placeText} | House Owner: ${house.house_owner || 'N/A'}</p>
                            </button>
                        `);
                    });

                    $('#houseSuggestions').removeClass('hidden');
                },
                error: function() {
                    $('#searchLoading').addClass('hidden');
                    $('#noResults').text('Error loading houses.').removeClass('hidden');
                }
            });
        }, 300);
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#houseSearch, #houseSuggestions').length) {
            $('#houseSuggestions').addClass('hidden');
        }
    });

    $(document).on('click', '.house-option', function() {
        const houseId = $(this).data('id');
        $('#searchLoading').removeClass('hidden');

        $.ajax({
            url: "{{ route('members.getHouseDetails', ':id') }}".replace(':id', houseId),
            type: 'GET',
            success: function(house) {
                $('#searchLoading').addClass('hidden');
                $('#house_id').val(house.id);

                $('#place_name').val(house.place ? house.place.name : 'N/A');
                $('#house_no').val(house.house_no || 'N/A');
                $('#jamath_house_no').val(house.jamath_house_no || 'N/A');
                $('#house_name').val(house.house_name || 'N/A');
                $('#house_owner').val(house.house_owner || 'N/A');
                $('#phone').val(house.phone || 'N/A');
                $('#mobile').val(house.mobile || 'N/A');

                $('#houseSuggestions').addClass('hidden');
                $('#houseSearch').val('');
                $('#changeHouseBtn').removeClass('hidden');
                loadMembers(house.id);
            },
            error: function() {
                $('#searchLoading').addClass('hidden');
                appAlert('Failed to load house details.', 'error');
            }
        });
    });

    function loadMembers(houseId) {
        $.ajax({
            url: "{{ route('members.getHouseMembers', ':id') }}".replace(':id', houseId),
            type: 'GET',
            success: function(members) {
                const memberList = $('#memberList');
                if (!members.length) {
                    memberList.html('<p class="text-slate-500">No members yet.</p>');
                    return;
                }

                let rows = '';
                members.forEach((member, index) => {
                    const memberName = member.name || 'N/A';
                    const slNumber = member.sl_number || 'N/A';
                    const relationName = (member.relation && member.relation.name) ? member.relation.name : 'N/A';
                    const isOwner = relationName === 'House Owner';
                    const ownerBadge = isOwner
                        ? '<span class="ml-2 inline-flex items-center rounded-full bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 font-semibold">HOUSE OWNER</span>'
                        : '';

                    rows += `
                        <tr class="${isOwner ? 'bg-amber-50' : ''}">
                            <td class="px-3 py-2 border-b border-slate-100 text-center font-medium">${slNumber}</td>
                            <td class="px-3 py-2 border-b border-slate-100 font-semibold text-slate-900">${memberName}${ownerBadge}</td>
                            <td class="px-3 py-2 border-b border-slate-100">${relationName}</td>
                        </tr>
                    `;
                });

                memberList.html(`
                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-3 py-2 text-center w-20">SL No</th>
                                    <th class="px-3 py-2 text-left">Member Name</th>
                                    <th class="px-3 py-2 text-left">Relation</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `);
            }
        });
    }

    if ($('#house_id').val()) {
        loadMembers($('#house_id').val());
    }

    $('#changeHouseBtn').on('click', function() {
        // Redirect to the base index route to clear URL parameters and reset the house selection
        window.location.href = "{{ route('members.index') }}";
    });

    function saveLookup(url, inputSelector, selectSelector, modalId) {
        const name = $(inputSelector).val().trim();
        if (!name) {
            appAlert('Please enter a name');
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: $('[name="_token"]').val(),
                name: name
            },
            success: function(data) {
                $(selectSelector).append(`<option value="${data.id}" selected>${data.name}</option>`);
                $(inputSelector).val('');
                closeModal(modalId);
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed';
                appAlert('Error: ' + message, 'error');
            }
        });
    }

    $('#saveRelationBtn').on('click', function() {
        saveLookup("{{ route('members.createRelation') }}", '#newRelationName', '#relation_id', 'addRelationModal');
    });

    $('#saveQualificationBtn').on('click', function() {
        saveLookup("{{ route('members.createQualification') }}", '#newQualificationName', '#qualification_id', 'addQualificationModal');
    });

    $('#saveIslamicQualificationBtn').on('click', function() {
        saveLookup("{{ route('members.createIslamicQualification') }}", '#newIslamicQualificationName', '#islamic_qualification_id', 'addIslamicQualificationModal');
    });

    $('#saveOccupationBtn').on('click', function() {
        saveLookup("{{ route('members.createOccupation') }}", '#newOccupationName', '#occupation_id', 'addOccupationModal');
    });

    $('#saveJobLocationBtn').on('click', function() {
        saveLookup("{{ route('members.createJobLocation') }}", '#newJobLocationName', '#job_location_id', 'addJobLocationModal');
    });

    $('#memberForm').on('submit', function(e) {
        if (!$('#house_id').val()) {
            e.preventDefault();
            appAlert('Please select a house first.');
            $('#houseSearch').focus();
            return false;
        }
    });
});
</script>
</body>
</html>
