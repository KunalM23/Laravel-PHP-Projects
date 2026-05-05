<?php

use App\Http\Controllers\Api\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;



Route::get('/', function () {
    return view('login');
});
 
// 1. Show the Reset Page
Route::get('/password/reset', function() {
    return view('user.otp_reset');
});

// 2. Handle sending the OTP via PHPMailer
Route::post('/password/send-otp', [ForgotPasswordController::class, 'sendOtp']);

// 3. Handle verifying the OTP and changing the password
Route::post('/password/update', [ForgotPasswordController::class, 'resetPassword']);

// Admin login page
Route::get('/admin/login', function () {
    return view('admin.auth.login');
});

// User login page
Route::get('/user/login', function () {
    return view('user.auth.login');
});

Route::get('/login', function () {
    return view('login');
});

// Admin login route
Route::post('/admin/login', function (Request $request) {
    $user = DB::table('users')->where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return redirect('/admin/login')->with('error', 'Invalid credentials');
    }

    // Fetch Role
    $role = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('role_user.user_id', $user->id)
        ->select('roles.name')
        ->first();

    // Fetch Permissions
    $permissions = DB::table('permission_user')
        ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
        ->where('permission_user.user_id', $user->id)
        ->pluck('permissions.name')
        ->toArray();

    // Store in Session
    session([
        'current_user_id'  => $user->id,
        'user_role'        => $role ? $role->name : null,
        'user_permissions' => $permissions
    ]);

    // Role-based Access Control for Redirection
    if (session('user_role') === 'admin') {
        return redirect('/admin/dashboard');
    }

    return redirect('/admin/login')->with('error', 'Unauthorized access');
});

// User login route
Route::post('/user/login', function (Request $request) {
    $user = DB::table('users')->where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return redirect('/user/login')->with('error', 'Invalid credentials');
    }

    // Fetch Role
    $role = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('role_user.user_id', $user->id)
        ->select('roles.name')
        ->first();

    // Fetch Permissions
    $permissions = DB::table('permission_user')
        ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
        ->where('permission_user.user_id', $user->id)
        ->pluck('permissions.name')
        ->toArray();

    // Store in Session
    session([
        'current_user_id'  => $user->id,
        'user_role'        => $role ? $role->name : null,
        'user_permissions' => $permissions
    ]);

    // Role-based Access Control for Redirection
    if (session('user_role') === 'user') {
        return redirect('/user/dashboard');
    }

    return redirect('/user/login')->with('error', 'Unauthorized access');
});

// Admin dashboard with Role Verification
Route::get('/admin/dashboard', function () {
    if (!session('current_user_id') || session('user_role') !== 'admin') {
        return redirect('/admin/login');
    }
    return view('admin.dashboard');
});

// User dashboard with Role Verification
Route::get('/user/dashboard', function () {
    if (!session('current_user_id') || session('user_role') !== 'user') {
        return redirect('/user/login');
    }
    return view('user.dashboard');
});

// Admin CRUD Routes
Route::prefix('admin')->group(function () {
    Route::get('/leads', [App\Http\Controllers\Api\LeadController::class, 'index']);
    Route::post('/leads', [App\Http\Controllers\Api\LeadController::class, 'store']);
    Route::put('/leads/{id}', [App\Http\Controllers\Api\LeadController::class, 'update']);
    Route::delete('/leads/{id}', [App\Http\Controllers\Api\LeadController::class, 'destroy']);
    
    Route::get('/tasks', [App\Http\Controllers\Api\TaskController::class, 'index']);
    Route::post('/tasks', [App\Http\Controllers\Api\TaskController::class, 'store']);
    Route::put('/tasks/{id}', [App\Http\Controllers\Api\TaskController::class, 'update']);
    Route::put('/tasks/{id}/complete', [App\Http\Controllers\Api\TaskController::class, 'complete']);
    Route::delete('/tasks/{id}', [App\Http\Controllers\Api\TaskController::class, 'destroy']);
    
    Route::get('/interactions', [App\Http\Controllers\Api\InteractionController::class, 'index']);
    Route::post('/interactions', [App\Http\Controllers\Api\InteractionController::class, 'store']);
    Route::put('/interactions/{id}', [App\Http\Controllers\Api\InteractionController::class, 'update']);
    Route::delete('/interactions/{id}', [App\Http\Controllers\Api\InteractionController::class, 'destroy']);
    
    Route::get('/users', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\Api\UserController::class, 'store']);
    Route::match(['PUT', 'POST'], '/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::delete('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'destroy']);
    Route::put('/users/{id}/permissions', [App\Http\Controllers\Api\UserController::class, 'update']);
    
    Route::get('/users/roles', function () {
        $users = DB::table('users')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->select('users.*', 'designations.name as designation_name')
            ->orderByDesc('users.created_at')
            ->get();
        $designations = DB::table('designations')->get();
        return view('admin.users.roles', compact('users', 'designations'));
    });
    
    Route::put('/users/{id}/role', function (Request $request, $id) {
        DB::table('users')->where('id', $id)->update([
            'designation_id' => $request->designation_id,
            'status' => $request->status,
            'updated_at' => now()
        ]);

        DB::table('role_user')->where('user_id', $id)->delete();
        DB::table('role_user')->insert([
            'user_id' => $id,
            'role_id' => $request->role_id
        ]);

        return redirect('/admin/users')->with('success', 'User role updated successfully');
    });
});

// User CRUD Routes
Route::prefix('user')->group(function () {
    Route::get('/leads', [App\Http\Controllers\Api\LeadController::class, 'index']);
    Route::post('/leads', [App\Http\Controllers\Api\LeadController::class, 'store']);
    Route::put('/leads/{id}', [App\Http\Controllers\Api\LeadController::class, 'update']);
    Route::delete('/leads/{id}', [App\Http\Controllers\Api\LeadController::class, 'destroy']);
    
    Route::get('/tasks', [App\Http\Controllers\Api\TaskController::class, 'index']);
    Route::post('/tasks', [App\Http\Controllers\Api\TaskController::class, 'store']);
    Route::put('/tasks/{id}', [App\Http\Controllers\Api\TaskController::class, 'update']);
    Route::put('/tasks/{id}/complete', [App\Http\Controllers\Api\TaskController::class, 'complete']);
    Route::delete('/tasks/{id}', [App\Http\Controllers\Api\TaskController::class, 'destroy']);
    
    Route::get('/interactions', [App\Http\Controllers\Api\InteractionController::class, 'index']);
    Route::post('/interactions', [App\Http\Controllers\Api\InteractionController::class, 'store']);
    Route::put('/interactions/{id}', [App\Http\Controllers\Api\InteractionController::class, 'update']);
    Route::delete('/interactions/{id}', [App\Http\Controllers\Api\InteractionController::class, 'destroy']);
     Route::delete('/interactions/{id}', [App\Http\Controllers\Api\InteractionController::class, 'destroy']);
});

// Logout
Route::get('/session-logout', function () {
    session()->flush();
    return view('login');
});
