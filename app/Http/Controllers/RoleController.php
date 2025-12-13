<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-roles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-roles', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::withCount('permissions', 'users');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where('name', 'like', "%{$search}%");
            }

            $total = $query->count();

            if ($request->has('order')) {
                $columns = ['id', 'name', 'permissions_count', 'users_count'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $roles = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => Role::count(),
                'recordsFiltered' => $total,
                'data' => $roles
            ]);
        }

        return view('roles.index');
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[1] ?? 'other';
        });
        
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan!');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[1] ?? 'other';
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);
        
        // Sync permissions
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting superadmin or admin role
        if (in_array($role->name, ['superadmin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => "Role '{$role->name}' tidak dapat dihapus karena merupakan role sistem!"
            ], 403);
        }

        // Prevent deleting role if it has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak dapat dihapus karena masih digunakan oleh user!'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus!'
        ]);
    }
}
