@extends('admin.layouts.app')
@section('title', 'CRM Login')
@section('page-title', 'CRM Login')

@section('content')

<div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="row w-100">
        <div class="col-lg-4 col-md-6 mx-auto">

            <div class="card shadow-sm login-card">

                <div class="card-body p-5">

                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-building text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="text-dark mb-1">CRM System</h4>
                        <p class="text-muted small">Choose your login type</p>
                    </div>

                    <!-- Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="row g-2">

                        <div class="col-6">
                            <a href="/admin/login" class="btn btn-primary w-100 btn-login">
                                <i class="bi bi-shield-check me-1"></i>
                                Admin
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="/user/login" class="btn btn-outline-secondary w-100 btn-login">
                                <i class="bi bi-person me-1"></i>
                                User
                            </a>
                        </div>

                    </div>

                    <hr class="my-4">

                    <!-- Info -->
                    <div class="text-center">
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Admin: admin@example.com / password<br>
                            <i class="bi bi-info-circle me-1"></i>
                            Kunal: kunalmajumdar71gmail.com / Minus@11<br>
                            <i class="bi bi-info-circle me-1"></i>
                            Priya: priya@example.com / password
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<style>
/* Card */
.login-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}

/* Buttons */
.btn-login {
    padding: 10px;
    font-weight: 500;
    border-radius: 6px;
}

/* Primary (softened) */
.btn-primary {
    background-color: #2f5d8a;
    border-color: #2f5d8a;
}

.btn-primary:hover {
    background-color: #264e73;
    border-color: #264e73;
}

/* Outline button */
.btn-outline-secondary {
    border-color: #ced4da;
    color: #495057;
}

.btn-outline-secondary:hover {
    background-color: #f1f3f5;
}

/* Remove heavy shadow */
.shadow-sm {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
}
</style>

@endsection