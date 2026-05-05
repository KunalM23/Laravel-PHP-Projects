<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'CRM User')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --primary: #4f46e5; /* Consistent Indigo with Admin */
    --sidebar-bg: #1e293b;
    --sidebar-hover: #334155;
    --bg: #f1f5f9;
}

body {
    background: var(--bg);
    font-family: 'Segoe UI', sans-serif;
}

/* SIDEBAR */
.sidebar {
    background: linear-gradient(180deg, #1e293b, #0f172a);
    color: white;
    min-height: 110vh;
    display: flex;
    flex-direction: column;
}

.sidebar .logo {
    padding: 18px;
    background: #10b981; /* Distinct green for User side */
    text-align: center;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.75);
    padding: 12px 16px;
    font-size: 14px;
    display: flex;
    align-items: center;
    transition: 0.2s;
}

.sidebar .nav-link i { margin-right: 8px; }

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: var(--sidebar-hover);
    color: white;
}

/* MAIN CONTENT */
.main-content { min-height: 100vh; }

.navbar {
    background: white;
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
}

.content-area { padding: 20px; }

/* CARDS & TABLES */
.card {
    border: none;
    border-radius: 12px;
    background: white;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

.stats-card {
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    border-left: 4px solid var(--primary);
}

@media(max-width:768px){
    .sidebar { min-height: auto; }
}
</style>
</head>

<body>

@php
$loggedIn = session()->has('current_user_id');
@endphp

@if($loggedIn)

@php
$userName = DB::table('users')->where('id', session('current_user_id'))->value('name') ?? 'User';
@endphp

<div class="container-fluid p-0">
<div class="row g-0">

<!-- SIDEBAR -->
<div class="col-md-2">
<div class="sidebar shadow">

<div class="logo">
    <h5 class="text-white mb-0"><i class="bi bi-person-workspace"></i> My CRM</h5>
</div>

<nav class="nav flex-column">
    <a href="/user/dashboard" class="nav-link {{ request()->is('user/dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="/user/leads" class="nav-link {{ request()->is('user/leads*') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i> Leads
    </a>

    <a href="/user/tasks" class="nav-link {{ request()->is('user/tasks*') ? 'active' : '' }}">
        <i class="bi bi-check2-square"></i> Tasks
    </a>

    <a href="/user/interactions" class="nav-link {{ request()->is('user/interactions*') ? 'active' : '' }}">
        <i class="bi bi-chat"></i> Interactions
    </a>
</nav>

</div>
</div>

<!-- MAIN CONTENT -->
<div class="col-md-10">
<div class="main-content">

<!-- NAVBAR -->
<div class="navbar d-flex justify-content-between align-items-center shadow-sm">
    <h6 class="mb-0 text-primary fw-bold">@yield('page-title')</h6>
    
    <div class="d-flex align-items-center gap-3">
        <span class="fw-semibold small text-muted">
            <i class="bi bi-person-circle me-1"></i> {{ $userName }}
        </span>

        <button onclick="logout()" class="btn btn-sm btn-danger px-3 shadow-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </div>
</div>

<div class="content-area">
    @yield('content')
</div>

</div>
</div>

</div>
</div>

@else
@yield('content')
@endif

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function logout() {
    Swal.fire({
        title: 'Logout?',
        text: "Are you sure you want to end your session?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/session-logout';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif
});
</script>

@stack('scripts')

</body>
</html>