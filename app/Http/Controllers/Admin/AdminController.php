<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Middleware protection (we'll add the route middleware later)
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
}