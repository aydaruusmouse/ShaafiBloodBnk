<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;    // if you have a Role model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Super admin can see all users
        if ($user->role && $user->role->name === 'admin') {
            $users = User::with('role')->paginate(15);
        } else {
            // Hospital admin can only see users from their hospital
            $users = User::where('hospital_id', $user->hospital_id)
                ->with('role')
                ->paginate(15);
        }
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Super admin can assign any role
        if ($user->role && $user->role->name === 'admin') {
            $roles = Role::pluck('name','id');
        } else {
            // Hospital admin can only assign hospital-specific roles (not super admin)
            $roles = Role::whereNotIn('name', ['admin'])->pluck('name','id');
        }
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role_id'  => 'required|exists:roles,id',
        ]);

        // Ensure hospital admin can't create super admin users
        if ($user->role && $user->role->name !== 'admin') {
            $role = Role::find($data['role_id']);
            if ($role && $role->name === 'admin') {
                abort(403, 'Hospital admins cannot create super admin users.');
            }
        }

        $userData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $data['role_id'],
        ];

        // Auto-assign hospital_id for hospital admins
        if ($user->role && $user->role->name !== 'admin') {
            $userData['hospital_id'] = $user->hospital_id;
        }

        User::create($userData);

        return redirect()->route('users.index')
                         ->with('success','User created.');
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $updateData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role_id'  => $data['role_id'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('users.index')
                             ->with('error', 'Cannot delete admin user.');
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
