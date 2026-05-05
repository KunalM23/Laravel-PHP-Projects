<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LeadController extends Controller
{
    /**
     * PRIVATE AI LOGIC: Updated for Gemini 3 Flash Preview & Interaction Support
     */
    private function getAiLeadScore($name, $company, $interactions = 0) {
        $apiKey = env('GEMINI_API_KEY');
        
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey;

        $prompt = "You are a sales expert. Evaluate this lead: Name: $name, Company: $company. 
                   Interactions: $interactions.
                   Assign a score (0-100) and a short 1-sentence reason.
                   Return ONLY JSON: {\"score\": 85, \"reason\": \"High value enterprise lead.\"}";

        try {
            
            $response = Http::withoutVerifying()->post($url, ["contents" => [["parts" => [["text" => $prompt]]]]]);
            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            
            $start = strpos($text, '{');
            $end = strrpos($text, '}');

            if ($start !== false && $end !== false) {
                $jsonStr = substr($text, $start, ($end - $start) + 1);
                return json_decode($jsonStr, true);
            }
            
            return ['score' => 0, 'reason' => 'AI format unreadable.'];
        } catch (\Exception $e) {
            return ['score' => 0, 'reason' => 'AI score calculation failed.'];
        }
    }

    /**
     * INDEX LOGIC
     */
    public function index(Request $request)
    {
        $userId = session('current_user_id');

        $userRole = DB::table('role_user')->where('user_id', $userId)->first();
        $isAdmin = ($userRole && $userRole->role_id == 1);

        $userPermission = DB::table('permissions')
            ->join('permission_user', 'permissions.id', '=', 'permission_user.permission_id')
            ->where('permission_user.user_id', $userId)
            ->value('name') ?? 'read';

        $query = DB::table('leads')
            ->join('lead_statuses', 'leads.status_id', '=', 'lead_statuses.id')
            ->join('users', 'leads.assigned_to', '=', 'users.id')
            ->select('leads.*', 'lead_statuses.name as status_name', 'users.name as assigned_name');

        if (!$isAdmin) {
            $query->where('leads.assigned_to', $userId);
        }

        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('leads.name', 'like', "%{$search}%")
                  ->orWhere('leads.email', 'like', "%{$search}%")
                  ->orWhere('leads.phone', 'like', "%{$search}%");
            });
        }

        if ($statusId = $request->query('status_id')) {
            $query->where('leads.status_id', $statusId);
        }

        $query->orderByDesc('leads.created_at');
        $leads = $query->get();

        if ($request->wantsJson()) {
            return response()->json($leads);
        }

        $sources = DB::table('sources')->get();
        $statuses = DB::table('lead_statuses')->get();
        $users = DB::table('users')->get();
        
        $viewPath = $isAdmin ? 'admin.leads.list' : 'user.leads.list';
        
        return view($viewPath, compact('leads', 'sources', 'statuses', 'users', 'userPermission'));
    }

    /**
     * STORE: Updated AI call only
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'source_id' => ['required', 'exists:sources,id'],
            'status_id' => ['required', 'exists:lead_statuses,id'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        // New leads have 0 interactions
        $aiData = $this->getAiLeadScore($validated['name'], $validated['company'] ?? 'Individual', 0);
        $validated['score'] = $aiData['score'] ?? 50;
        $validated['ai_analysis'] = $aiData['reason'] ?? 'New lead added.';

        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $leadId = DB::table('leads')->insertGetId($validated);
        $lead = DB::table('leads')->where('id', $leadId)->first();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Lead created successfully.', 'data' => $lead], 201);
        }

        return redirect('/admin/leads')->with('success', 'Lead added successfully');
    }

    public function show(Request $request, string $id)
    {
        $lead = DB::table('leads')->where('id', $id)->first();
        if (! $lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }
        if($request->wantsJson()){
            return response()->json($lead);
        }
        return view('admin.leads.show', compact('lead'));
    }

    /**
     * UPDATE: Added Interaction Count to AI scoring[cite: 5]
     */
    public function update(Request $request, string $id)
    {
        $lead = DB::table('leads')->where('id', $id)->first();
        if (! $lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'source_id' => ['sometimes', 'required', 'exists:sources,id'],
            'status_id' => ['sometimes', 'required', 'exists:lead_statuses,id'],
            'assigned_to' => ['sometimes', 'required', 'exists:users,id'],
        ]);

        // Dynamically count interactions for this specific lead[cite: 5]
        $interactions = DB::table('interactions')->where('lead_id', $id)->count();

        $aiData = $this->getAiLeadScore($validated['name'] ?? $lead->name, $validated['company'] ?? $lead->company, $interactions);
        $validated['score'] = $aiData['score'];
        $validated['ai_analysis'] = $aiData['reason'];
        $validated['updated_at'] = now();

        DB::table('leads')->where('id', $id)->update($validated);
        $updatedLead = DB::table('leads')->where('id', $id)->first();

        if($request->wantsJson()){
            return response()->json(['message' => 'Lead updated successfully.', 'data' => $updatedLead]);
        }

        return redirect('/admin/leads')->with('success', 'Lead updated successfully');
    }

    public function destroy(Request $request, string $id)
    {
        $deleted = DB::table('leads')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        if($request->wantsJson()){
            return response()->json(['message' => 'Lead deleted successfully.']);
        }

        return redirect('/admin/leads')->with('success', 'Lead deleted successfully');
    }

    public function exportExcel()
    {
        $rows = DB::table('leads')->orderByDesc('id')->get();
        $filename = 'leads_export_'.now()->format('Ymd_His').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Source ID', 'Status ID', 'Assigned To', 'Score', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->name, $row->email, $row->phone, $row->company, $row->source_id, $row->status_id, $row->assigned_to, $row->score, $row->created_at]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $rows = DB::table('leads')->orderByDesc('id')->get();
        return response()->view('exports.leads-pdf', ['rows' => $rows]);
    }
}
