<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        $issues = IssueReport::with(['tenant','room','assignee'])->orderByDesc('created_at')->get();
        $staffs = User::where('role', 'staff')->orderBy('name')->get();
        return view('pages.admin.issues.index', compact('issues', 'staffs'));
    }

    public function assign(Request $request, IssueReport $issue)
    {
        $data = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $issue->update([
            'assigned_to' => $data['assigned_to'] ?? null,
            'status' => $data['assigned_to'] ? 'in_progress' : $issue->status,
        ]);
        return redirect()->back()->with('success', 'Petugas ditetapkan.');
    }

    public function setStatus(Request $request, IssueReport $issue)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);
        $issue->update(['status' => $data['status']]);
        return redirect()->back()->with('success', 'Status laporan diperbarui.');
    }
}

