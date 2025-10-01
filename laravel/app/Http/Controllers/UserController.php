<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Services\SortingService;
use App\Services\RoleManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepository;
    protected $sortingService;
    protected $roleManagementService;

    public function __construct(UserRepository $userRepository, SortingService $sortingService, RoleManagementService $roleManagementService)
    {
        $this->userRepository = $userRepository;
        $this->sortingService = $sortingService;
        $this->roleManagementService = $roleManagementService;
    }

    /**
     * Display a listing of users (Admin only)
     */
    public function index(Request $request)
    {
        // Check admin access
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin privileges required.');
        }
        // Validate and clean sorting parameters
        $sortParams = $this->sortingService->validateUserSortParameters(
            $request->get('sort'),
            $request->get('direction')
        );

        // Get users with sorting
        $users = $this->userRepository->getAllWithSorting(
            $sortParams['sort_by'],
            $sortParams['direction']
        );

        // Get some basic stats for dashboard
        $stats = [
            'total' => $users->count(),
            'admins' => $this->userRepository->countByRole('admin'),
            'organizers' => $this->userRepository->countByRole('organizer'),
            'users' => $this->userRepository->countByRole('user'),
            'recent' => $this->userRepository->getRecentUsers()->count()
        ];

        return view('admin.users.index', [
            'users' => $users,
            'sortBy' => $sortParams['sort_by'],
            'sortDirection' => $sortParams['direction'],
            'sortOptions' => $this->sortingService->getUserSortOptions(),
            'isDefaultSort' => $this->sortingService->isDefaultSort($sortParams['sort_by'], $sortParams['direction']),
            'stats' => $stats,
            'roleService' => $this->roleManagementService
        ]);
    }

    /**
     * Show user details (Admin only)
     */
    public function show(int $id)
    {
        // Check admin access
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user = $this->userRepository->findById($id);

        if (!$user) {
            abort(404, 'User not found.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update user role (Admin only)
     */
    public function updateRole(Request $request, int $id)
    {
        // Check admin access
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'role' => 'required|in:user,organizer,admin'
        ]);

        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $this->roleManagementService->changeUserRole($user, $request->role, Auth::user());

            return response()->json([
                'success' => true,
                'message' => "User role updated to {$request->role}",
                'new_role' => $request->role
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
