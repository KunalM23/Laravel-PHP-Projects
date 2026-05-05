@extends('user.layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
@php
    $userId = session('current_user_id');
    $myLeads = DB::table('leads')->where('assigned_to', $userId)->count();
    $myTasks = DB::table('tasks')->where('user_id', $userId)->count();
    $myPending = DB::table('tasks')->where('user_id', $userId)->where('status_id', '!=', 3)->count();
    $myInteractions = DB::table('interactions')->where('user_id', $userId)->count();
    
    // Recent data for dashboard
    $recentLeads = DB::table('leads')
        ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
        ->select('leads.*', 'lead_statuses.name as status_name')
        ->where('leads.assigned_to', $userId)
        ->orderByDesc('leads.created_at')
        ->limit(5)
        ->get();
        
    $recentTasks = DB::table('tasks')
        ->join('leads', 'tasks.lead_id', '=', 'leads.id')
        ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
        ->select('tasks.*', 'leads.name as lead_name', 'task_statuses.name as status_name')
        ->where('tasks.user_id', $userId)
        ->orderByDesc('tasks.created_at')
        ->limit(5)
        ->get();
        
    $recentInteractions = DB::table('interactions')
        ->join('leads', 'interactions.lead_id', '=', 'leads.id')
        ->join('interaction_types', 'interactions.interaction_type_id', '=', 'interaction_types.id')
        ->select('interactions.*', 'leads.name as lead_name', 'interaction_types.name as type_name')
        ->where('interactions.user_id', $userId)
        ->orderByDesc('interactions.interaction_date')
        ->limit(5)
        ->get();
@endphp

<style>
    /* Compact Stats Cards with Color Fills */
    .stats-card {
        padding: 15px;
        border-radius: 12px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-card .stats-num { font-size: 1.8rem; font-weight: 700; }
    .stats-card .stats-label { font-size: 0.9rem; opacity: 0.9; }
    .stats-card i { font-size: 2.2rem; opacity: 0.3; }

    /* Custom Gradients */
    .bg-gradient-leads { background: linear-gradient(45deg, #10b981, #059669); }
    .bg-gradient-tasks { background: linear-gradient(45deg, #f59e0b, #d97706); }
    .bg-gradient-chat { background: linear-gradient(45deg, #3b82f6, #2563eb); }
    .bg-gradient-pending { background: linear-gradient(45deg, #ef4444, #dc2626); }

    /* Modern Table/List Styling */
    .activity-card { border: none; border-radius: 12px; }
    .activity-header { background: transparent; border-bottom: 1px solid #f1f5f9; padding: 15px; }
    .activity-list .list-group-item { 
        padding: 12px 15px; 
        border: none; 
        border-bottom: 1px solid #f8fafc; 
        font-size: 0.85rem;
    }
</style>

<!-- Statistics Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card bg-gradient-leads">
            <div>
                <div class="stats-num">{{ $myLeads }}</div>
                <div class="stats-label">My Leads</div>
            </div>
            <i class="bi bi-person-plus"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card bg-gradient-tasks">
            <div>
                <div class="stats-num">{{ $myTasks }}</div>
                <div class="stats-label">My Tasks</div>
            </div>
            <i class="bi bi-check2-square"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card bg-gradient-chat">
            <div>
                <div class="stats-num">{{ $myInteractions }}</div>
                <div class="stats-label">Interactions</div>
            </div>
            <i class="bi bi-chat-dots"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card bg-gradient-pending">
            <div>
                <div class="stats-num">{{ $myPending }}</div>
                <div class="stats-label">Pending Tasks</div>
            </div>
            <i class="bi bi-exclamation-triangle"></i>
        </div>
    </div>
</div>

<!-- Recent Activity Tables -->
<div class="row g-4">
    <!-- Recent Leads -->
    <div class="col-lg-4">
        <div class="card activity-card shadow-sm h-100">
            <div class="activity-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Recent Leads</h6>
                <a href="/user/leads" class="btn btn-sm btn-light text-primary small">All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush activity-list">
                    @forelse($recentLeads as $lead)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $lead->name }}</div>
                                <div class="text-muted small">{{ $lead->status_name }}</div>
                            </div>
                            <span class="badge bg-light text-success border">{{ \Carbon\Carbon::parse($lead->created_at)->format('d M') }}</span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No leads found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks -->
    <div class="col-lg-4">
        <div class="card activity-card shadow-sm h-100">
            <div class="activity-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Active Tasks</h6>
                <a href="/user/tasks" class="btn btn-sm btn-light text-primary small">All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush activity-list">
                    @forelse($recentTasks as $task)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">{{ Str::limit($task->title, 25) }}</span>
                                <span class="text-warning small fw-bold">{{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}</span>
                            </div>
                            <div class="text-muted small">Lead: {{ $task->lead_name }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No active tasks.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Interactions -->
    <div class="col-lg-4">
        <div class="card activity-card shadow-sm h-100">
            <div class="activity-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Interactions</h6>
                <a href="/user/interactions" class="btn btn-sm btn-light text-primary small">All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush activity-list">
                    @forelse($recentInteractions as $interaction)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">{{ ucfirst($interaction->type_name) }}</span>
                                <span class="text-info small">{{ \Carbon\Carbon::parse($interaction->interaction_date)->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted small">With: {{ $interaction->lead_name }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No interactions logged.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection