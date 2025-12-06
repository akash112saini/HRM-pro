<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordAuditLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['tenant', 'passwordAuditLogs']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by tenant
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->withCount('passwordAuditLogs')
            ->paginate(20);

        return view('super-admin.passwords.index', compact('users'));
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
            'notes' => 'nullable|string',
        ]);

        // Store the plain password temporarily for display
        $plainPassword = $validated['new_password'];

        // Update password
        $user->update([
            'password' => Hash::make($plainPassword),
        ]);

        // Log password change
        PasswordAuditLog::logChange(
            $user,
            auth()->user(),
            'force_reset',
            $validated['notes'] ?? 'Password reset by super admin'
        );

        // Log activity
        ActivityLog::logActivity(
            'reset_user_password',
            $user,
            null,
            null,
            "Reset password for user: {$user->email}"
        );

        // Store password in session for one-time display
        return back()->with([
            'success' => 'Password reset successfully!',
            'new_password' => $plainPassword,
            'password_user' => $user->name,
        ]);
    }

    public function viewAuditLog(User $user)
    {
        $auditLogs = PasswordAuditLog::where('user_id', $user->id)
            ->with('changedBy')
            ->latest('changed_at')
            ->paginate(20);

        return view('super-admin.passwords.audit', compact('user', 'auditLogs'));
    }

    /**
     * Generate a one-time login token
     */
    public function generateLoginToken(User $user)
    {
        $token = \App\Models\LoginToken::generate($user, 24);

        $loginUrl = route('login.token', ['token' => $token->token]);

        ActivityLog::logActivity(
            'generated_login_token',
            $user,
            null,
            null,
            "Generated login token for user: {$user->email}"
        );

        return back()->with([
            'success' => 'Login token generated successfully!',
            'login_url' => $loginUrl,
            'token_user' => $user->name,
            'expires_at' => $token->expires_at->format('M d, Y H:i:s'),
        ]);
    }

    /**
     * Impersonate a user
     */
    public function impersonate(User $user)
    {
        // Store original user ID in session
        session()->put('impersonating_from', auth()->id());
        session()->put('original_user_id', auth()->id());

        // Log the impersonation
        ActivityLog::logActivity(
            'impersonated_user',
            $user,
            null,
            null,
            "Super admin impersonating user: {$user->email}"
        );

        // Login as the target user
        auth()->login($user);

        return redirect('/')->with('success', "You are now logged in as {$user->name}");
    }

    /**
     * Stop impersonating
     */
    public function stopImpersonating()
    {
        $originalUserId = session()->get('original_user_id');

        if (!$originalUserId) {
            return redirect()->route('dashboard');
        }

        $originalUser = User::find($originalUserId);

        if ($originalUser) {
            auth()->login($originalUser);
        }

        session()->forget(['impersonating_from', 'original_user_id']);

        return redirect()->route('super-admin.passwords.index')->with('success', 'Stopped impersonating');
    }
}
