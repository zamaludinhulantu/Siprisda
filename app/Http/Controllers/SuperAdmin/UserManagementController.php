<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('institution:id,name');
        $search = trim((string) $request->get('q'));
        $roleFilter = $request->get('role');
        $institutionFilter = $request->get('institution_id');
        $roles = array_keys($this->roleLabels());

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, $roles, true)) {
            $query->where('role', $roleFilter);
        }

        if ($institutionFilter) {
            $query->where('institution_id', $institutionFilter);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $roleSummary = User::selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');
        $institutions = Institution::orderBy('name')->get(['id', 'name']);

        return view('superadmin.users.index', [
            'users' => $users,
            'roleOptions' => $this->roleLabels(),
            'roleSummary' => $roleSummary,
            'institutions' => $institutions,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'institutionFilter' => $institutionFilter,
        ]);
    }

    public function create()
    {
        return view('superadmin.users.create', [
            'roleOptions' => $this->roleLabels(),
            'institutions' => Institution::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $roles = array_keys($this->roleLabels());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($roles)],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $institutionId = null;
        if (!empty($validated['institution_name'])) {
            $institution = Institution::firstOrCreate(['name' => trim($validated['institution_name'])]);
            $institutionId = $institution->id;
        }

        $plainPassword = $validated['password'] ?? Str::random(12);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'institution_id' => $institutionId,
            'password' => $plainPassword,
        ]);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Pengguna baru dibuat. Password sementara: {$plainPassword} (mohon segera diganti).");
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

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $newPassword = $validated['password'] ?? Str::random(12);
        $user->update(['password' => $newPassword]);

        return back()->with('success', "Password untuk {$user->email} disetel ulang. Password sementara: {$newPassword}");
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
