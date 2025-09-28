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

    public function __construct(
        UserRepository $userRepository,
        SortingService $sortingService,
        RoleManagementService $roleManagementService
    ) {
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

        // Get stats for dashboard cards
        $stats = [
            'total' => $users->count(),
            'admins' => $users->where('role', 'admin')->count(),
            'organizers' => $users->where('role', 'organizer')->count(),
            'users' => $users->where('role', 'user')->count(),
            'verified' => $users->where('email_verified', true)->count(),
            'unverified' => $users->where('email_verified', false)->count(),
            'new_this_week' => $users->filter(function ($user) {
                return $user->created_at >= now()->subWeek();
            })->count()
        ];

        // Get available roles for dropdowns
        $availableRoles = $this->roleManagementService->getAvailableRoles();

        return view('admin.users.index', compact(
            'users',
            'stats',
            'sortBy',
            'sortDirection',
            'sortOptions',
            'availableRoles'
        ));
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
        if (!$this->roleManagementService->canManageRoles()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $user = $this->userRepository->findById($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $result = $this->roleManagementService->changeUserRole($user, $request->role);

        return response()->json($result);
    }
}
