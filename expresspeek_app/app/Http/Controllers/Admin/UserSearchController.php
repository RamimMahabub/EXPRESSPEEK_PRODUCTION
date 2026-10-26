<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $role = $request->query('role');
        $query = $request->query('query');

        if (!$role || !$query) {
            return response()->json([]);
        }

        $users = User::query()
            ->when($role, function ($q, $role) {
                return $q->role($role);
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($users);
    }
}
