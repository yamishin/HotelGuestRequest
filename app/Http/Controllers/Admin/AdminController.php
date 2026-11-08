<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ServiceRequest; // 👈 THIS WAS MISSING
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Middleware protection
    public function __construct()
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized Access. Admins Only.');
        }
    }

    // List all users
    public function dashboard()
    {
        $supervisors = User::where('role', 'supervisor')->orderBy('department')->get();
        $staff = User::where('role', 'staff')->orderBy('department')->get();
        
        return view('dashboards.admin.index', compact('supervisors', 'staff'));
    }

    // Show form to add a supervisor
    public function createSupervisor()
    {
        return view('dashboards.admin.create_supervisor');
    }

    // Save new supervisor
    public function storeSupervisor(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:30',
            'department' => 'required|in:Front Desk,Housekeeping,Maintenance,IT',
        ]);

        User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make('password'),
            'phone'      => $validated['phone'] ?? null,
            'role'       => 'supervisor',
            'department' => $validated['department'],
            'status'     => 'active',
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', "Supervisor {$validated['name']} created for {$validated['department']}. Default password is 'password'.");
    }

    // Toggle Supervisor Status (Activate/Deactivate)
    public function toggleStatus(User $user)
    {
        if ($user->role !== 'supervisor') abort(404);
        
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);
        
        return back()->with('success', "{$user->name} is now {$newStatus}.");
    }

    /* ============================================================
     |  GLOBAL REQUEST HISTORY (Admin)
     ============================================================ */
    public function history(Request $request)
    {
        $query = ServiceRequest::with(['stay.guest', 'stay.room', 'assignee', 'router', 'resolver']);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('request_code', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhereHas('stay.room', fn($r) => $r->where('room_number', 'like', "%{$s}%"))
                  ->orWhereHas('stay.guest', function ($g) use ($s) {
                      $g->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by Department
        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboards.admin.history', compact('requests'));
    }

        /* ============================================================
     |  GLOBAL REPORTS (Admin)
     ============================================================ */
    public function reports()
    {
        // Get all requests grouped by department for summary
        $departments = ['Front Desk', 'Housekeeping', 'Maintenance', 'IT'];
        $reportData = [];

        foreach ($departments as $dept) {
            $total = ServiceRequest::where('department', $dept)->count();
            $resolved = ServiceRequest::where('department', $dept)->where('status', 'resolved')->count();
            $unresolved = ServiceRequest::where('department', $dept)->whereIn('status', ['rejected', 'unavailable'])->count();
            
            $avgTime = ServiceRequest::where('department', $dept)
                ->where('status', 'resolved')
                ->whereNotNull('completed_at')
                ->whereNotNull('created_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 60) as avg_time')
                ->value('avg_time');

            $reportData[$dept] = [
                'total' => $total,
                'resolved' => $resolved,
                'unresolved' => $unresolved,
                'avg_time' => $avgTime ? round($avgTime) . ' mins' : '—',
            ];
        }

        return view('dashboards.admin.reports', compact('reportData'));
    }
}