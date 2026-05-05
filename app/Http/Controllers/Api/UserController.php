<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Step 1: List users for table pages.
     * Step 2: Apply simple filters/sorting/pagination for learning.
     * Why this is needed: list pages become faster and easier to search.
     * Simpler alternative: return all rows with ->get().
     */
    public function index(Request $request)
    {
        $query = DB::table('users');

        // Step 1: Free-text search.
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Step 2: Exact filters.
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($gender = $request->query('gender')) {
            $query->where('gender', $gender);
        }
        if ($designationId = $request->query('designation_id')) {
            $query->where('designation_id', $designationId);
        }

        // Step 3: Date range filters.
        if ($fromDate = $request->query('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate = $request->query('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Step 4: Sorting (kept whitelisted for safety).
        $allowedSort = ['id', 'name', 'email', 'status', 'created_at'];
        $sortBy = $request->query('sort_by', 'id');
        $sortBy = in_array($sortBy, $allowedSort, true) ? $sortBy : 'id';
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        // Step 5: Pagination for list pages.
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $users = $query->get();

        // For API calls return JSON, for web calls return data
        if ($request->wantsJson()) {
            return response()->json($users);
        }

        // For web views, return data for view
        $designations = DB::table('designations')->get();
        return view('admin.users.list', compact('users', 'designations'));
    }

    /**
     * Step 1: Find one user by id.
     */
    public function show(string $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        
        if(request()->wantsJson()){
            return response()->json($user);
        }
        
        return redirect('/admin/dashboard')->with('success', 'User retrieved successfully');
    }

    /**
     * Step 1: Validate request.
     * Step 2: Hash password and save user.
     * Why this is needed: secure password + clean API response.
     * Simpler alternative: insert raw request directly (not recommended).
     */
    // --- In UserController.php ---

    /**
     * Modified store method to include fixed Role ID 2
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'gender' => ['nullable', 'in:Male,Female,other'], // Updated to match modal
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'status' => ['nullable', 'in:active,inactive'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'remarks' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/users', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        // 1. Insert User
        $userId = DB::table('users')->insertGetId($validated);

        // 2. NECESSARY ADDITION: Fixed role value 2 in role_user table
        DB::table('role_user')->insert([
            'user_id' => $userId,
            'role_id' => 2, 
        ]);

        $user = DB::table('users')->where('id', $userId)->first();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User created successfully.', 'data' => $user], 201);
        }
        return redirect('/admin/users')->with('success', 'User added successfully with default role');
    }

    /**
     * Modified update method to sync permissions
     */
    public function update(Request $request, string $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$id],
            'username' => ['sometimes', 'required', 'string', 'max:255', 'unique:users,username,'.$id],
            'password' => ['nullable', 'string', 'min:6'],
            'gender' => ['nullable', 'in:Male,Female,other'],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'status' => ['nullable', 'in:active,inactive'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'remarks' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'], // NECESSARY ADDITION
        ]);

        if ($request->hasFile('image')) {
            if (! empty($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('uploads/users', 'public');
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // NECESSARY ADDITION: Sync Permissions
        if ($request->has('permissions')) {
            DB::table('permission_user')->where('user_id', $id)->delete();
            foreach ($request->permissions as $perm_id) {
                DB::table('permission_user')->insert([
                    'user_id' => $id,
                    'permission_id' => $perm_id
                ]);
            }
            unset($validated['permissions']);
        }

        $validated['updated_at'] = now();
        DB::table('users')->where('id', $id)->update($validated);
        $updatedUser = DB::table('users')->where('id', $id)->first();
        
        if ($request->wantsJson()) {
            return response()->json(['message' => 'User updated successfully.', 'data' => $updatedUser]);
        }

        return redirect('/admin/users')->with('success', 'User updated successfully');
    }

    /**
     * Step 1: Delete user by id.
     */
    public function destroy(Request $request, string $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $deleted = DB::table('users')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        if ($user && ! empty($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User deleted successfully.']);
        }

        return redirect('/admin/users')->with('success', 'User deleted successfully');
    }

    /**
     * Step 1: Export users in CSV format.
     * Why this is needed: Excel opens CSV directly.
     * Simpler alternative: copy table manually.
     */
    public function exportExcel(Request $request)
    {
        $rows = DB::table('users')->orderByDesc('id')->get();
        $filename = 'users_export_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Username', 'Status', 'Gender', 'Designation ID', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->name, $row->email, $row->username, $row->status, $row->gender, $row->designation_id, $row->created_at]);
            }
            fclose($out);
        };
        

        if($request->wantsJson()){
            return response()->stream($callback, 200, $headers);
        }
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simple PDF-ready HTML export.
     * Why this is needed: browser can save this page as PDF.
     */
    public function exportPdf(Request $request)
    {
        $rows = DB::table('users')->orderByDesc('id')->get();
        
        if($request->wantsJson()){
            return response()->view('exports.users-pdf', ['rows' => $rows]);
        }
        
        return response()->view('exports.users-pdf', ['rows' => $rows]);
    }
}
