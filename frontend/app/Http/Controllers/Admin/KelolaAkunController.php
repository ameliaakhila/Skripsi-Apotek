<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

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
        // simplePaginate is more performant than paginate for large datasets
        $data = Cache::remember($cacheKey, 600, function () use ($perPage, $page) {
            return User::select('id_user', 'username', 'email', 'role')
                ->orderBy('id_user', 'asc')
                ->simplePaginate($perPage, ['*'], 'page', $page);
        });

        return view('admin.kelola-akun', compact('data'));
    }
}
