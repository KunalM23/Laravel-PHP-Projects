@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

@php
    $totalUsers = DB::table('users')->count();
    $totalLeads = DB::table('leads')->count();
    $totalTasks = DB::table('tasks')->count();
    $totalInteractions = DB::table('interactions')->count();

    $recentLeads = DB::table('leads')
        ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
        ->join('users', 'leads.assigned_to', '=', 'users.id')
        ->select('leads.*', 'lead_statuses.name as status_name', 'users.name as assigned_name')
        ->orderByDesc('leads.created_at')
        ->limit(4)
        ->get();

    $recentTasks = DB::table('tasks')
        ->join('leads', 'tasks.lead_id', '=', 'leads.id')
        ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
        ->join('users', 'tasks.user_id', '=', 'users.id')
        ->select('tasks.*', 'leads.name as lead_name', 'task_statuses.name as status_name', 'users.name as assigned_name')
        ->orderByDesc('tasks.created_at')
        ->limit(4)
        ->get();

    $userTaskStats = DB::table('tasks')
        ->join('users', 'tasks.user_id', '=', 'users.id')
        ->select(
            'users.name',
            DB::raw('COUNT(tasks.id) as total_tasks'),
            DB::raw("SUM(CASE WHEN tasks.status_id = 3 THEN 1 ELSE 0 END) as completed_tasks")
        )
        ->groupBy('users.name')
        ->get();
@endphp

<!-- STATS -->
<div class="row mb-2">
    @foreach([
        ['label'=>'Users','value'=>$totalUsers,'icon'=>'bi-people','class'=>'stats-1'],
        ['label'=>'Leads','value'=>$totalLeads,'icon'=>'bi-person-plus','class'=>'stats-2'],
        ['label'=>'Tasks','value'=>$totalTasks,'icon'=>'bi-check2-square','class'=>'stats-3'],
        ['label'=>'Interactions','value'=>$totalInteractions,'icon'=>'bi-chat-dots','class'=>'stats-4'],
    ] as $stat)
    <div class="col-lg-3 col-md-6 mb-2">
        <div class="stats-card {{ $stat['class'] }}">
            <i class="bi {{ $stat['icon'] }}"></i>
            <h3>{{ $stat['value'] }}</h3>
            <p>{{ $stat['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<!-- RECENT DATA -->
<div class="row mb-3">

    <!-- LEADS -->
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">Recent Leads</div>
            <div class="card-body p-2">
                <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLeads as $lead)
                        <tr>
                            <td>{{ $lead->name }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $lead->status_name }}
                                </span>
                            </td>
                            <td>{{ $lead->assigned_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <!-- TASKS -->
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">Recent Tasks</div>
            <div class="card-body p-2">
                <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>User</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->assigned_name }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $task->status_name }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- USER TASK REPORT -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">User Task Report</div>
            <div class="card-body p-1">
                <div class="chart-container">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labels = {!! json_encode($userTaskStats->pluck('name')) !!};
const totalTasks = {!! json_encode($userTaskStats->pluck('total_tasks')) !!};
const completedTasks = {!! json_encode($userTaskStats->pluck('completed_tasks')) !!};

new Chart(document.getElementById('taskChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Total',
                data: totalTasks,
                backgroundColor: '#6366f1'
            },
            {
                label: 'Completed',
                data: completedTasks,
                backgroundColor: '#22c55e'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: { size: 10 }
                }
            }
        }
    }
});
</script>

<!-- STYLES -->
<style>

/* SMALL STATS CARDS */
.stats-card {
    border-radius: 10px;
    padding: 2px;
    color: white;
    text-align: center;
    transition: 0.2s;
}

.stats-card:hover {
    transform: translateY(-3px);
}

.stats-1 { background: linear-gradient(45deg, #6366f1, #8b5cf6); }
.stats-2 { background: linear-gradient(45deg, #22c55e, #4ade80); }
.stats-3 { background: linear-gradient(45deg, #f59e0b, #fbbf24); }
.stats-4 { background: linear-gradient(45deg, #ef4444, #f87171); }

.stats-card i {
    font-size: 1.4rem;
}

.stats-card h3 {
    margin: 5px 0;
    font-size: 18px;
}

.stats-card p {
    font-size: 12px;
    margin: 0;
}

/* CARD */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.card-header {
    background: transparent;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 600;
    font-size: 14px;
}

/* TABLE */
.table {
    font-size: 12.5px;
}

.table thead {
    background: #6366f1;
    color: white;
}

.table tbody tr:hover {
    background: #eef2ff;
}

/* BADGES */
.badge {
    border-radius: 6px;
    padding: 4px 7px;
    font-size: 11px;
}

/* SMALL CHART */
.chart-container {
    max-width: 600px;
    height: 220px;
    margin: auto;
}

</style>

@endsection