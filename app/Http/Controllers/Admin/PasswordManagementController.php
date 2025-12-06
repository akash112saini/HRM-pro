<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginToken;
use App\Models\ImpersonationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('tenant_id', Auth::user()->tenant_id)
            ->where('id', '!=', Auth::id()) // Exclude self
            ->with('employee');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20);

        return view('admin.password-management.index', compact('users'));
    }

    public function resetPassword(User $user)
    {
        // Generate random password
        $password = Str::random(12);

        $user->update([
            'password' => Hash::make($password)
        ]);

        return response()->json([
            'success' => true,
            'password' => $password
        ]);
    }

    public function generateLoginLink(User $user)
    {
        // Create login token
        $token = Str::random(64);

        LoginToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24)
        ]);

        return response()->json([
            'success' => true,
            'link' => route('login.token', $token),
            'expires_at' => now()->addHours(24)->format('d M Y H:i A')
        ]);
    }

    public function impersonate(User $user)
    {
        // Log the impersonation
        ImpersonationLog::create([
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'started_at' => now()
        ]);

        // Store original user ID in session
        session(['impersonator_id' => Auth::id()]);

        // Login as the user
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'You are now impersonating ' . $user->name);
    }

    public function stopImpersonation()
    {
        if (session()->has('impersonator_id')) {
            $impersonatorId = session('impersonator_id');

            // Find the log entry and update ended_at
            ImpersonationLog::where('admin_id', $impersonatorId)
                ->where('user_id', Auth::id())
                ->whereNull('ended_at')
                ->latest()
                ->first()
                    ?->update(['ended_at' => now()]);

            // Login back as original user
            Auth::loginUsingId($impersonatorId);
            session()->forget('impersonator_id');

            return redirect()->route('admin.employees.index')
                ->with('success', 'Impersonation ended. Welcome back!');
        }

        return redirect()->back();
    }
}
