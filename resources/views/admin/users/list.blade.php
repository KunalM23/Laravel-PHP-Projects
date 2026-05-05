@extends('admin.layouts.app')
@section('title', 'Users Management')

@section('content')

@php
    $permissions_list = DB::table('permissions')->get();
    $designations = DB::table('designations')->get();

    $users = DB::table('users')
        ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
        ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
        ->select('users.*', 'designations.name as designation_name', 'roles.name as role_name')
        ->where('users.id', '!=', session('current_user_id')) 
        ->orderByDesc('users.created_at')
        ->get();

    foreach($users as $user) {
        $user->permissions = DB::table('permission_user')
            ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
            ->where('permission_user.user_id', $user->id)
            ->pluck('permissions.name', 'permissions.id')
            ->toArray();
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow">
    <div class="text-white">
        <h4 class="mb-0 fw-bold">Staff & Access Control</h4>
        <p class="mb-0 small opacity-75">Manage profiles and individual module permissions</p>
    </div>
    <button class="btn btn-warning fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-2"></i>Add Staff
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Member</th>
                        <th>Designation</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->image ? asset('storage/'.$user->image) : asset('assets/images/default-user.png') }}" 
                                     class="rounded-circle me-3 border" width="45" height="45" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="text-muted small">@ {{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->designation_name ?? 'Not Assigned' }}</td>
                        <td><span class="badge bg-soft-primary text-primary border">{{ strtoupper($user->role_name ?? 'User') }}</span></td>
                        <td>
                            <div class="permission-pills p-1 rounded" onclick="openPermissionModal({{ json_encode($user) }})" style="cursor: pointer; border: 1px dashed #ddd;">
                                @forelse($user->permissions as $pName)
                                    <span class="badge bg-info x-small mb-1">{{ $pName }}</span>
                                @empty
                                    <span class="text-muted x-small italic">No permissions set</span>
                                @endforelse
                            </div>
                        </td>
                        <td><span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($user->status) }}</span></td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-sm btn-white border text-primary" onclick="openEditModal({{ json_encode($user) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Updated Delete to SweetAlert --}}
                                <button class="btn btn-sm btn-white border text-danger" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}', 'user')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-form-{{ $user->id }}" action="/admin/users/{{ $user->id }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/admin/users" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Register New Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8 row g-3">
                    <div class="col-12"><label class="form-label small fw-bold">Full Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label small fw-bold">Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Designation</label>
                        <select name="designation_id" class="form-select" required>
                            <option value="">Select Designation</option>
                            @foreach($designations as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4 text-center border-start">
                    <label class="form-label small fw-bold">Avatar</label>
                    <div class="mb-2"><img id="add_preview" src="https://ui-avatars.com/api/?name=User" class="rounded-circle border" width="100" height="100" style="object-fit: cover;"></div>
                    <input type="file" name="image" class="form-control form-control-sm" onchange="previewImage(this, 'add_preview')">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer bg-light"><button type="submit" class="btn btn-primary px-5 fw-bold">Create User</button></div>
        </form>
    </div>
</div>

{{-- Permissions Modal --}}
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form id="permissionForm" method="POST" class="modal-content border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold">Module Access: <span id="perm_user_name"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-2">
                @foreach($permissions_list as $p)
                    <div class="col-6">
                        <div class="form-check form-switch p-2 border rounded bg-light">
                            <input class="form-check-input ms-0 me-2 perm-check" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="p_{{ $p->id }}">
                            <label class="form-check-label small fw-bold" for="p_{{ $p->id }}">{{ $p->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Update Permissions</button></div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editForm" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">Edit Staff Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8 row g-3">
                    <div class="col-12"><label class="form-label small fw-bold">Full Name</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label small fw-bold">Username</label><input type="text" name="username" id="edit_username" class="form-control" required></div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Gender</label>
                        <select name="gender" id="edit_gender" class="form-select">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Designation</label>
                        <select name="designation_id" id="edit_designation" class="form-select">
                            @foreach($designations as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 text-center border-start">
                    <label class="form-label small fw-bold">Photo</label>
                    <div class="mb-2"><img id="edit_preview" src="" class="rounded-circle border" width="100" height="100" style="object-fit: cover;"></div>
                    <input type="file" name="image" class="form-control form-control-sm" onchange="previewImage(this, 'edit_preview')">
                </div>
            </div>
            <div class="modal-footer border-0"><button type="submit" class="btn btn-success px-5 fw-bold">Save Changes</button></div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById(id).src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openPermissionModal(user) {
        document.getElementById('permissionForm').action = '/admin/users/' + user.id + '/permissions';
        document.getElementById('perm_user_name').innerText = user.name;
        const checkboxes = document.querySelectorAll('.perm-check');
        checkboxes.forEach(cb => { cb.checked = user.permissions.hasOwnProperty(cb.value); });
        new bootstrap.Modal(document.getElementById('permissionModal')).show();
    }

    function openEditModal(user) {
        document.getElementById('editForm').action = '/admin/users/' + user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_gender').value = user.gender;
        document.getElementById('edit_status').value = user.status;
        document.getElementById('edit_designation').value = user.designation_id;
        document.getElementById('edit_preview').src = user.image ? '/storage/' + user.image : '/assets/images/default-user.png';
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }
</script>

<style>
    .page-header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 1.5rem 2rem; border-radius: 15px; }
    .bg-soft-primary { background-color: #f5f3ff; color: #5b21b6; }
    .x-small { font-size: 0.65rem; }
    .btn-white { background: #fff; }
</style>
@endsection