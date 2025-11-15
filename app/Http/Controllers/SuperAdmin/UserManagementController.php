<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('institution:id,name');
        $search = trim((string) $request->get('q'));

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('superadmin.users.index', [
            'users' => $users,
            'roleOptions' => $this->roleLabels(),
            'search' => $search,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $roles = array_keys($this->roleLabels());

        $validated = $request->validate([
            'role' => ['required', Rule::in($roles)],
        ]);

        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            abort(403, 'Tidak dapat mengubah role super admin lain.');
        }

        if ($user->id === $request->user()->id && $validated['role'] !== 'superadmin') {
            return back()->with('error', 'Tidak dapat menurunkan role akun Anda sendiri.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'Role pengguna diperbarui.');
    }

    protected function roleLabels(): array
    {
        return [
            'superadmin' => 'Super Admin (akses penuh)',
            'admin' => 'Admin Bappeda',
            'kesbangpol' => 'Kesbangpol',
            'user' => 'Peneliti',
        ];
    }
}
