<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Display list of admins and couriers
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'courier', 'developer', 'blogger'])->latest();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $users = $query->paginate(15)->withQueryString();
        
        return view('admin.staff.index', compact('users'));
    }

    /**
     * Show form to create new user
     */
    public function create(Request $request)
    {
        return view('admin.staff.create');
    }

    /**
     * Store new admin or courier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,courier,developer,blogger',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'avatar' => $avatarPath,
            'is_active' => $request->has('is_active'),
        ]);

        $roleLabel = match($validated['role']) {
            'admin' => 'Admin',
            'developer' => 'Developer',
            'blogger' => 'Blogger',
            default => 'Kurir'
        };
        
        return redirect()->route('admin.staff.index')
            ->with('success', "{$roleLabel} berhasil ditambahkan.");
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        if (!in_array($user->role, ['admin', 'courier', 'developer', 'blogger'])) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'User tidak ditemukan.');
        }

        return view('admin.staff.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['admin', 'courier', 'developer', 'blogger'])) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:admin,courier,developer,blogger',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ];

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'is_active' => $request->has('is_active'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($userData);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        if (!in_array($user->role, ['admin', 'courier', 'developer', 'blogger'])) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'User tidak ditemukan.');
        }

        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}.");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if (!in_array($user->role, ['admin', 'courier', 'developer', 'blogger'])) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'User tidak ditemukan.');
        }

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Check if courier has active orders
        if ($user->role === 'courier' && $user->activeDeliveries()->count() > 0) {
            return back()->with('error', 'Kurir masih memiliki pengiriman aktif.');
        }

        // Delete avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
