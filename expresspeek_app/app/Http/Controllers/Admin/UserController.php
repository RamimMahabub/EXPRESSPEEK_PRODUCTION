<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request)
    {
        $role = $request->query('role', 'all');
        $users = $role !== 'all'
            ? $this->userService->getUsersByRole($role)
            : User::with('roles')
                ->where('email', 'not like', '%@expresspeek.local')
                ->latest()
                ->paginate(20);

        $stats = $this->userService->getUserStats();

        return view('admin.users.index', compact('users', 'role', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function show(User $user)
    {
        $user->loadMissing('roles');
        
        $shipments = $user->isAgent() 
            ? $user->assignedShipments()->latest()->paginate(10)
            : $user->shipments()->latest()->paginate(10);

        return view('admin.users.show', compact('user', 'shipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_business' => 'boolean',
            'company_name' => 'nullable|string|max:255|required_if:is_business,1,true',
            'email' => 'required|email|unique:users,email',
            'phone_country_code' => ['required', 'string', 'regex:/^\+[1-9]\d{0,3}$/'],
            'phone' => ['required', 'string', 'regex:/^[0-9\s().-]{6,20}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,customer,agent',
        ]);

        $validated['phone'] = $this->validatedPhone($validated);

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
            'name' => 'required|string|max:255',
            'is_business' => 'boolean',
            'company_name' => 'nullable|string|max:255|required_if:is_business,1,true',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone_country_code' => ['required', 'string', 'regex:/^\+[1-9]\d{0,3}$/'],
            'phone' => ['required', 'string', 'regex:/^[0-9\s().+-]{6,24}$/'],
            'role' => 'required|in:admin,customer,agent',
        ]);

        $validated['phone'] = $this->validatedPhone($validated, $user);

        $user->update([
            'name' => $validated['name'],
            'is_business' => $validated['is_business'] ?? false,
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'],
            'phone_country_code' => $validated['phone_country_code'],
            'phone' => $validated['phone'],
        ]);
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

    private function validatedPhone(array $data, ?User $except = null): string
    {
        $phone = User::normalizePhone($data['phone_country_code'], $data['phone']);

        if (! preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            throw ValidationException::withMessages([
                'phone' => 'Enter a valid mobile number including the correct country code.',
            ]);
        }

        $query = User::query()->where('phone', $phone);
        if ($except) {
            $query->whereKeyNot($except->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'This phone number is already connected to another user.',
            ]);
        }

        return $phone;
    }
}
