<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('pages.admin.users.index', [
            'users' => User::orderBy('name')->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('pages.admin.users.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);
        User::create($this->validatedData($request));

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureAdmin($request);

        return view('pages.admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin($request);
        $data = $this->validatedData($request, $user);

        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah role akun sendiri menjadi operator.');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdmin($request);

        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'operator'])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
