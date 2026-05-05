@extends('user.layouts.app')
@section('title', 'Task Management')

@section('content')
@php
    $userId = session('current_user_id');
    
    // Permission check
    $userPermissionId = DB::table('permission_user')
        ->where('user_id', $userId)
        ->value('permission_id');

    // Data retrieval consistent with admin view but filtered for the user
    $tasks = DB::table('tasks')
        ->join('leads', 'tasks.lead_id', '=', 'leads.id')
        ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
        ->join('users', 'tasks.user_id', '=', 'users.id')
        ->select('tasks.*', 'leads.name as lead_name', 'task_statuses.name as status_name', 'users.name as assigned_name')
        ->where('tasks.user_id', $userId)
        ->orderByDesc('tasks.created_at')
        ->get();
        
    $leads = DB::table('leads')->where('assigned_to', $userId)->get();
    $statuses = DB::table('task_statuses')->get();
@endphp

{{-- Header: Exact match to admin design style[cite: 10] --}}
<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow text-white p-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 15px;">
    <div>
        <h4 class="mb-0 fw-bold">Task Management</h4>
        <p class="mb-0 small opacity-75">Track your activities and deadlines</p>
    </div>
    {{-- PERMISSION: Show Add User button only if all_access (1)[cite: 10] --}}
    @if($userPermissionId == 1)
        <button class="btn btn-warning fw-bold px-4" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add Task
        </button>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Task</th>
                        <th>Lead</th>
                        <th>Assigned</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        {{-- Hide column entirely if permission is read only (2) --}}
                        @if($userPermissionId != 2)
                            <th class="text-center pe-4">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $task->title }}</td>
                        <td><span class="badge bg-soft-primary text-primary border">{{ $task->lead_name }}</span></td>
                        <td>{{ $task->assigned_name }}</td>
                        <td>
                            @php
                                $pClass = ['high'=>'bg-danger', 'medium'=>'bg-info', 'low'=>'bg-secondary'][$task->priority] ?? 'bg-info';
                            @endphp
                            <span class="badge {{ $pClass }}">{{ ucfirst($task->priority) }}</span>
                        </td>
                        <td>{{ $task->due_date ? date('d M Y', strtotime($task->due_date)) : 'N/A' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ ucfirst($task->status_name) }}</span></td>
                        
                        @if($userPermissionId != 2)
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                {{-- Edit button for all_access (1) or write (3) --}}
                                @if($userPermissionId == 1 || $userPermissionId == 3)
                                    <button class="btn btn-sm btn-white border text-primary" 
                                        onclick="openEditModal({{ json_encode($task) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                @endif

                                {{-- Delete button ONLY for all_access (1) --}}
                                @if($userPermissionId == 1)
                                    <button class="btn btn-sm btn-white border text-danger" 
                                        onclick="handleDelete({{ $task->id }}, '{{ $task->title }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $task->id }}" action="/user/tasks/{{ $task->id }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No tasks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal: Matched to DB table fields and admin structure[cite: 10] --}}
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/user/tasks" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="status_id" value="1">
            <input type="hidden" name="user_id" value="{{ $userId }}">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">New Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Lead</label>
                    <select name="lead_id" class="form-select" required>
                        <option value="">Select Lead</option>
                        @foreach($leads as $l) 
                            <option value="{{ $l->id }}">{{ $l->name }}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Save Task</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal: Matched to admin design[cite: 10] --}}
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editTaskForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5>Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Task Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control mb-2" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" id="edit_priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Status</label>
                    <select name="status_id" id="edit_status" class="form-select">
                        @foreach($statuses as $st) 
                            <option value="{{ $st->id }}">{{ $st->name }}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">Update Task</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openEditModal(task) {
        document.getElementById('editTaskForm').action = '/user/tasks/' + task.id;
        document.getElementById('edit_title').value = task.title;
        document.getElementById('edit_priority').value = task.priority;
        document.getElementById('edit_status').value = task.status_id;
        document.getElementById('edit_description').value = task.description || '';
        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
    }

    function handleDelete(id, name) {
        Swal.fire({
            title: 'Delete Task?',
            text: `Are you sure you want to delete "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
    @endif
</script>

<style>
    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca; }
    .btn-white { background: #fff; }
</style>
@endsection