<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'trader')->with('subscription')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('subscriptions', 'payments');
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) return back()->with('error', 'Cannot delete admin users.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
