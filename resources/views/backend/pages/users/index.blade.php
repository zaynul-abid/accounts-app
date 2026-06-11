@extends('backend.layouts.app')
@section('title', 'Manage Users')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Manage Users')
@section('sub-header', 'Create accounts and maintain access')
@section('content')
    <section class="content py-3">
        <div class="container-fluid">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold mb-0">Create New User</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.users.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input
                                        type="text"
                                        id="username"
                                        name="username"
                                        class="form-control"
                                        value="{{ old('username') }}"
                                        required
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ old('email') }}"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select id="role" name="role" class="form-control" required>
                                        @foreach($roles as $value => $label)
                                            <option value="{{ $value }}" @selected(old('role', \App\Models\User::ROLE_STAFF) === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control"
                                        required
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        required
                                    >
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold mb-0">Existing Users</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                    <tr>
                                        <th style="width: 24%;">Username</th>
                                        <th style="width: 24%;">Email</th>
                                        <th style="width: 14%;">Role</th>
                                        <th style="width: 14%;">Created</th>
                                        <th style="width: 24%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold">{{ $user->username }}</div>
                                                @if(auth()->id() === $user->id)
                                                    <small class="text-muted">Current account</small>
                                                @endif
                                            </td>
                                            <td>{{ $user->email ?: 'Not set' }}</td>
                                            <td>
                                                <span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-secondary' }}">
                                                    {{ $roles[$user->role] ?? ucfirst($user->role ?? 'staff') }}
                                                </span>
                                            </td>
                                            <td>{{ optional($user->created_at)->format('d M Y') }}</td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary mr-2 mb-2"
                                                    data-toggle="collapse"
                                                    data-target="#edit-user-{{ $user->id }}"
                                                    aria-expanded="false"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning mr-2 mb-2"
                                                    data-toggle="collapse"
                                                    data-target="#password-user-{{ $user->id }}"
                                                    aria-expanded="false"
                                                >
                                                    Change Password
                                                </button>
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.users.destroy', $user) }}"
                                                    class="d-inline"
                                                    data-confirm="Delete this user account?"
                                                    data-confirm-button="Yes, delete"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger mb-2"
                                                        {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="collapse-row">
                                            <td colspan="5" class="p-0 border-0">
                                                <div class="collapse border-top bg-light" id="edit-user-{{ $user->id }}">
                                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-row">
                                                            <div class="form-group col-md-4">
                                                                <label>Username</label>
                                                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <label>Email</label>
                                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label>Role</label>
                                                                <select name="role" class="form-control" required>
                                                                    @foreach($roles as $value => $label)
                                                                        <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-2 d-flex align-items-end">
                                                                <button type="submit" class="btn btn-primary btn-block">Save</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div class="collapse border-top bg-white" id="password-user-{{ $user->id }}">
                                                    <form method="POST" action="{{ route('admin.users.password', $user) }}" class="p-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-row">
                                                            <div class="form-group col-md-5">
                                                                <label>New Password</label>
                                                                <input type="password" name="password" class="form-control" required>
                                                            </div>
                                                            <div class="form-group col-md-5">
                                                                <label>Confirm Password</label>
                                                                <input type="password" name="password_confirmation" class="form-control" required>
                                                            </div>
                                                            <div class="form-group col-md-2 d-flex align-items-end">
                                                                <button type="submit" class="btn btn-warning btn-block">Update</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .collapse-row td {
            background: transparent;
        }
    </style>
@endsection
