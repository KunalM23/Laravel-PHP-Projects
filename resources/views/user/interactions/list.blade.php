@extends('user.layouts.app')
@section('title', 'Interactions')

@section('content')
@php
    $userId = session('current_user_id');
    
    // Fetch numeric permission_id from the junction table like in existing_23.php[cite: 13]
    $userPermissionId = DB::table('permission_user')
        ->where('user_id', $userId)
        ->value('permission_id');

    // Fetch interactions specifically for this user to maintain data isolation[cite: 14]
    $interactions = DB::table('interactions')
        ->join('leads', 'interactions.lead_id', '=', 'leads.id')
        ->join('interaction_types', 'interactions.interaction_type_id', '=', 'interaction_types.id')
        ->select('interactions.*', 'leads.name as lead_name', 'interaction_types.name as type_name')
        ->where('interactions.user_id', $userId)
        ->orderByDesc('interactions.interaction_date')
        ->get();
        
    $leads = DB::table('leads')->where('assigned_to', $userId)->get();
    $types = DB::table('interaction_types')->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow">
    <div class="text-white">
        <h4 class="mb-0 fw-bold">Interactions Log</h4>
        <p class="mb-0 small opacity-75">Track your conversations and follow-up activities</p>
    </div>
    
    @if($userPermissionId == 1)
        <button class="btn btn-warning fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addInteractionModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Log Interaction
        </button>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lead</th>
                        <th>Type</th>
                        <th>Date & Time</th>
                        <th>Notes</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interactions as $item)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $item->lead_name }}</td>
                        <td>
                            <span class="badge bg-soft-primary text-primary border px-2">
                                {{ ucfirst($item->type_name) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->interaction_date)->format('d M Y, H:i') }}</td>
                        <td class="small text-muted">{{ Str::limit($item->notes, 60) }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm">
                                {{-- FIX: Allow both Full Access (1) AND Write Access (3) to see the Edit button --}}
                                @if($userPermissionId == 1 || $userPermissionId == 3)
                                    <button class="btn btn-sm btn-white border text-primary" 
                                        onclick="openEditModal({{ json_encode($item) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endif
                                
                                {{-- Keep Delete restricted to Full Access (1) only --}}
                                @if($userPermissionId == 1)
                                    <button class="btn btn-sm btn-white border text-danger" 
                                        onclick="handleDelete({{ $item->id }}, '{{ $item->lead_name }} interaction')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}" action="/user/interactions/{{ $item->id }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No interactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Interaction Modal --}}
<div class="modal fade" id="addInteractionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Log New Interaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/user/interactions">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Lead <span class="text-danger">*</span></label>
                        <select class="form-select" name="lead_id" required>
                            <option value="">Select Lead</option>
                            @foreach($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="interaction_type_id" required>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ ucfirst($type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="interaction_date" value="{{ date('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Logged By</label>
                        @php $currentUserName = DB::table('users')->where('id', $userId)->value('name'); @endphp
                        <input type="text" class="form-control bg-light" value="{{ $currentUserName }}" readonly disabled>
                        <input type="hidden" name="user_id" value="{{ $userId }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Interaction details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Save Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editInteractionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editInteractionForm" method="POST" class="modal-content border-0">
            @csrf @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Edit Interaction Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Lead</label>
                    <select name="lead_id" id="edit_lead_id" class="form-select" required>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Type</label>
                    <select name="interaction_type_id" id="edit_type_id" class="form-select" required>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ ucfirst($t->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Date</label>
                    <input type="datetime-local" name="interaction_date" id="edit_date" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Notes</label>
                    <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Update Log</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openEditModal(item) {
        document.getElementById('editInteractionForm').action = '/user/interactions/' + item.id;
        document.getElementById('edit_lead_id').value = item.lead_id;
        document.getElementById('edit_type_id').value = item.interaction_type_id;
        
        let date = new Date(item.interaction_date);
        date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        document.getElementById('edit_date').value = date.toISOString().slice(0, 16);
        document.getElementById('edit_notes').value = item.notes;

        new bootstrap.Modal(document.getElementById('editInteractionModal')).show();
    }

    function handleDelete(id, name) {
        Swal.fire({
            title: 'Delete Log?',
            text: `Are you sure you want to delete the ${name}?`,
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
        Swal.fire({ icon: 'success', title: 'Done!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
    @endif
</script>

<style>
    .page-header { background: linear-gradient(135deg, #0b1c39 0%, #2040a9 100%); padding: 1.5rem 2rem; border-radius: 15px; }
    .bg-soft-primary { background-color: #eef2ff; color: #4338ca; }
    .btn-white { background: #fff; }
</style>
@endsection