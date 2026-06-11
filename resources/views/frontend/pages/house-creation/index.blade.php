<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Management | House Creations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { background-color: #f3f4f7; font-family: 'Inter', sans-serif; color: #334155; }
        .main-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 15px; }

        .card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); margin-bottom: 30px; }
        .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 20px 24px; border-radius: 16px 16px 0 0 !important; }

        .form-section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6366f1; margin-bottom: 20px; display: flex; align-items: center; }
        .form-label { font-weight: 500; font-size: 0.875rem; color: #475569; }
        .required-field::after { content: " *"; color: #ef4444; }
        .section-divider { height: 1px; background: #f1f5f9; margin: 25px 0; }

        .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; color: #64748b; padding: 15px; border-top: none; }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

        .action-group { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-action { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: white; transition: all 0.2s; color: #64748b; text-decoration: none; }
        .btn-action:hover { background: #f1f5f9; color: #6366f1; border-color: #6366f1; }
        .btn-delete:hover { color: #ef4444; border-color: #fecaca; background: #fef2f2; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-house-add text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold" id="formTitle">House Creation</h4>
                    <small class="text-muted" id="formSubtitle">Fill in the details to register a new property</small>
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary d-none" id="cancelEditBtn">
                <i class="bi bi-x-lg me-1"></i> Cancel Edit
            </button>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <strong>Error!</strong> Please correct the following:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" id="houseForm" action="{{ route('house-creations.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="house_id" id="house_id" value="">

                <div class="form-section-title"><i class="bi bi-info-circle me-2"></i> Basic Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required-field">Registration Date</label>
                        <input type="date" class="form-control" id="registration_date" name="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Mahallu</label>
                        <div class="input-group">
                            <select class="form-select" name="place_id" id="placeSelect" required>
                                <option value="" selected disabled>Select Mahallu</option>
                                @foreach($mahallus as $mahallu)
                                    <option value="{{ $mahallu->id }}" {{ old('place_id') == $mahallu->id ? 'selected' : '' }}>{{ $mahallu->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#placeModal"><i class="bi bi-plus-lg"></i></button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.lookups.index', 'places') }}" title="Manage Mahallus">
                                <i class="bi bi-gear"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="form-section-title"><i class="bi bi-building me-2"></i> Property Details</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label required-field">House Owner</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="house_owner" name="house_owner" value="{{ old('house_owner') }}" required>
                            <button class="btn btn-outline-primary" type="button" id="openOwnerModalBtn" title="Enter owner details">
                                <i class="bi bi-person-vcard"></i>
                            </button>
                        </div>
                        <small class="text-muted">Use the icon to enter full house owner member details</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">House Name</label>
                        <input type="text" class="form-control" id="house_name" name="house_name" value="{{ old('house_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Jama-ath House No</label>
                        <input type="text" class="form-control" id="jamath_house_no" name="jamath_house_no" value="{{ old('jamath_house_no') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required-field">House Type</label>
                        <div class="input-group">
                            <select class="form-select" name="house_type_id" id="houseTypeSelect" required>
                                <option value="" selected disabled>Select Type</option>
                                @foreach($houseTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('house_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#typeModal"><i class="bi bi-plus-lg"></i></button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.lookups.index', 'house-types') }}" title="Manage House Types">
                                <i class="bi bi-gear"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label required-field">No of Floors</label>
                        <input type="number" class="form-control" id="floors" name="floors" value="{{ old('floors', 1) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ward No</label>
                        <input type="text" class="form-control" id="ward_no" name="ward_no" value="{{ old('ward_no') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">House No</label>
                        <input type="text" class="form-control" id="house_no" name="house_no" value="{{ old('house_no') }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="form-section-title"><i class="bi bi-wallet2 me-2"></i> Billing & Status</div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Phone No</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Mobile No</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reg Fee (₹)</label>
                        <input type="number" class="form-control" id="reg_fee" name="reg_fee" value="{{ old('reg_fee') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="houseSubCheck" name="house_sub" value="1" {{ old('house_sub') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="houseSubCheck">Subscription</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="activeCheck" {{ old('active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="activeCheck">Active</label>
                        </div>
                    </div>

                    <div class="col-md-4" id="defaultAmountWrapper">
                        <label class="form-label">Default Monthly Amount</label>
                        <input type="number" class="form-control" id="defaultAmountInput" name="default_amount" value="{{ old('default_amount') }}">
                    </div>
                    <div class="col-md-4" id="dueAmountWrapper">
                        <label class="form-label">Opening Due Amount</label>
                        <input type="number" class="form-control" id="dueAmountInput" name="due_amount" value="{{ old('due_amount') }}">
                    </div>
                </div>

                <div class="mt-5 d-flex justify-content-between gap-3">
                    <input type="hidden" name="owner_member_name" id="owner_member_name" value="{{ old('owner_member_name') }}">
                    <input type="hidden" name="owner_member_adhar_number" id="owner_member_adhar_number" value="{{ old('owner_member_adhar_number') }}">
                    <input type="hidden" name="owner_member_date" id="owner_member_date" value="{{ old('owner_member_date') }}">
                    <input type="hidden" name="owner_member_father_name" id="owner_member_father_name" value="{{ old('owner_member_father_name') }}">
                    <input type="hidden" name="owner_member_mother_name" id="owner_member_mother_name" value="{{ old('owner_member_mother_name') }}">
                    <input type="hidden" name="owner_member_relation_id" id="owner_member_relation_id" value="{{ old('owner_member_relation_id') }}">
                    <input type="hidden" name="owner_member_dob" id="owner_member_dob" value="{{ old('owner_member_dob') }}">
                    <input type="hidden" name="owner_member_age" id="owner_member_age" value="{{ old('owner_member_age') }}">
                    <input type="hidden" name="owner_member_gender" id="owner_member_gender" value="{{ old('owner_member_gender') }}">
                    <input type="hidden" name="owner_member_blood_group" id="owner_member_blood_group" value="{{ old('owner_member_blood_group') }}">
                    <input type="hidden" name="owner_member_mobile" id="owner_member_mobile" value="{{ old('owner_member_mobile') }}">
                    <input type="hidden" name="owner_member_whatsapp" id="owner_member_whatsapp" value="{{ old('owner_member_whatsapp') }}">
                    <input type="hidden" name="owner_member_marital_status" id="owner_member_marital_status" value="{{ old('owner_member_marital_status', 'Single') }}">
                    <input type="hidden" name="owner_member_spouse_name" id="owner_member_spouse_name" value="{{ old('owner_member_spouse_name') }}">
                    <input type="hidden" name="owner_member_islamic_qualification_id" id="owner_member_islamic_qualification_id" value="{{ old('owner_member_islamic_qualification_id') }}">
                    <input type="hidden" name="owner_member_qualification_id" id="owner_member_qualification_id" value="{{ old('owner_member_qualification_id') }}">
                    <input type="hidden" name="owner_member_occupation_id" id="owner_member_occupation_id" value="{{ old('owner_member_occupation_id') }}">
                    <input type="hidden" name="owner_member_job_location_id" id="owner_member_job_location_id" value="{{ old('owner_member_job_location_id') }}">
                    <input type="hidden" name="owner_member_subscription" id="owner_member_subscription" value="{{ old('owner_member_subscription') }}">
                    <input type="hidden" name="owner_member_default_subscription" id="owner_member_default_subscription" value="{{ old('owner_member_default_subscription') }}">
                    <input type="hidden" name="owner_member_subscription_amount" id="owner_member_subscription_amount" value="{{ old('owner_member_subscription_amount') }}">
                    <input type="hidden" name="owner_member_subscription_type" id="owner_member_subscription_type" value="{{ old('owner_member_subscription_type') }}">
                    <input type="hidden" name="owner_member_narration" id="owner_member_narration" value="{{ old('owner_member_narration') }}">
                    <input type="hidden" name="owner_member_op_amount" id="owner_member_op_amount" value="{{ old('owner_member_op_amount') }}">
                    <input type="hidden" name="owner_member_active" id="owner_member_active" value="{{ old('owner_member_active', 1) }}">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 py-2">
                        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                    </a>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3" id="submitBtn">
                            <i class="bi bi-cloud-upload me-2"></i> Save Property
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" id="resetBtn">
                            Clear Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- House Inventory -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">House Inventory</h5>
            <span class="badge bg-light text-dark border">{{ $houses->total() }} Records</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Mahallu</th>
                        <th>Sl No</th>
                        <th>House No</th>
                        <th>House Name</th>
                        <th>House Owner</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($houses as $house)
                    <tr>
                        <td class="text-nowrap">
                            {{ \Carbon\Carbon::parse($house->registration_date)->format('d-m-Y') }}
                        </td>
                        <td>
                            <span class="text-primary fw-medium">
                                {{ $house->mahallu->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="fw-bold text-muted">{{ $house->sl_number }}</td>
                        <td>{{ $house->house_no }}</td>
                        <td>{{ $house->house_name }}</td>
                        <td class="fw-semibold">{{ $house->house_owner }}</td>
                        <td>
                            <div class="action-group">
                                <button type="button" class="btn-action edit-house-btn"
                                    data-id="{{ $house->id }}"
                                    data-registration_date="{{ $house->registration_date->format('Y-m-d') }}"
                                    data-place_id="{{ $house->place_id }}"
                                    data-house_owner="{{ addslashes($house->house_owner) }}"
                                    data-house_name="{{ addslashes($house->house_name) }}"
                                    data-jamath_house_no="{{ addslashes($house->jamath_house_no) }}"
                                    data-house_type_id="{{ $house->house_type_id }}"
                                    data-floors="{{ $house->floors }}"
                                    data-ward_no="{{ $house->ward_no }}"
                                    data-house_no="{{ $house->house_no }}"
                                    data-address="{{ addslashes($house->address ?? '') }}"
                                    data-phone="{{ $house->phone }}"
                                    data-mobile="{{ $house->mobile }}"
                                    data-reg_fee="{{ $house->reg_fee }}"
                                    data-house_sub="{{ $house->house_sub }}"
                                    data-default_amount="{{ $house->default_amount ?? '' }}"
                                    data-due_amount="{{ $house->due_amount ?? '' }}"
                                    data-active="{{ $house->active }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                @if(auth()->user()?->isAdmin())
                                    <form action="{{ route('house-creations.destroy', $house->id) }}" method="POST" data-confirm="Are you sure you want to delete this record?" data-confirm-button="Yes, delete" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No properties registered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($houses->hasPages())
            <div class="card-footer bg-white">
                {{ $houses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Mahallu Modal -->
<div class="modal fade" id="placeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">New Mahallu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" id="p_name" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea id="p_desc" class="form-control"></textarea></div>
                <button type="button" id="btnSavePlace" class="btn btn-primary w-100 py-2">Add Mahallu</button>
            </div>
        </div>
    </div>
</div>

<!-- House Type Modal -->
<div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">New House Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Type Name</label><input type="text" id="t_name" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea id="t_desc" class="form-control"></textarea></div>
                <button type="button" id="btnSaveType" class="btn btn-primary w-100 py-2">Add Type</button>
            </div>
        </div>
    </div>
</div>

<!-- Owner Member Details Modal -->
<div class="modal fade" id="ownerMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">House Owner Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" id="owner_date_modal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required-field">Name</label>
                        <input type="text" id="owner_name_modal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adhar Number</label>
                        <input type="text" id="owner_adhar_number_modal" class="form-control" maxlength="20">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">House Owner Image</label>
                        <input type="file" name="owner_member_image" id="owner_member_image_modal" form="houseForm" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Relation</label>
                        <div class="input-group">
                            <select id="owner_relation_modal" class="form-select">
                                <option value="">Select relation</option>
                                @foreach($relations as $relation)
                                    <option value="{{ $relation->id }}" {{ $relation->name === 'House Owner' ? 'selected' : '' }}>{{ $relation->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary owner-quick-add" type="button" data-kind="relation"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father Name</label>
                        <input type="text" id="owner_father_name_modal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mother Name</label>
                        <input type="text" id="owner_mother_name_modal" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">DOB</label>
                        <input type="date" id="owner_dob_modal" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" id="owner_age_modal" class="form-control">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="owner_manual_age_modal">
                            <label class="form-check-label" for="owner_manual_age_modal">Enter age manually</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select id="owner_gender_modal" class="form-select">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <select id="owner_blood_group_modal" class="form-select">
                            <option value="">Select</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" id="owner_mobile_modal" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" id="owner_whatsapp_modal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Marital Status</label>
                        <select id="owner_marital_status_modal" class="form-select">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="owner_spouse_wrapper_modal">
                        <label class="form-label">Spouse Name</label>
                        <input type="text" id="owner_spouse_name_modal" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Islamic Qualification</label>
                        <div class="input-group">
                            <select id="owner_islamic_qualification_modal" class="form-select">
                                <option value="">Select</option>
                                @foreach($islamicQualifications as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary owner-quick-add" type="button" data-kind="islamic_qualification"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Qualification</label>
                        <div class="input-group">
                            <select id="owner_qualification_modal" class="form-select">
                                <option value="">Select</option>
                                @foreach($qualifications as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary owner-quick-add" type="button" data-kind="qualification"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Occupation</label>
                        <div class="input-group">
                            <select id="owner_occupation_modal" class="form-select">
                                <option value="">Select</option>
                                @foreach($occupations as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary owner-quick-add" type="button" data-kind="occupation"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Location</label>
                        <div class="input-group">
                            <select id="owner_job_location_modal" class="form-select">
                                <option value="">Select</option>
                                @foreach($jobLocations as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary owner-quick-add" type="button" data-kind="job_location"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr class="my-1">
                    </div>
                    <div class="col-12">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="owner_subscription_modal">
                            <label class="form-check-label fw-bold" for="owner_subscription_modal">Member Subscription Enabled</label>
                        </div>
                    </div>
                    <div class="col-12" id="owner_subscription_details_modal" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="owner_default_subscription_modal">
                                    <label class="form-check-label fw-bold" for="owner_default_subscription_modal">Default</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" id="owner_subscription_amount_modal" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">Type</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="owner_subscription_type_modal" id="owner_subscription_type_monthly_modal" value="Monthly">
                                    <label class="form-check-label" for="owner_subscription_type_monthly_modal">Monthly</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="owner_subscription_type_modal" id="owner_subscription_type_yearly_modal" value="Yearly">
                                    <label class="form-check-label" for="owner_subscription_type_yearly_modal">Yearly</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Opening Amount</label>
                        <input type="number" step="0.01" id="owner_op_amount_modal" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Narration</label>
                        <textarea id="owner_narration_modal" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="owner_active_modal" checked>
                            <label class="form-check-label fw-bold" for="owner_active_modal">Active</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveOwnerMemberDetailsBtn" class="btn btn-primary">Save House Owner Details</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Owner Lookup Create Modals -->
<div class="modal fade" id="ownerRelationCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add Relation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="ownerNewRelationName" class="form-control mb-3" placeholder="Relation name">
                <button type="button" id="ownerSaveRelationBtn" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerIslamicQualificationCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add Islamic Qualification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="ownerNewIslamicQualificationName" class="form-control mb-3" placeholder="Islamic qualification name">
                <button type="button" id="ownerSaveIslamicQualificationBtn" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerQualificationCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add Qualification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="ownerNewQualificationName" class="form-control mb-3" placeholder="Qualification name">
                <button type="button" id="ownerSaveQualificationBtn" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerOccupationCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add Occupation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="ownerNewOccupationName" class="form-control mb-3" placeholder="Occupation name">
                <button type="button" id="ownerSaveOccupationBtn" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerJobLocationCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Add Job Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="ownerNewJobLocationName" class="form-control mb-3" placeholder="Job location name">
                <button type="button" id="ownerSaveJobLocationBtn" class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@include('partials.sweet-alert')

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Toggle subscription related fields
    const subCheck = document.getElementById('houseSubCheck');
    const wrap1 = document.getElementById('defaultAmountWrapper');
    const wrap2 = document.getElementById('dueAmountWrapper');

    function toggleSubscriptionFields() {
        const isChecked = subCheck.checked;
        wrap1.style.display = isChecked ? 'block' : 'none';
        wrap2.style.display = isChecked ? 'block' : 'none';
        document.getElementById('defaultAmountInput').disabled = !isChecked;
        document.getElementById('dueAmountInput').disabled = !isChecked;
    }

    subCheck.addEventListener('change', toggleSubscriptionFields);
    toggleSubscriptionFields();

    // Owner member modal logic
    const ownerModalEl = document.getElementById('ownerMemberModal');
    const ownerModal = new bootstrap.Modal(ownerModalEl);
    let restoreOwnerModalAfterChild = false;

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

    function syncOwnerModalFromHidden() {
        $('#owner_date_modal').val($('#owner_member_date').val() || $('#registration_date').val() || '');
        $('#owner_name_modal').val($('#owner_member_name').val() || $('#house_owner').val() || '');
        $('#owner_adhar_number_modal').val($('#owner_member_adhar_number').val() || '');
        $('#owner_relation_modal').val($('#owner_member_relation_id').val() || '');
        $('#owner_father_name_modal').val($('#owner_member_father_name').val() || '');
        $('#owner_mother_name_modal').val($('#owner_member_mother_name').val() || '');
        $('#owner_dob_modal').val($('#owner_member_dob').val() || '');
        $('#owner_age_modal').val($('#owner_member_age').val() || '');
        $('#owner_gender_modal').val($('#owner_member_gender').val() || '');
        $('#owner_blood_group_modal').val($('#owner_member_blood_group').val() || '');
        $('#owner_mobile_modal').val($('#owner_member_mobile').val() || $('#mobile').val() || '');
        $('#owner_whatsapp_modal').val($('#owner_member_whatsapp').val() || '');
        $('#owner_marital_status_modal').val($('#owner_member_marital_status').val() || 'Single');
        $('#owner_spouse_name_modal').val($('#owner_member_spouse_name').val() || '');
        $('#owner_islamic_qualification_modal').val($('#owner_member_islamic_qualification_id').val() || '');
        $('#owner_qualification_modal').val($('#owner_member_qualification_id').val() || '');
        $('#owner_occupation_modal').val($('#owner_member_occupation_id').val() || '');
        $('#owner_job_location_modal').val($('#owner_member_job_location_id').val() || '');
        $('#owner_subscription_modal').prop('checked', ($('#owner_member_subscription').val() == '1'));
        $('#owner_default_subscription_modal').prop('checked', ($('#owner_member_default_subscription').val() == '1'));
        $('#owner_subscription_amount_modal').val($('#owner_member_subscription_amount').val() || '');
        const subscriptionType = $('#owner_member_subscription_type').val() || '';
        $('input[name="owner_subscription_type_modal"]').prop('checked', false);
        if (subscriptionType === 'Monthly') {
            $('#owner_subscription_type_monthly_modal').prop('checked', true);
        } else if (subscriptionType === 'Yearly') {
            $('#owner_subscription_type_yearly_modal').prop('checked', true);
        }
        $('#owner_narration_modal').val($('#owner_member_narration').val() || '');
        $('#owner_op_amount_modal').val($('#owner_member_op_amount').val() || '');
        $('#owner_active_modal').prop('checked', $('#owner_member_active').val() !== '0');
        $('#owner_spouse_wrapper_modal').show();
        $('#owner_subscription_details_modal').toggle($('#owner_subscription_modal').is(':checked'));
        $('#owner_manual_age_modal').prop('checked', !!$('#owner_member_age').val());
        $('#owner_age_modal').prop('readonly', !$('#owner_manual_age_modal').is(':checked'));
    }

    $('#openOwnerModalBtn, #house_owner').on('click focus', function() {
        syncOwnerModalFromHidden();
        ownerModal.show();
    });

    // When opening small create modals from owner modal, remember to restore owner modal.
    $('#ownerMemberModal').on('click', '[data-bs-target^="#owner"][data-bs-target$="CreateModal"]', function() {
        restoreOwnerModalAfterChild = true;
    });

    // Restore owner modal after child modal closes (both Add and Cancel flows).
    $('#ownerRelationCreateModal, #ownerIslamicQualificationCreateModal, #ownerQualificationCreateModal, #ownerOccupationCreateModal, #ownerJobLocationCreateModal')
        .on('hidden.bs.modal', function() {
            if (restoreOwnerModalAfterChild) {
                setTimeout(function() {
                    ownerModal.show();
                    restoreOwnerModalAfterChild = false;
                }, 120);
            }
        });

    $('#owner_marital_status_modal').on('change', function() {
        // Keep spouse field visible for consistency with requested owner detail entry.
    });

    $('#owner_dob_modal').on('change', function() {
        if (!$('#owner_manual_age_modal').is(':checked')) {
            $('#owner_age_modal').val(calculateAgeFromDob($(this).val()));
        }
    });

    $('#owner_manual_age_modal').on('change', function() {
        const isManual = $(this).is(':checked');
        $('#owner_age_modal').prop('readonly', !isManual);
        if (!isManual) {
            $('#owner_age_modal').val(calculateAgeFromDob($('#owner_dob_modal').val()));
        }
    });

    $('#owner_subscription_modal').on('change', function() {
        const enabled = $(this).is(':checked');
        $('#owner_subscription_details_modal').toggle(enabled);
        if (!enabled) {
            $('#owner_default_subscription_modal').prop('checked', false);
            $('#owner_subscription_amount_modal').val('');
            $('input[name="owner_subscription_type_modal"]').prop('checked', false);
        }
    });

    $('#saveOwnerMemberDetailsBtn').on('click', function() {
        const ownerName = $('#owner_name_modal').val().trim();
        if (!ownerName) {
            appAlert('House owner name is required.');
            return;
        }

        $('#house_owner').val(ownerName);
        $('#owner_member_date').val($('#owner_date_modal').val() || $('#registration_date').val());
        $('#owner_member_name').val(ownerName);
        $('#owner_member_adhar_number').val($('#owner_adhar_number_modal').val().trim());
        $('#owner_member_relation_id').val($('#owner_relation_modal').val());
        $('#owner_member_father_name').val($('#owner_father_name_modal').val().trim());
        $('#owner_member_mother_name').val($('#owner_mother_name_modal').val().trim());
        $('#owner_member_dob').val($('#owner_dob_modal').val());
        $('#owner_member_age').val($('#owner_age_modal').val());
        $('#owner_member_gender').val($('#owner_gender_modal').val());
        $('#owner_member_blood_group').val($('#owner_blood_group_modal').val());
        $('#owner_member_mobile').val($('#owner_mobile_modal').val().trim() || $('#mobile').val().trim());
        $('#owner_member_whatsapp').val($('#owner_whatsapp_modal').val().trim());
        $('#owner_member_marital_status').val($('#owner_marital_status_modal').val());
        $('#owner_member_spouse_name').val($('#owner_spouse_name_modal').val().trim());
        $('#owner_member_islamic_qualification_id').val($('#owner_islamic_qualification_modal').val());
        $('#owner_member_qualification_id').val($('#owner_qualification_modal').val());
        $('#owner_member_occupation_id').val($('#owner_occupation_modal').val());
        $('#owner_member_job_location_id').val($('#owner_job_location_modal').val());
        $('#owner_member_subscription').val($('#owner_subscription_modal').is(':checked') ? '1' : '0');
        $('#owner_member_default_subscription').val($('#owner_default_subscription_modal').is(':checked') ? '1' : '0');
        $('#owner_member_subscription_amount').val($('#owner_subscription_amount_modal').val());
        $('#owner_member_subscription_type').val($('input[name="owner_subscription_type_modal"]:checked').val() || '');
        $('#owner_member_narration').val($('#owner_narration_modal').val().trim());
        $('#owner_member_op_amount').val($('#owner_op_amount_modal').val());
        $('#owner_member_active').val($('#owner_active_modal').is(':checked') ? '1' : '0');

        ownerModal.hide();
    });

    // Keep owner mobile in sync with house mobile unless explicitly set in modal.
    $('#mobile').on('input', function() {
        if (!$('#owner_member_mobile').val()) {
            $('#owner_member_mobile').val($(this).val().trim());
        }
    });

    // -----------------------------
    //        EDIT FUNCTIONALITY
    // -----------------------------
    $(document).on('click', '.edit-house-btn', function() {
        const data = $(this).data();

        // Fill form
        $('#house_id').val(data.id);
        $('#registration_date').val(data.registration_date);
        $('#placeSelect').val(data.place_id);
        $('#house_owner').val(data.house_owner);
        $('#house_name').val(data.house_name);
        $('#jamath_house_no').val(data.jamath_house_no);
        $('#houseTypeSelect').val(data.house_type_id);
        $('#floors').val(data.floors);
        $('#ward_no').val(data.ward_no);
        $('#house_no').val(data.house_no);
        $('#address').val(data.address);
        $('#phone').val(data.phone);
        $('#mobile').val(data.mobile);
        $('#reg_fee').val(data.reg_fee);

        $('#houseSubCheck').prop('checked', parseInt(data.house_sub) === 1);
        $('#activeCheck').prop('checked', parseInt(data.active) === 1);

        toggleSubscriptionFields();

        if (parseInt(data.house_sub) === 1) {
            $('#defaultAmountInput').val(data.default_amount);
            $('#dueAmountInput').val(data.due_amount);
        }

        // Change form to UPDATE
        $('#houseForm')
            .attr('action', "{{ route('house-creations.update', ':id') }}".replace(':id', data.id))
            .append('<input type="hidden" name="_method" value="PUT">');

        // UI feedback
        $('#formTitle').text('Edit House');
        $('#formSubtitle').text('Update property details');
        $('#submitBtn').html('<i class="bi bi-save me-2"></i> Update Property');
        $('#cancelEditBtn').removeClass('d-none');
    });

    // Reset / Cancel Edit
    $('#cancelEditBtn, #resetBtn').on('click', function(e) {
        e.preventDefault();
        $('#houseForm')[0].reset();
        $('#houseForm')
            .attr('action', "{{ route('house-creations.store') }}")
            .find('input[name="_method"]').remove();

        $('#formTitle').text('House Creation');
        $('#formSubtitle').text('Fill in the details to register a new property');
        $('#submitBtn').html('<i class="bi bi-cloud-upload me-2"></i> Save Property');
        $('#cancelEditBtn').addClass('d-none');

        toggleSubscriptionFields();

        $('#owner_member_name, #owner_member_adhar_number, #owner_member_date, #owner_member_father_name, #owner_member_mother_name, #owner_member_relation_id, #owner_member_dob, #owner_member_age, #owner_member_gender, #owner_member_blood_group, #owner_member_mobile, #owner_member_whatsapp, #owner_member_marital_status, #owner_member_spouse_name, #owner_member_islamic_qualification_id, #owner_member_qualification_id, #owner_member_occupation_id, #owner_member_job_location_id, #owner_member_subscription, #owner_member_default_subscription, #owner_member_subscription_amount, #owner_member_subscription_type, #owner_member_narration, #owner_member_op_amount').val('');
        $('#owner_member_image_modal').val('');
        $('#owner_member_active').val('1');
    });

    // Add new Mahallu via AJAX
    $('#btnSavePlace').on('click', function() {
        const name = $('#p_name').val().trim();
        if (!name) return appAlert('Mahallu name is required');

        $.post("{{ route('places.store') }}", {
            name: name,
            description: $('#p_desc').val(),
            status: 'active'
        }, function(data) {
            $('#placeSelect').append(new Option(data.name, data.id, true, true));
            $('#placeModal').modal('hide');
            $('#p_name, #p_desc').val('');
        }).fail(function() {
            appAlert('Failed to add mahallu. Please try again.', 'error');
        });
    });

    // Add new House Type via AJAX
    $('#btnSaveType').on('click', function() {
        const name = $('#t_name').val().trim();
        if (!name) return appAlert('Type name is required');

        $.post("{{ route('house-types.store') }}", {
            name: name,
            description: $('#t_desc').val(),
            status: 'active'
        }, function(data) {
            $('#houseTypeSelect').append(new Option(data.name, data.id, true, true));
            $('#typeModal').modal('hide');
            $('#t_name, #t_desc').val('');
        }).fail(function() {
            appAlert('Failed to add house type. Please try again.', 'error');
        });
    });

    // Add lookup values in owner modal (same endpoints as member creation)
    function addOwnerLookup(url, inputSelector, selectSelector, modalId) {
        const name = $(inputSelector).val().trim();
        if (!name) {
            appAlert('Name is required');
            return;
        }

        $.post(url, { name: name }, function(data) {
            $(selectSelector).append(new Option(data.name, data.id, true, true));
            $(inputSelector).val('');
            const modalEl = document.getElementById(modalId);
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            // keep owner modal context active after adding lookup value
            restoreOwnerModalAfterChild = true;
        }).fail(function(xhr) {
            const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to add';
            appAlert(message, 'error');
        });
    }

    // Quick add from owner modal without opening nested Bootstrap modals.
    $(document).on('click', '.owner-quick-add', function(e) {
        e.preventDefault();
        const kind = $(this).data('kind');
        const map = {
            relation: {
                label: 'relation',
                url: "{{ route('members.createRelation') }}",
                select: '#owner_relation_modal'
            },
            islamic_qualification: {
                label: 'islamic qualification',
                url: "{{ route('members.createIslamicQualification') }}",
                select: '#owner_islamic_qualification_modal'
            },
            qualification: {
                label: 'qualification',
                url: "{{ route('members.createQualification') }}",
                select: '#owner_qualification_modal'
            },
            occupation: {
                label: 'occupation',
                url: "{{ route('members.createOccupation') }}",
                select: '#owner_occupation_modal'
            },
            job_location: {
                label: 'job location',
                url: "{{ route('members.createJobLocation') }}",
                select: '#owner_job_location_modal'
            }
        };

        const cfg = map[kind];
        if (!cfg) return;

        appPrompt(`Enter new ${cfg.label} name`, {
            title: `Add ${cfg.label}`,
            placeholder: `New ${cfg.label} name`
        }).then(function(name) {
            if (!name) return;

            $.post(cfg.url, { name: name }, function(data) {
                $(cfg.select).append(new Option(data.name, data.id, true, true));
            }).fail(function(xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to add';
                appAlert(message, 'error');
            });
        });
    });

    $('#ownerSaveRelationBtn').on('click', function() {
        addOwnerLookup("{{ route('members.createRelation') }}", '#ownerNewRelationName', '#owner_relation_modal', 'ownerRelationCreateModal');
    });

    $('#ownerSaveIslamicQualificationBtn').on('click', function() {
        addOwnerLookup("{{ route('members.createIslamicQualification') }}", '#ownerNewIslamicQualificationName', '#owner_islamic_qualification_modal', 'ownerIslamicQualificationCreateModal');
    });

    $('#ownerSaveQualificationBtn').on('click', function() {
        addOwnerLookup("{{ route('members.createQualification') }}", '#ownerNewQualificationName', '#owner_qualification_modal', 'ownerQualificationCreateModal');
    });

    $('#ownerSaveOccupationBtn').on('click', function() {
        addOwnerLookup("{{ route('members.createOccupation') }}", '#ownerNewOccupationName', '#owner_occupation_modal', 'ownerOccupationCreateModal');
    });

    $('#ownerSaveJobLocationBtn').on('click', function() {
        addOwnerLookup("{{ route('members.createJobLocation') }}", '#ownerNewJobLocationName', '#owner_job_location_modal', 'ownerJobLocationCreateModal');
    });
});
</script>
</body>
</html>

