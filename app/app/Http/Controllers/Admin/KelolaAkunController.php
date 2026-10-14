<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelolaAkunController extends Controller
{
    /**
     * Display paginated list of all users with role-based status
     * OPTIMIZED: Using simplePaginate for better performance, caching per page
     */
    public function index()
    {
        // ! OPTIMIZATION: Cache user list per page (600s = 10 min)
        $page = request('page', 1);
        $perPage = 25;
        $cacheKey = 'list_user_page_' . $page;

        // Query actual user data from database with pagination
        $data = Cache::remember($cacheKey, 600, function () use ($perPage, $page) {
            return User::select('id_user', 'username', 'email', 'role', 'status')
                ->orderBy('id_user', 'asc')
                ->simplePaginate($perPage, ['*'], 'page', $page);
        });

        return view('admin.kelola-akun', compact('data'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,apoteker,manajer',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        $this->clearUserCache();

        return redirect()->back()->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',id_user',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',id_user',
            'role' => 'required|in:admin,apoteker,manajer',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $user->update($validated);

        $this->clearUserCache();

        return redirect()->back()->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->clearUserCache();

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password berhasil direset.');
    }

    /**
     * Clear all user list caches
     */
    private function clearUserCache()
    {
        // For simplicity, clear all related caches. 
        // In a real app with many pages, you'd be more surgical.
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget('list_user_page_' . $i);
        }
    }
}
