<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Member List</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('partials.sweet-alert')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800">
<div class="max-w-7xl mx-auto px-4 py-6 lg:py-8">
    <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900">Member List</h1>
            <p class="text-sm text-slate-500 mt-1">Select a house to view members, edit details, or change the house owner</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 text-white px-4 py-2 text-sm font-medium hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i> Add Member
            </a>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div id="pageAlert" class="hidden mb-6 rounded-xl border p-4 font-semibold"></div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <aside class="xl:col-span-4 space-y-4">
            <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5">
                <form method="GET" action="{{ route('members.list') }}" class="flex gap-2">
                    @if($selectedHouse)
                        <input type="hidden" name="house_id" value="{{ $selectedHouse->id }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="Search houses">
                    <button class="rounded-xl bg-slate-900 text-white px-4" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </section>

            <section class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Houses</h2>
                    <span class="text-xs rounded-full bg-slate-100 px-2.5 py-1 text-slate-600">{{ $houses->total() }} total</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($houses as $house)
                        @php($activeHouse = $selectedHouse && $selectedHouse->id === $house->id)
                        <a href="{{ route('members.list', array_filter(['house_id' => $house->id, 'search' => request('search')])) }}"
                           class="block px-5 py-4 hover:bg-slate-50 {{ $activeHouse ? 'bg-emerald-50' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $house->house_name }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $house->mahallu->name ?? 'N/A' }} | House No: {{ $house->house_no }}</p>
                                    <p class="text-xs text-slate-500">House Owner: {{ $house->house_owner ?: 'N/A' }}</p>
                                </div>
                                <span class="text-xs rounded-full bg-white border border-slate-200 px-2.5 py-1 text-slate-600">{{ $house->members_count }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-slate-500">No houses found.</div>
                    @endforelse
                </div>
                @if($houses->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200">
                        {{ $houses->links() }}
                    </div>
                @endif
            </section>
        </aside>

        <main class="xl:col-span-8">
            @if(!$selectedHouse)
                <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-10 text-center text-slate-500">
                    <i class="fa-solid fa-house-user text-4xl mb-3 text-slate-300"></i>
                    <p class="font-semibold text-slate-700">Select a house to view members.</p>
                </section>
            @else
                <section class="rounded-2xl bg-white shadow-sm border border-slate-200 p-5 lg:p-6 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase font-bold tracking-wide text-emerald-600">{{ $selectedHouse->mahallu->name ?? 'N/A' }}</p>
                            <h2 class="text-2xl font-bold text-slate-900 mt-1">{{ $selectedHouse->house_name }}</h2>
                            <p class="text-sm text-slate-500 mt-1">Current house owner: <span class="font-semibold text-slate-800">{{ $selectedHouse->house_owner ?: 'N/A' }}</span></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                                <p class="text-xs text-slate-500">SL No</p>
                                <p class="font-bold">{{ $selectedHouse->sl_number }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                                <p class="text-xs text-slate-500">Members</p>
                                <p class="font-bold">{{ $selectedHouse->members->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-5 text-sm">
                        <div><span class="text-slate-500">House No:</span> <span class="font-semibold">{{ $selectedHouse->house_no }}</span></div>
                        <div><span class="text-slate-500">Jamath No:</span> <span class="font-semibold">{{ $selectedHouse->jamath_house_no }}</span></div>
                        <div><span class="text-slate-500">Type:</span> <span class="font-semibold">{{ $selectedHouse->houseType->name ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-500">Mobile:</span> <span class="font-semibold">{{ $selectedHouse->mobile }}</span></div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900">Members Under This House</h2>
                        <a href="{{ route('members.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Add member</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left">SL No</th>
                                <th class="px-4 py-3 text-left">Member</th>
                                <th class="px-4 py-3 text-left">Contact</th>
                                <th class="px-4 py-3 text-left">Profile</th>
                                <th class="px-4 py-3 text-left">Work / Education</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($selectedHouse->members as $member)
                                @php($isOwner = (int) $member->relation_id === (int) $ownerRelation->id)
                                @php($memberPayload = [
                                    "name" => $member->name,
                                    "adhar_number" => $member->adhar_number,
                                    "owner_image_url" => $member->owner_image ? asset('storage/' . $member->owner_image) : null,
                                    "father_name" => $member->father_name,
                                    "mother_name" => $member->mother_name,
                                    "marital_status" => $member->marital_status,
                                    "spouse_name" => $member->spouse_name,
                                    "relation_id" => $member->relation_id,
                                    "dob" => optional($member->dob)->format("Y-m-d"),
                                    "age" => $member->age,
                                    "gender" => $member->gender,
                                    "blood_group" => $member->blood_group,
                                    "mobile_number" => $member->mobile_number,
                                    "whatsapp_number" => $member->whatsapp_number,
                                    "islamic_qualification_id" => $member->islamic_qualification_id,
                                    "qualification_id" => $member->qualification_id,
                                    "occupation_id" => $member->occupation_id,
                                    "job_location_id" => $member->job_location_id,
                                    "subscription" => (bool) $member->subscription,
                                    "default_subscription" => (bool) $member->default_subscription,
                                    "subscription_amount" => $member->subscription_amount,
                                    "subscription_type" => $member->subscription_type,
                                    "narration" => $member->narration,
                                    "op_amount" => $member->op_amount,
                                    "active" => (bool) $member->active,
                                ])
                                <tr class="border-t border-slate-100 {{ $isOwner ? 'bg-amber-50' : '' }}">
                                    <td class="px-4 py-4 font-mono font-semibold text-emerald-600">{{ $member->sl_number ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-start gap-3">
                                            <div class="h-12 w-12 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 flex items-center justify-center shrink-0">
                                                @if($member->owner_image)
                                                    <img src="{{ asset('storage/' . $member->owner_image) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <i class="fa-solid fa-user text-slate-400"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $member->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $member->relation->name ?? 'No relation' }}</p>
                                                @if($isOwner)
                                                    <span class="mt-1 inline-flex rounded-full bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 font-bold">HOUSE OWNER</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        <p>{{ $member->mobile_number ?: '-' }}</p>
                                        <p class="text-xs">WhatsApp: {{ $member->whatsapp_number ?: '-' }}</p>
                                        <p class="text-xs">Adhar: {{ $member->adhar_number ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        <p>{{ $member->gender ?: '-' }} @if($member->age) | {{ $member->age }} yrs @endif</p>
                                        <p class="text-xs">Blood: {{ $member->blood_group ?: '-' }}</p>
                                        <p class="text-xs">Marital: {{ $member->marital_status ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        <p>{{ $member->occupation->name ?? '-' }}</p>
                                        <p class="text-xs">{{ $member->qualification->name ?? '-' }}</p>
                                        <p class="text-xs">{{ $member->jobLocation->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                    class="edit-member-btn rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold hover:bg-slate-50"
                                                    data-update-url="{{ route('members.update', $member->id) }}"
                                                    data-member="{{ base64_encode(json_encode($memberPayload)) }}">
                                                Edit
                                            </button>
                                            @if(auth()->user()?->isAdmin() && ! $isOwner)
                                                <form method="POST" action="{{ route('members.changeOwner', ['house' => $selectedHouse->id, 'member' => $member->id]) }}" data-confirm="Make {{ $member->name }} the house owner?" data-confirm-button="Yes, make owner">
                                                    @csrf
                                                    <button type="submit" class="rounded-lg bg-amber-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-amber-700">Make House Owner</button>
                                                </form>
                                            @endif
                                            @if(auth()->user()?->isAdmin())
                                                <form method="POST" action="{{ route('members.destroy', $member->id) }}" data-confirm="Are you sure you want to delete {{ $member->name }}?" data-confirm-button="Yes, delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg bg-red-600 text-white px-3 py-1.5 text-xs font-semibold hover:bg-red-700">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-slate-500">No members under this house yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </main>
    </div>
</div>

<div id="editMemberModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-xl">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Edit Member</h3>
            <button type="button" id="closeEditModal" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">Close</button>
        </div>
        <form id="editMemberForm" class="p-5 space-y-6">
            @csrf
            @method('PUT')
            @if($selectedHouse)
                <section class="rounded-2xl bg-white border border-slate-200 p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4"><i class="fa-solid fa-house text-emerald-600 mr-2"></i>House Selection</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Mahallu
                            <input value="{{ $selectedHouse->mahallu->name ?? 'N/A' }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">House No
                            <input value="{{ $selectedHouse->house_no }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Jamath No
                            <input value="{{ $selectedHouse->jamath_house_no }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">House Name
                            <input value="{{ $selectedHouse->house_name }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">House Owner
                            <input value="{{ $selectedHouse->house_owner }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Mobile No
                            <input value="{{ $selectedHouse->mobile }}" readonly class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        </label>
                    </div>
                </section>
            @endif

            <section class="rounded-2xl bg-white border border-slate-200 p-5 space-y-5">
                <h2 class="text-lg font-semibold text-slate-900"><i class="fa-solid fa-user text-sky-600 mr-2"></i>Member Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="block text-sm font-medium">Name
                        <input name="name" id="edit_name" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Adhar Number
                        <input name="adhar_number" id="edit_adhar_number" maxlength="20" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <div class="block text-sm font-medium">
                        <span>House Owner Image</span>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="h-16 w-16 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 flex items-center justify-center">
                                <img id="edit_owner_image_preview" src="" alt="" class="hidden h-full w-full object-cover">
                                <i id="edit_owner_image_placeholder" class="fa-solid fa-user text-slate-400"></i>
                            </div>
                            <input type="file" name="owner_image" id="edit_owner_image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </div>
                    </div>
                    <label class="block text-sm font-medium">Father Name
                        <input name="father_name" id="edit_father_name" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Mother Name
                        <input name="mother_name" id="edit_mother_name" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Marital Status
                        <select name="marital_status" id="edit_marital_status" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium" id="edit_spouse_name_wrapper">Spouse Name
                        <input name="spouse_name" id="edit_spouse_name" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Relation
                        <select name="relation_id" id="edit_relation_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select relation</option>
                            @foreach($relations as $relation)
                                <option value="{{ $relation->id }}">{{ $relation->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Date of Birth
                        <input type="date" name="dob" id="edit_dob" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Age
                        <input type="number" name="age" id="edit_age" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        <span class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" id="edit_manual_age" class="rounded border-slate-300"> Enter manually
                        </span>
                    </label>
                    <label class="block text-sm font-medium">Gender
                        <select name="gender" id="edit_gender" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Blood Group
                        <select name="blood_group" id="edit_blood_group" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select blood group</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Mobile Number
                        <input name="mobile_number" id="edit_mobile_number" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Whatsapp No
                        <input name="whatsapp_number" id="edit_whatsapp_number" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="block text-sm font-medium">Islamic Qualification
                        <select name="islamic_qualification_id" id="edit_islamic_qualification_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select</option>
                            @foreach($islamicQualifications as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Qualification
                        <select name="qualification_id" id="edit_qualification_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select</option>
                            @foreach($qualifications as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Occupation
                        <select name="occupation_id" id="edit_occupation_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select</option>
                            @foreach($occupations as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Job Location
                        <select name="job_location_id" id="edit_job_location_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 bg-white">
                            <option value="">Select</option>
                            @foreach($jobLocations as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="rounded-xl border border-slate-200 p-4 bg-slate-50 space-y-4">
                    <label class="inline-flex items-center gap-2 text-sm font-medium">
                        <input type="checkbox" name="subscription" id="edit_subscription" value="1"> Member Subscription Enabled
                    </label>
                    <div id="edit_subscription_details" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center gap-2 text-sm font-medium">
                            <input type="checkbox" name="default_subscription" id="edit_default_subscription" value="1"> Default
                        </label>
                        <label class="block text-sm font-medium">Amount
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        </label>
                        <div>
                            <label class="block text-sm font-medium mb-2">Type</label>
                            <div class="flex items-center gap-4 pt-2">
                                <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="subscription_type" value="Monthly" id="edit_subscription_type_monthly"> Monthly</label>
                                <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="subscription_type" value="Yearly" id="edit_subscription_type_yearly"> Yearly</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="md:col-span-2 block text-sm font-medium">Narration
                        <textarea name="narration" id="edit_narration" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5"></textarea>
                    </label>
                    <label class="block text-sm font-medium">Opening Amount
                        <input type="number" step="0.01" name="op_amount" id="edit_op_amount" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </label>
                    <label class="flex items-end gap-2 text-sm font-medium">
                        <input type="checkbox" name="active" id="edit_active" value="1"> Active
                    </label>
                </div>
            </section>
            <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                <button type="button" id="cancelEditMember" class="rounded-lg border border-slate-300 px-4 py-2 font-semibold">Cancel</button>
                <button type="submit" class="rounded-lg bg-emerald-600 text-white px-5 py-2 font-semibold hover:bg-emerald-700">Update Member</button>
            </div>
        </form>
    </div>
</div>

<script>
$(function() {
    const modal = $('#editMemberModal');
    const form = $('#editMemberForm');

    function showAlert(message, ok = true) {
        appAlert(message, ok ? 'success' : 'error');
    }

    function closeModal() {
        modal.addClass('hidden').removeClass('flex');
    }

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

    function toggleEditManualAgeMode() {
        const isManual = $('#edit_manual_age').is(':checked');
        $('#edit_age').prop('readonly', !isManual).toggleClass('bg-slate-100', !isManual);
        if (!isManual) {
            $('#edit_age').val(calculateAgeFromDob($('#edit_dob').val()));
        }
    }

    function toggleEditSpouseField() {
        if ($('#edit_marital_status').val() === 'Married') {
            $('#edit_spouse_name_wrapper').show();
        } else {
            $('#edit_spouse_name_wrapper').hide();
            $('#edit_spouse_name').val('');
        }
    }

    function toggleEditSubscriptionDetails() {
        const enabled = $('#edit_subscription').is(':checked');
        if (enabled) {
            $('#edit_subscription_details').show();
        } else {
            $('#edit_subscription_details').hide();
            $('#edit_default_subscription').prop('checked', false);
            $('#edit_amount').val('');
            $('input[name="subscription_type"]').prop('checked', false);
        }
    }

    $('.edit-member-btn').on('click', function() {
        let data = {};
        try {
            data = JSON.parse(atob($(this).attr('data-member') || 'e30='));
        } catch (e) {
            showAlert('Failed to load member details for editing.', false);
            return;
        }

        form.attr('action', $(this).data('update-url'));

        $('#edit_name').val(data.name || '');
        $('#edit_adhar_number').val(data.adhar_number || '');
        $('#edit_owner_image').val('');
        if (data.owner_image_url) {
            $('#edit_owner_image_preview').attr('src', data.owner_image_url).removeClass('hidden');
            $('#edit_owner_image_placeholder').addClass('hidden');
        } else {
            $('#edit_owner_image_preview').attr('src', '').addClass('hidden');
            $('#edit_owner_image_placeholder').removeClass('hidden');
        }
        $('#edit_father_name').val(data.father_name || '');
        $('#edit_mother_name').val(data.mother_name || '');
        $('#edit_marital_status').val(data.marital_status || '');
        $('#edit_spouse_name').val(data.spouse_name || '');
        $('#edit_relation_id').val(data.relation_id || '');
        $('#edit_dob').val(data.dob || '');
        $('#edit_age').val(data.age || '');
        $('#edit_gender').val(data.gender || '');
        $('#edit_blood_group').val(data.blood_group || '');
        $('#edit_mobile_number').val(data.mobile_number || '');
        $('#edit_whatsapp_number').val(data.whatsapp_number || '');
        $('#edit_islamic_qualification_id').val(data.islamic_qualification_id || '');
        $('#edit_qualification_id').val(data.qualification_id || '');
        $('#edit_occupation_id').val(data.occupation_id || '');
        $('#edit_job_location_id').val(data.job_location_id || '');
        $('#edit_amount').val(data.subscription_amount || '');
        $('input[name="subscription_type"]').prop('checked', false);
        if (data.subscription_type === 'Monthly') {
            $('#edit_subscription_type_monthly').prop('checked', true);
        } else if (data.subscription_type === 'Yearly') {
            $('#edit_subscription_type_yearly').prop('checked', true);
        }
        $('#edit_narration').val(data.narration || '');
        $('#edit_op_amount').val(data.op_amount || '');
        $('#edit_subscription').prop('checked', !!data.subscription);
        $('#edit_default_subscription').prop('checked', !!data.default_subscription);
        $('#edit_active').prop('checked', !!data.active);
        $('#edit_manual_age').prop('checked', !!data.age);
        toggleEditManualAgeMode();
        toggleEditSpouseField();
        toggleEditSubscriptionDetails();

        modal.removeClass('hidden').addClass('flex');
    });

    $('#edit_manual_age').on('change', toggleEditManualAgeMode);
    $('#edit_dob').on('change', function() {
        if (!$('#edit_manual_age').is(':checked')) {
            $('#edit_age').val(calculateAgeFromDob($(this).val()));
        }
    });
    $('#edit_marital_status').on('change', toggleEditSpouseField);
    $('#edit_subscription').on('change', toggleEditSubscriptionDetails);
    $('#edit_owner_image').on('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            $('#edit_owner_image_preview').attr('src', e.target.result).removeClass('hidden');
            $('#edit_owner_image_placeholder').addClass('hidden');
        };
        reader.readAsDataURL(file);
    });

    $('#closeEditModal, #cancelEditMember').on('click', closeModal);
    modal.on('click', function(e) {
        if (e.target === this) closeModal();
    });

    form.on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(form[0]),
            processData: false,
            contentType: false,
            success: function(response) {
                closeModal();
                showAlert(response.message || 'Member updated successfully.');
                setTimeout(function() {
                    window.location.reload();
                }, 700);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON && xhr.responseJSON.errors;
                if (errors) {
                    showAlert(Object.values(errors).flat().join(' '), false);
                } else {
                    showAlert('Failed to update member.', false);
                }
            }
        });
    });
});
</script>
</body>
</html>
  
