<?php
/*
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ── AUTH ──────────────────────────────────────────────────────────────────────
// POST /api/login    { email, password }
// POST /api/logout
Route::post('login', function (\Illuminate\Http\Request $req) {
    $user = DB::table('users')->where('email', $req->email)->first();
    if (!$user || !\Illuminate\Support\Facades\Hash::check($req->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    session(['current_user_id' => $user->id]);
    return response()->json(['message' => 'Login successful', 'user' => $user]);
});

Route::post('logout', function () {
    session()->flush();
    return response()->json(['message' => 'Logged out']);
});

// ── USERS ─────────────────────────────────────────────────────────────────────
// GET    /api/users
// POST   /api/users
// GET    /api/users/{id}
// PUT    /api/users/{id}
// DELETE /api/users/{id}
Route::apiResource('users', UserController::class);
Route::get('users-export-excel', [UserController::class, 'exportExcel']);
Route::get('users-export-pdf',   [UserController::class, 'exportPdf']);

// ── LEADS ─────────────────────────────────────────────────────────────────────
// GET    /api/leads              ?search= &status_id= &source_id= &assigned_to= &from_date= &to_date= &page=
// POST   /api/leads
// GET    /api/leads/{id}
// PUT    /api/leads/{id}
// DELETE /api/leads/{id}
Route::apiResource('leads', LeadController::class);
Route::get('leads-export-excel', [LeadController::class, 'exportExcel']);
Route::get('leads-export-pdf',   [LeadController::class, 'exportPdf']);

// ── INTERACTIONS ──────────────────────────────────────────────────────────────
// GET    /api/interactions       ?lead_id= &user_id= &interaction_type_id= &from_date= &to_date= &page=
// POST   /api/interactions
// GET    /api/interactions/{id}
// PUT    /api/interactions/{id}
// DELETE /api/interactions/{id}
Route::apiResource('interactions', InteractionController::class);
Route::get('interactions-export-excel', [InteractionController::class, 'exportExcel']);
Route::get('interactions-export-pdf',   [InteractionController::class, 'exportPdf']);

// ── TASKS ─────────────────────────────────────────────────────────────────────
// GET    /api/tasks              ?search= &status_id= &user_id= &lead_id= &priority= &from_date= &to_date= &page=
// POST   /api/tasks
// GET    /api/tasks/{id}
// PUT    /api/tasks/{id}
// DELETE /api/tasks/{id}
Route::apiResource('tasks', TaskController::class);
Route::get('tasks-export-excel', [TaskController::class, 'exportExcel']);
Route::get('tasks-export-pdf',   [TaskController::class, 'exportPdf']);
Route::get('tasks-reports',      [TaskController::class, 'reports']);

// ── LOOKUPS (dropdowns) ───────────────────────────────────────────────────────
// GET /api/lookups/sources
// GET /api/lookups/lead-statuses
// GET /api/lookups/interaction-types
// GET /api/lookups/task-statuses
// GET /api/lookups/designations
// GET /api/lookups/roles
// GET /api/lookups/users
// GET /api/lookups/leads
Route::prefix('lookups')->group(function () {
    Route::get('sources',           fn() => response()->json(DB::table('sources')->get()));
    Route::get('lead-statuses',     fn() => response()->json(DB::table('lead_statuses')->get()));
    Route::get('interaction-types', fn() => response()->json(DB::table('interaction_types')->get()));
    Route::get('task-statuses',     fn() => response()->json(DB::table('task_statuses')->get()));
    Route::get('designations',      fn() => response()->json(DB::table('designations')->get()));
    Route::get('roles',             fn() => response()->json(DB::table('roles')->get()));
    Route::get('users',             fn() => response()->json(DB::table('users')->select('id','name','email')->get()));
    Route::get('leads',             fn() => response()->json(DB::table('leads')->select('id','name','company')->get()));
});
*/