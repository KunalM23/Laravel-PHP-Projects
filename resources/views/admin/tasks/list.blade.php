@extends('admin.layouts.app')
@section('title', 'Tasks Management')
@section('page-title', 'Tasks Management')

@section('content')
@php
    $tasks = DB::table('tasks')
        ->join('leads', 'tasks.lead_id', '=', 'leads.id')
        ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
        ->join('users', 'tasks.user_id', '=', 'users.id')
        ->select('tasks.*', 'leads.name as lead_name', 'task_statuses.name as status_name', 'users.name as assigned_name')
        ->orderByDesc('tasks.created_at')
        ->get();
        
    $users = DB::table('users')->where('id', '!=', 1)->get();
    $leads = DB::table('leads')->get();
    $statuses = DB::table('task_statuses')->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow text-white p-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 15px;">
    <div>
        <h4 class="mb-0 fw-bold">Task Management</h4>
        <p class="mb-0 small opacity-75">Assign and track staff activities</p>
    </div>
    <button class="btn btn-warning fw-bold px-4" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        <i class="bi bi-plus-circle-fill me-2"></i>Add Task
    </button>
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
                        <th class="text-center pe-4">Actions</th>
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
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-white border text-primary" 
                                    onclick="openEditModal({{ json_encode($task) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-white border text-danger" 
                                    onclick="confirmDelete({{ $task->id }}, '{{ $task->title }}', 'task')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-form-{{ $task->id }}" action="/admin/tasks/{{ $task->id }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">No tasks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/tasks" method="POST" class="modal-content">
            @csrf
            {{-- CRITICAL FIX: Controller requires status_id. Defaulting to 1 (Pending) --}}
            <input type="hidden" name="status_id" value="1">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">New Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="col-6"><label class="form-label">Lead</label>
                    <select name="lead_id" class="form-select" required>
                        <option value="">Select Lead</option>
                        @foreach($leads as $l) <option value="{{ $l->id }}">{{ $l->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-6"><label class="form-label">Staff</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Select Staff</option>
                        @foreach($users as $u) <option value="{{ $u->id }}">{{ $u->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-6"><label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-6"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Save Task</button></div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editTaskForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header"><h5>Edit Task</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
                        @foreach($statuses as $st) <option value="{{ $st->id }}">{{ $st->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success w-100">Update Task</button></div>
        </form>
    </div>
</div>

<script>
    function openEditModal(task) {
        document.getElementById('editTaskForm').action = '/admin/tasks/' + task.id;
        document.getElementById('edit_title').value = task.title;
        document.getElementById('edit_priority').value = task.priority;
        document.getElementById('edit_status').value = task.status_id;
        document.getElementById('edit_description').value = task.description || '';
        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
    }
</script>

<style>
    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca; }
    .btn-white { background: #fff; }
</style>
@endsection