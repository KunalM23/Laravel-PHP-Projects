@extends('admin.layouts.app')
@section('title', 'Interactions Management')
@section('page-title', 'Interactions Management')

@section('content')
@php
    $interactions = DB::table('interactions')
        ->join('leads', 'interactions.lead_id', '=', 'leads.id')
        ->join('interaction_types', 'interactions.interaction_type_id', '=', 'interaction_types.id')
        ->join('users', 'interactions.user_id', '=', 'users.id')
        ->select('interactions.*', 'leads.name as lead_name', 'interaction_types.name as type_name', 'users.name as user_name')
        ->orderByDesc('interactions.interaction_date')
        ->get();
        
    $leads = DB::table('leads')->get();
    $types = DB::table('interaction_types')->get();
    $staff = DB::table('users')->where('id', '!=', 1)->get();
@endphp

{{-- 
    Manual success alert removed. 
    It is now handled by the global SweetAlert in app.blade.php. 
--}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">All Interactions</h4>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addInteractionModal">
        <i class="bi bi-plus-circle me-2"></i>Log Interaction
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($interactions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lead</th>
                        <th>Type</th>
                        <th>Assigned Staff</th>
                        <th>Date & Time</th>
                        <th>Notes</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($interactions as $interaction)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">{{ $interaction->lead_name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-soft-primary text-primary border px-2">
                                {{ ucfirst($interaction->type_name) }}
                            </span>
                        </td>
                        <td>{{ $interaction->user_name }}</td>
                        <td>
                            <span class="text-muted small">
                                {{ \Carbon\Carbon::parse($interaction->interaction_date)->format('d M Y, H:i') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                {{ Str::limit($interaction->notes, 60) ?: 'No notes provided' }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-sm btn-white border text-primary"
                                    onclick="editInteraction({{ $interaction->id }}, '{{ $interaction->lead_id }}', '{{ $interaction->interaction_type_id }}', '{{ $interaction->user_id }}', '{{ \Carbon\Carbon::parse($interaction->interaction_date)->format('Y-m-d\TH:i') }}', '{{ addslashes($interaction->notes) }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                {{-- Trigger SweetAlert confirmation via layout function --}}
                                <button class="btn btn-sm btn-white border text-danger" 
                                    onclick="confirmDelete({{ $interaction->id }}, '{{ $interaction->lead_name }}', 'interaction')">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $interaction->id }}" action="/admin/interactions/{{ $interaction->id }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-dots text-muted display-4"></i>
                <h5 class="mt-3 text-muted">No interactions recorded yet</h5>
            </div>
        @endif
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addInteractionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/interactions" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Log New Interaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label small fw-bold">Target Lead</label>
                    <select name="lead_id" class="form-select" required>
                        <option value="">Select Lead</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Interaction Type</label>
                    <select name="interaction_type_id" class="form-select" required>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ ucfirst($t->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Staff Member</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Select Staff</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Date & Time</label>
                    <input type="datetime-local" name="interaction_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Notes / Comments</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Summary of the conversation..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-primary px-4 fw-bold">Save Interaction</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editInteractionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editInteractionForm" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Update Interaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label small fw-bold">Target Lead</label>
                    <select name="lead_id" id="editInteractionLead" class="form-select" required>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Interaction Type</label>
                    <select name="interaction_type_id" id="editInteractionType" class="form-select" required>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ ucfirst($t->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Staff Member</label>
                    <select name="user_id" id="editInteractionUser" class="form-select" required>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Date & Time</label>
                    <input type="datetime-local" name="interaction_date" id="editInteractionDate" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Notes</label>
                    <textarea name="notes" id="editInteractionNotes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success px-4 fw-bold">Apply Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editInteraction(id, leadId, typeId, userId, date, notes) {
    document.getElementById('editInteractionForm').action = '/admin/interactions/' + id;
    document.getElementById('editInteractionLead').value = leadId;
    document.getElementById('editInteractionType').value = typeId;
    document.getElementById('editInteractionUser').value = userId;
    document.getElementById('editInteractionDate').value = date;
    document.getElementById('editInteractionNotes').value = notes;
    new bootstrap.Modal(document.getElementById('editInteractionModal')).show();
}
</script>

<style>
    .bg-soft-primary { background-color: #eef2ff; }
    .btn-white { background: #fff; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection