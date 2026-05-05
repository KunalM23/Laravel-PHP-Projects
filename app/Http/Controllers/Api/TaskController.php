<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Step 1: Build base list query.
     * Step 2: Add search, filters, sorting, pagination.
     */
    public function index(Request $request)
    {
        // 1. Get current session and check role from role_user junction[cite: 12]
        $currentUserId = session('current_user_id');
        $userRole = DB::table('role_user')
            ->where('user_id', $currentUserId)
            ->value('role_id');

        // 2. Build base query with joins to get readable names[cite: 11]
        $query = DB::table('tasks')
            ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
            ->leftJoin('leads', 'tasks.lead_id', '=', 'leads.id')
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->select(
                'tasks.*',
                'task_statuses.name as status_name',
                'leads.name as lead_name',
                'users.name as creator_name'
            );

        // 3. PERMISSION LOGIC: If not Admin, restrict to assigned tasks[cite: 12]
        if ($userRole != 1) {
            $query->where('tasks.user_id', $currentUserId);
        }

        // 4. Search & Filter logic (keeping existing filters)[cite: 11]
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tasks.title', 'like', "%{$search}%")
                    ->orWhere('tasks.description', 'like', "%{$search}%")
                    ->orWhere('leads.name', 'like', "%{$search}%");
            });
        }

        if ($statusId = $request->query('status_id')) {
            $query->where('tasks.status_id', $statusId);
        }

        // Admin can filter by any staff member[cite: 11, 12]
        if ($userId = $request->query('user_id')) {
            if ($userRole == 1) {
                $query->where('tasks.user_id', $userId);
            }
        }

        if ($leadId = $request->query('lead_id')) {
            $query->where('tasks.lead_id', $leadId);
        }

        if ($priority = $request->query('priority')) {
            $query->where('tasks.priority', $priority);
        }

        if ($fromDate = $request->query('from_date')) {
            $query->whereDate('tasks.due_date', '>=', $fromDate);
        }

        if ($toDate = $request->query('to_date')) {
            $query->whereDate('tasks.due_date', '<=', $toDate);
        }

        // 5. Sorting & Pagination[cite: 11]
        $allowedSort = ['id', 'title', 'priority', 'due_date', 'created_at'];
        $sortBy = $request->query('sort_by', 'id');
        $sortBy = in_array($sortBy, $allowedSort, true) ? "tasks.{$sortBy}" : 'tasks.id';
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;
        
        if($request->wantsJson()){
            return response()->json($query->paginate($perPage));
        }
        
        // 6. Data for View[cite: 11]
        $tasks = $query->paginate($perPage);
        $leads = DB::table('leads')->get();
        $statuses = DB::table('task_statuses')->get();
        
        // Staff list for filters (Exclude the main Admin ID 1)[cite: 12]
        $staff = DB::table('users')->where('id', '!=', 1)->get();

        // 7. Dynamic View Path based on Role[cite: 12]
        $viewPath = ($userRole == 1) ? 'admin.tasks.list' : 'user.tasks.list';

        return view($viewPath, compact('tasks', 'leads', 'statuses', 'staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'lead_id' => ['required', 'exists:leads,id'],
            'user_id' => ['required', 'exists:users,id'],
            'status_id' => ['required', 'exists:task_statuses,id'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $taskId = DB::table('tasks')->insertGetId($validated);
        $task = DB::table('tasks')->where('id', $taskId)->first();

        if($request->wantsJson()){
            return response()->json([
                'message' => 'Task created successfully.',
                'data' => $task,
            ], 201);
        }
        
        return redirect('/admin/tasks')->with('success', 'Task created successfully');
    }

    public function show(Request $request, string $id)
    {
        $task = DB::table('tasks')->where('id', $id)->first();
        if (! $task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }
        
        if($request->wantsJson()){
            return response()->json($task);
        }
        
        return redirect('/admin/tasks')->with('success', 'Task retrieved successfully');
    }

    public function update(Request $request, string $id)
    {
        $task = DB::table('tasks')->where('id', $id)->first();
        if (! $task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'lead_id' => ['sometimes', 'required', 'exists:leads,id'],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'status_id' => ['sometimes', 'required', 'exists:task_statuses,id'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['updated_at'] = now();
        DB::table('tasks')->where('id', $id)->update($validated);
        $updatedTask = DB::table('tasks')->where('id', $id)->first();

        if($request->wantsJson()){
            return response()->json([
                'message' => 'Task updated successfully.',
                'data' => $updatedTask,
            ]);
        }
        
        return redirect('/admin/tasks')->with('success', 'Task updated successfully');
    }

    public function destroy(Request $request, string $id)
    {
        $deleted = DB::table('tasks')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        if($request->wantsJson()){
            return response()->json(['message' => 'Task deleted successfully.']);
        }
        
        return redirect('/admin/tasks')->with('success', 'Task deleted successfully');
    }

    /**
     * Task reports for admin/user dashboard.
     */
    public function reports(Request $request)
    {
        $userId = $request->query('user_id');
        $baseQuery = DB::table('tasks');
        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }

        $total = (clone $baseQuery)->count();
        $completed = (clone $baseQuery)->where('status_id', 2)->count();
        $pending = (clone $baseQuery)->where('status_id', 1)->count();
        $inProgress = (clone $baseQuery)->whereNotIn('status_id', [1, 2])->count();

        $byUser = DB::table('tasks')
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $byDate = DB::table('tasks')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        if($request->wantsJson()){
            return response()->json([
                'summary' => [
                    'total' => $total,
                    'completed' => $completed,
                    'pending' => $pending,
                    'in_progress' => $inProgress,
                ],
                'user_wise' => $byUser,
                'date_wise' => $byDate,
            ]);
        }
        
        return redirect('/admin/tasks')->with('success', 'Tasks reports retrieved successfully');
    }

    /**
     * CSV export (opens in Excel).
     */
    public function exportExcel()
    {
        $rows = DB::table('tasks')->orderByDesc('id')->get();
        $filename = 'tasks_export_'.now()->format('Ymd_His').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Title', 'Lead ID', 'User ID', 'Status ID', 'Priority', 'Due Date', 'Description', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->title, $row->lead_id, $row->user_id, $row->status_id, $row->priority, $row->due_date, $row->description, $row->created_at]);
            }
            fclose($out);
        };

        if(request()->wantsJson()){
            return response()->stream($callback, 200, $headers);
        }
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $rows = DB::table('tasks')->orderByDesc('id')->get();
        
        if(request()->wantsJson()){
            return response()->view('exports.tasks-pdf', ['rows' => $rows]);
        }
        
        return response()->view('exports.tasks-pdf', ['rows' => $rows]);
    }
}
