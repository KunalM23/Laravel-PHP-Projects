@extends('user.layouts.app')
@section('title', 'User Login')
@section('page-title', 'User Login')

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
                                <i class="bi bi-person-workspace text-success"></i>
                            </div>
                            <h3 class="fw-bold text-success">My CRM</h3>
                            <p class="text-muted small">Sign in to your workspace</p>
                        </div>

                        {{-- Error Handling[cite: 9] --}}
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

                        {{-- Login Form[cite: 9] --}}
                        <form method="POST" action="/user/login">
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

                            <div class="mb-2"> {{-- Adjusted margin for button below --}}
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

                            {{-- NEW: Forgot Password Button[cite: 8] --}}
                            <div class="text-end mb-4">
                                <a href="/password/reset" class="text-decoration-none small fw-bold text-success">
                                    Forgot Password?
                                </a>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-success btn-lg fw-bold py-2 shadow-sm">
                                    Sign In <i class="bi bi-box-arrow-in-right ms-2"></i>
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <span class="badge bg-light text-muted border py-2 px-3 fw-normal">
                                    <i class="bi bi-shield-check text-success me-1"></i> Personal CRM Workspace
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted small opacity-75">
                    <i class="bi bi-info-circle me-1"></i> Default: priya@example.com / rahul@example.com / password
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

    /* Card Styling[cite: 9] */
    .login-card {
        border-radius: 15px;
        background: #ffffff;
    }

    /* Brand Icon Container */
    .brand-icon-wrapper {
        width: 65px;
        height: 65px;
        background: #eafaf1;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }

    /* Form Styling[cite: 9] */
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
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Success Button Styling[cite: 9] */
    .btn-success {
        background: linear-gradient(135deg, #198754, #157347);
        border: none;
        border-radius: 8px;
        transition: transform 0.2s;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #157347, #146c43);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Alert Styling[cite: 9] */
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
</style>
@endsection