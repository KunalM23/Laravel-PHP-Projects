@extends('admin.layouts.app')
@section('title', 'Leads Management')

@section('content')

@php
    $leads = $leads ?? collect([]);
    $sources = $sources ?? collect([]);
    $statuses = $statuses ?? collect([]);
    $users = $users ?? collect([]);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow">
    <div class="text-white">
        <h4 class="mb-0 fw-bold">Leads & Pipeline</h4>
        <p class="mb-0 small opacity-75">Track potential clients and conversion scores</p>
    </div>
    <button class="btn btn-warning fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addLeadModal">
        <i class="bi bi-plus-circle-fill me-2"></i>Add New Lead
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lead Info</th>
                        <th>Company</th>
                        <th>Assigned To</th>
                        <th>Lead Score</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-soft-primary text-primary fw-bold">
                                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $lead->name }}</div>
                                    <div class="text-muted small">{{ $lead->email ?? 'No Email' }} | {{ $lead->phone ?? 'No Phone' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $lead->company ?? 'Individual' }}</td>
                        <td>
                            <div class="small fw-bold text-dark"><i class="bi bi-person me-1"></i>{{ $lead->assigned_name }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center" style="min-width: 120px;">
                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                    <div class="progress-bar {{ $lead->score > 70 ? 'bg-success' : ($lead->score > 40 ? 'bg-warning' : 'bg-danger') }}" 
                                        role="progressbar" style="width: {{ $lead->score }}%"></div>
                                </div>
                                <span class="small fw-bold">{{ $lead->score }}%</span>
                            </div>
                            @if(!empty($lead->ai_analysis))
                                <div class="text-muted mt-1" style="font-size: 0.65rem; line-height: 1;">
                                    <i class="bi bi-robot me-1"></i>{{ $lead->ai_analysis }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-sm btn-white border text-primary" 
                                    onclick="openEditModal({{ json_encode($lead) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-white border text-danger" 
                                    onclick="confirmDelete({{ $lead->id }}, '{{ $lead->name }}', 'lead')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-form-{{ $lead->id }}" action="/admin/leads/{{ $lead->id }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No leads found in the system.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Lead Modal --}}
<div class="modal fade" id="addLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/admin/leads" method="POST" class="modal-content border-0">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Capture New Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Lead Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Company</label>
                    <input type="text" name="company" class="form-control" placeholder="Company Name">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Source</label>
                    <select name="source_id" class="form-select" required>
                        <option value="">Select Source</option>
                        @foreach($sources as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Assign To</label>
                    <select name="assigned_to" class="form-select" required>
                        <option value="">Select Staff</option>
                        @foreach($users as $u)
                            @if($u->id != 1)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                {{-- Hidden input for status so the backend doesn't fail --}}
                <input type="hidden" name="status_id" value="1">
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-primary px-5 fw-bold">Save Lead</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Lead Modal --}}
<div class="modal fade" id="editLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editLeadForm" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">Edit Lead Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Lead Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Company</label>
                    <input type="text" name="company" id="edit_company" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Phone</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Source</label>
                    <select name="source_id" id="edit_source" class="form-select">
                        @foreach($sources as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Assigned To</label>
                    <select name="assigned_to" id="edit_assigned" class="form-select" required>
                        @foreach($users as $u)
                            @if($u->id != 1)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="status_id" id="edit_status">
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success px-5 fw-bold">Update Lead</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(lead) {
        document.getElementById('editLeadForm').action = '/admin/leads/' + lead.id;
        document.getElementById('edit_name').value = lead.name;
        document.getElementById('edit_company').value = lead.company;
        document.getElementById('edit_email').value = lead.email;
        document.getElementById('edit_phone').value = lead.phone;
        document.getElementById('edit_source').value = lead.source_id;
        document.getElementById('edit_status').value = lead.status_id;
        document.getElementById('edit_assigned').value = lead.assigned_to;

        new bootstrap.Modal(document.getElementById('editLeadModal')).show();
    }
</script>

<style>
    .page-header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 1.5rem 2rem; border-radius: 15px; }
    .bg-soft-primary { background-color: #ecfdf5; color: #065f46; }
    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .btn-white { background: #fff; }
    .progress { background-color: #e2e8f0; border-radius: 10px; }
</style>

@endsection