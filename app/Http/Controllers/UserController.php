<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-users', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('roles');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $total = $query->count();

            if ($request->has('order')) {
                $columns = ['id', 'name', 'email', 'phone', 'is_active'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $users = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => User::count(),
                'recordsFiltered' => $total,
                'data' => $users
            ]);
        }

        return view('users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        $user->load('roles', 'transactions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
            ], 400);
        }

        // Prevent deleting superadmin or admin users
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            $roleName = $user->hasRole('superadmin') ? 'superadmin' : 'admin';
            return response()->json([
                'success' => false,
                'message' => "User dengan role {$roleName} tidak dapat dihapus!"
            ], 403);
        }

        // Check if user has transactions
        $transactionCount = $user->transactions()->count();
        
        if ($transactionCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "User '{$user->name}' tidak dapat dihapus karena masih memiliki {$transactionCount} transaksi terkait."
            ], 400);
        }

        try {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users_' . date('Y-m-d_His') . '.xlsx');
    }
}
