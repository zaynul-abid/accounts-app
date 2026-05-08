@extends('backend.layouts.app')
@section('title', $title)
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', $title)
@section('sub-header', 'Lookup Masters')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-body py-2">
                            @foreach($availableTypes as $typeKey => $cfg)
                                <a href="{{ route('admin.lookups.index', $typeKey) }}"
                                   class="btn btn-sm {{ $typeKey === $type ? 'btn-dark' : 'btn-outline-dark' }} mr-1 mb-1">
                                    {{ $cfg['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add {{ \Illuminate\Support\Str::singular($title) }}</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.lookups.store', $type) }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="active">Active</label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }} List</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th style="width: 70px;">#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 170px;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($items as $item)
                                        @php
                                            $isActive = isset($item->active) ? (bool) $item->active : (($item->status ?? 'inactive') === 'active');
                                        @endphp
                                        <tr>
                                            <td>{{ $items->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->description ?: '-' }}</td>
                                            <td>
                                                @if($isActive)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Disabled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-warning edit-master-btn"
                                                        data-toggle="modal"
                                                        data-target="#editMasterModal"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-description="{{ $item->description }}"
                                                        data-active="{{ $isActive ? 1 : 0 }}">
                                                    Edit
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('admin.lookups.destroy', ['type' => $type, 'id' => $item->id]) }}"
                                                      class="d-inline-block"
                                                      onsubmit="return confirm('Delete this item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No records found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="editMasterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editMasterForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit {{ \Illuminate\Support\Str::singular($title) }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_active" name="active" value="1">
                            <label class="custom-control-label" for="edit_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).on('click', '.edit-master-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const description = $(this).data('description') || '';
            const active = parseInt($(this).data('active'), 10) === 1;

            $('#edit_name').val(name);
            $('#edit_description').val(description);
            $('#edit_active').prop('checked', active);
            $('#editMasterForm').attr('action', "{{ route('admin.lookups.update', ['type' => $type, 'id' => '__ID__']) }}".replace('__ID__', id));
        });
    </script>
@endsection
