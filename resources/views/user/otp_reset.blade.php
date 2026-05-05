@extends('user.layouts.app')
@section('title', 'Reset Password')

@section('content')
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row w-100">
        <div class="col-lg-4 col-md-6 mx-auto">
            <div class="card shadow-sm login-card border-0">
                <div class="card-body p-4">
                    
                    <div class="text-center mb-3">
                        <div class="mb-2 mx-auto d-flex align-items-center justify-content-center" 
                             style="width: 55px; height: 55px; background: #eafaf1; border-radius: 12px;">
                            <i class="bi bi-shield-lock text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="fw-bold text-success mb-1">Reset Password</h4>
                        <p class="text-muted small mb-0">Follow the steps to recover your account</p>
                    </div>

                    <form action="/password/send-otp" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light" placeholder="name@example.com" value="{{ old('email') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-envelope me-1"></i> Send OTP
                        </button>
                    </form>

                    <hr class="my-3 opacity-25">

                    <form id="resetForm" action="/password/update" method="POST">
                        @csrf
                        @php
                            $latestOtp = \DB::table('otps')->latest()->first();
                            $emailFromDb = $latestOtp ? $latestOtp->email : '';
                        @endphp
                        <input type="hidden" name="email" value="{{ $emailFromDb }}">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">Enter 6-Digit OTP</label>
                            <input type="text" name="otp" class="form-control bg-light text-center fw-bold" placeholder="000000" maxlength="6" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">New Password</label>
                            <input type="password" name="password" id="pass" class="form-control bg-light" placeholder="••••••" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="conf" class="form-control bg-light" placeholder="••••••" required>
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100 py-2 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Update Password
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/user/login" class="text-decoration-none small text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        @endif

        @if($errors->any())
            Swal.fire({ icon: 'error', title: 'Oops...', html: '{!! implode("<br>", $errors->all()) !!}', confirmButtonColor: '#198754' });
        @endif

        // MODIFICATION: Client-side Match Check[cite: 7]
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            if (document.getElementById('pass').value !== document.getElementById('conf').value) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Mismatch', text: 'Passwords do not match!', confirmButtonColor: '#198754' });
            }
        });
    });
</script>

<style>
.login-card { border-radius: 12px; background: #ffffff; }
.form-control { border: 1px solid #e0e0e0; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; }
.form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25); }
.btn-success { background: linear-gradient(135deg, #198754, #157347); border: none; border-radius: 6px; }
.btn-outline-success { color: #198754; border-color: #198754; border-radius: 6px; }
.btn-outline-success:hover { background-color: #198754; color: #fff; }
</style>
@endsection