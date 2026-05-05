@extends('user.layouts.app')
@section('title', 'My Leads')

@section('content')
@php
    $userId = session('current_user_id');
    
    $userPermissionId = DB::table('permission_user')
        ->where('user_id', $userId)
        ->value('permission_id');

    $leads = DB::table('leads')
        ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
        ->select('leads.*', 'lead_statuses.name as status_name')
        ->where('leads.assigned_to', $userId)
        ->orderByDesc('leads.created_at')
        ->get();
        
    $sources = DB::table('sources')->get();
    $statuses = DB::table('lead_statuses')->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 page-header shadow">
    <div class="text-white">
        <h4 class="mb-0 fw-bold">My Leads & Pipeline</h4>
        <p class="mb-0 small opacity-75">Manage your prospects and conversion scores</p>
    </div>
    @if($userPermissionId == 1)
        <button class="btn btn-warning fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addLeadModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Lead
        </button>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Lead Info</th>
                        <th>Company</th>
                        {{-- STATUS REMOVED FROM TABLE HEADER --}}
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
                                    <div class="text-muted small">{{ $lead->email ?? 'No Email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $lead->company ?? 'Individual' }}</td>
                        {{-- STATUS CELL REMOVED FROM TABLE BODY --}}
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
                                @if($userPermissionId == 1)
                                    <button class="btn btn-sm btn-white border text-primary" 
                                        onclick="openEditModal({{ json_encode($lead) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-white border text-danger" 
                                        onclick="handleDelete({{ $lead->id }}, '{{ $lead->name }}')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>

                                    <form id="delete-form-{{ $lead->id }}" action="/user/leads/{{ $lead->id }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">No leads found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Lead Modal (Original Logic Maintained) -->
<div class="modal fade" id="addLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Add New Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/user/leads">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Company</label>
                        <input type="text" class="form-control" name="company" placeholder="Company Name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="email@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phone</label>
                        <input type="tel" class="form-control" name="phone" placeholder="Phone Number">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Source <span class="text-danger">*</span></label>
                        <select class="form-select" name="source_id" required>
                            <option value="">Select Source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}">{{ ucfirst($source->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status_id" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Assigned To</label>
                        @php $currentUserName = DB::table('users')->where('id', $userId)->value('name'); @endphp
                        <input type="text" class="form-control bg-light" value="{{ $currentUserName }}" readonly disabled>
                        <input type="hidden" name="assigned_to" value="{{ $userId }}">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Save Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lead Modal (Original Logic Maintained) -->
<div class="modal fade" id="editLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Edit Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editLeadForm">
                @csrf @method('PUT')
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editLeadName" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" id="editLeadEmail">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phone</label>
                        <input type="tel" class="form-control" name="phone" id="editLeadPhone">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Company</label>
                        <input type="text" class="form-control" name="company" id="editLeadCompany">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Source <span class="text-danger">*</span></label>
                        <select class="form-select" name="source_id" id="editLeadSource" required>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}">{{ ucfirst($source->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status_id" id="editLeadStatus" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openEditModal(lead) {
        document.getElementById('editLeadForm').action = '/user/leads/' + lead.id;
        document.getElementById('editLeadName').value = lead.name;
        document.getElementById('editLeadEmail').value = lead.email;
        document.getElementById('editLeadPhone').value = lead.phone;
        document.getElementById('editLeadCompany').value = lead.company;
        document.getElementById('editLeadSource').value = lead.source_id;
        document.getElementById('editLeadStatus').value = lead.status_id;

        new bootstrap.Modal(document.getElementById('editLeadModal')).show();
    }

    function handleDelete(id, name) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Confirm deletion of ${name}?`,
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
    .page-header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 1.5rem 2rem; border-radius: 15px; }
    .bg-soft-primary { background-color: #ecfdf5; color: #065f46; }
    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .btn-white { background: #fff; }
</style>
@endsection