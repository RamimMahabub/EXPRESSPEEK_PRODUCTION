<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $role  = $request->query('role', 'all');
        $users = $role !== 'all'
            ? $this->userService->getUsersByRole($role)
            : User::with('roles')->latest()->paginate(20);

        $stats = $this->userService->getUserStats();

        return view('admin.users.index', compact('users', 'role', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,customer,agent',
        ]);

        $user = $this->userService->createUser($validated, $validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created successfully.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,customer,agent',
        ]);

        $user->update(['name' => $validated['name'], 'email' => $validated['email']]);
        $this->userService->changeRole($user, $validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
