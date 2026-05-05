@extends('admin.layouts.app')
@section('title', 'Admin Login')
@section('page-title', 'Admin Login')

@section('content')
<div class="login-wrapper vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card border-0 shadow-lg">
                    <div class="card-body p-5">
                        {{-- Branding Section --}}
                        <div class="text-center mb-4">
                            <div class="brand-icon-wrapper mb-3 mx-auto shadow-sm">
                                <i class="bi bi-building text-primary"></i>
                            </div>
                            <h3 class="fw-bold text-primary">CRM Admin</h3>
                            <p class="text-muted small">Sign in to admin panel</p>
                        </div>

                        {{-- Error Handling[cite: 10] --}}
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div class="small">
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Login Form[cite: 10] --}}
                        <form method="POST" action="/admin/login">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-muted">
                                    Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control bg-light border-start-0" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="Enter your email"
                                           required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold text-muted">
                                    Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control bg-light border-start-0" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold py-2 shadow-sm">
                                    Sign In <i class="bi bi-box-arrow-in-right ms-2"></i>
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <span class="badge bg-light text-muted border py-2 px-3 fw-normal">
                                    <i class="bi bi-shield-check text-primary me-1"></i> Secure CRM System
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted small opacity-75">
                    <i class="bi bi-info-circle me-1"></i> Default: admin@example.com / password
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Main Wrapper with subtle gray background */
    .login-wrapper {
        background-color: #f8f9fa;
    }

    /* Card Styling[cite: 10] */
    .login-card {
        border-radius: 15px;
        background: #ffffff;
    }

    /* Brand Icon Container using your primary blue color */
    .brand-icon-wrapper {
        width: 65px;
        height: 65px;
        background: #e7f1ff;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }

    /* Form Styling[cite: 10] */
    .input-group-text {
        border: 1px solid #e0e0e0;
        color: #6c757d;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        background: #fff !important;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Primary Button Styling[cite: 10] */
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border: none;
        border-radius: 8px;
        transition: transform 0.2s;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0b5ed7, #0a58ca);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Alert Styling[cite: 10] */
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
</style>
@endsection