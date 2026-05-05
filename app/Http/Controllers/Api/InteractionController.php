<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InteractionController extends Controller
{
    /**
     * List interactions with beginner-friendly filters.
     */
    public function index(Request $request)
    {
        $userId = session('current_user_id');

        // Identify if user is Admin (Role ID 1) based on role_user seeder
        $userRole = DB::table('role_user')
            ->where('user_id', $userId)
            ->first();
        
        $isAdmin = ($userRole && $userRole->role_id == 1);

        // Fetch permission name (full_access, read, write) from permission_user seeder
        // Defaulting to 'read' if not found
        $userPermission = DB::table('permissions')
            ->join('permission_user', 'permissions.id', '=', 'permission_user.permission_id')
            ->where('permission_user.user_id', $userId)
            ->value('name') ?? 'read';

        // Base query with joins to show names instead of just IDs[cite: 13, 14]
        $query = DB::table('interactions')
            ->join('leads', 'interactions.lead_id', '=', 'leads.id')
            ->join('interaction_types', 'interactions.interaction_type_id', '=', 'interaction_types.id')
            ->join('users', 'interactions.user_id', '=', 'users.id')
            ->select('interactions.*','leads.name as lead_name','interaction_types.name as type_name','users.name as user_name');

        // Data Isolation: Only admins see all interactions; users see their own[cite: 15]
        if (!$isAdmin) {
            $query->where('interactions.user_id', $userId);
        }

        // Apply filters (from original logic)[cite: 14]
        if ($leadId = $request->query('lead_id')) {
            $query->where('interactions.lead_id', $leadId);
        }
        if ($typeId = $request->query('interaction_type_id')) {
            $query->where('interactions.interaction_type_id', $typeId);
        }
        if ($fromDate = $request->query('from_date')) {
            $query->whereDate('interactions.interaction_date', '>=', $fromDate);
        }

        $query->orderByDesc('interactions.interaction_date');
        $interactions = $query->get();

        // API Handling[cite: 13, 14]
        if ($request->wantsJson()) {
            return response()->json($interactions);
        }

        // Fetch data for dropdowns/modals in the web view
        $leads = $isAdmin ? DB::table('leads')->get() : DB::table('leads')->where('assigned_to', $userId)->get();
        $types = DB::table('interaction_types')->get();
        $users = DB::table('users')->get();

        // Route view based on role to fix sidebar/layout issues[cite: 13, 15]
        $viewPath = $isAdmin ? 'admin.interactions.list' : 'user.interactions.list';

        return view($viewPath, compact('interactions', 'leads', 'types', 'users', 'userPermission', 'userId', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'user_id' => ['required', 'exists:users,id'],
            'interaction_type_id' => ['required', 'exists:interaction_types,id'],
            'interaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $interactionId = DB::table('interactions')->insertGetId($validated);
        $interaction = DB::table('interactions')->where('id', $interactionId)->first();

        if($request->wantsJson()){
            return response()->json([
                'message' => 'Interaction created successfully.',
                'data' => $interaction,
            ], 201);
        }
        
        return redirect('/admin/interactions')->with('success', 'Interaction created successfully');
    }

    public function show(Request $request, string $id)
    {
        $interaction = DB::table('interactions')->where('id', $id)->first();
        if (! $interaction) {
            return response()->json(['message' => 'Interaction not found.'], 404);
        }
        
        if($request->wantsJson()){
            return response()->json($interaction);
        }
        
        return view('admin.interactions.show', compact('interaction'));
    }

    public function update(Request $request, string $id)
    {
        $interaction = DB::table('interactions')->where('id', $id)->first();
        if (! $interaction) {
            return response()->json(['message' => 'Interaction not found.'], 404);
        }

        $validated = $request->validate([
            'lead_id' => ['sometimes', 'required', 'exists:leads,id'],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'interaction_type_id' => ['sometimes', 'required', 'exists:interaction_types,id'],
            'interaction_date' => ['sometimes', 'required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['updated_at'] = now();
        DB::table('interactions')->where('id', $id)->update($validated);
        $updatedInteraction = DB::table('interactions')->where('id', $id)->first();

        if($request->wantsJson()){
            return response()->json([
                'message' => 'Interaction updated successfully.',
                'data' => $updatedInteraction,
            ]);
        }
        
        return redirect('/admin/interactions')->with('success', 'Interaction updated successfully');
    }

    public function destroy(Request $request, string $id)
    {
        $deleted = DB::table('interactions')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['message' => 'Interaction not found.'], 404);
        }

        if($request->wantsJson()){
            return response()->json(['message' => 'Interaction deleted successfully.']);
        }
        
        return redirect('/admin/interactions')->with('success', 'Interaction deleted successfully');
    }

    /**
     * Excel-friendly CSV export.
     */
    public function exportExcel()
    {
        $rows = DB::table('interactions')->orderByDesc('id')->get();
        $filename = 'interactions_export_'.now()->format('Ymd_His').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Lead ID', 'User ID', 'Type ID', 'Interaction Date', 'Notes', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->lead_id, $row->user_id, $row->interaction_type_id, $row->interaction_date, $row->notes, $row->created_at]);
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
        $rows = DB::table('interactions')->orderByDesc('id')->get();
        if(request()->wantsJson()){
            return response()->view('exports.interactions-pdf', ['rows' => $rows]);
        }
        
        return response()->view('exports.interactions-pdf', ['rows' => $rows]);
    }
}
