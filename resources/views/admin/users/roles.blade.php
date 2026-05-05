@extends('admin.layouts.app')
@section('title', 'User Roles Management')

@section('content')
@php
    $users = DB::table('users')
        ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
        ->select('users.*', 'designations.name as designation_name')
        ->orderByDesc('users.created_at')
        ->get();
    $designations = DB::table('designations')->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">User Roles Management</h4>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDesignationModal">
        <i class="bi bi-plus-circle me-2"></i>Add Designation
    </button>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-people me-2"></i>Users & Their Roles</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Username</th>
                        <th>Current Role</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td><span class="badge bg-primary">{{ $user->designation_name ?? 'No Role' }}</span></td>
                        <td>
                            <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="updateUserRole({{ $user->id }}, '{{ $user->name }}', '{{ $user->designation_id }}', '{{ $user->status }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- SweetAlert Status Toggle --}}
                                <button class="btn btn-outline-info" onclick="confirmStatusToggle({{ $user->id }}, '{{ $user->name }}', '{{ $user->status }}')">
                                    <i class="bi bi-toggle-{{ $user->status == 'active' ? 'on' : 'off' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-shield-check me-2"></i>Available Designations</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Designation Name</th>
                        <th>Created At</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($designations as $designation)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $designation->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($designation->created_at)->format('d M Y') }}</td>
                        <td class="text-center pe-4">
                            {{-- SweetAlert Delete --}}
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $designation->id }}, '{{ $designation->name }}', 'designation')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="delete-form-{{ $designation->id }}" action="/admin/designations/{{ $designation->id }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Designation Modal --}}
<div class="modal fade" id="addDesignationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/designations" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>New Designation</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label fw-bold">Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Sales Manager">
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Save</button></div>
        </form>
    </div>
</div>

<script>
    function confirmStatusToggle(id, name, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const actionText = currentStatus === 'active' ? 'Deactivate' : 'Activate';
        
        Swal.fire({
            title: actionText + ' User?',
            text: `Are you sure you want to ${actionText.toLowerCase()} ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: currentStatus === 'active' ? '#d33' : '#28a745',
            confirmButtonText: 'Yes, ' + actionText + '!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform AJAX or Form Submit
                fetch(`/admin/users/${id}/role`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                }).then(() => location.reload());
            }
        });
    }
</script>

<style>
    .table th { background: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
    .card { border-radius: 12px; }
    .btn-primary { background-color: #2f5d8a; border-color: #2f5d8a; }
</style>
@endsection