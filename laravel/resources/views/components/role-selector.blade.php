@props([
    'user',
    'currentUserId' => null,
    'availableRoles' => [],
    'disabled' => false
])

@php
    $isCurrentUser = $currentUserId && $user->id === $currentUserId;
    $roleService = app(\App\Services\RoleManagementService::class);
@endphp

@if($isCurrentUser)
    <!-- Current user - cannot change own role -->
    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $roleService->getRoleBadgeClass($user->role) }}">
        {{ $roleService->getRoleIcon($user->role) }}
        {{ ucfirst($user->role) }} (You)
    </span>
@else
    <!-- Other users - allow role change -->
    <div class="flex items-center gap-2">
        <select
            onchange="changeUserRole({{ $user->id }}, this.value, this)"
            data-original="{{ $user->role }}"
            {{ $disabled ? 'disabled' : '' }}
            class="bg-slate-800 border border-slate-600 text-white text-xs rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-slate-700 transition-colors">
            @foreach($availableRoles as $roleKey => $roleName)
                <option value="{{ $roleKey }}" {{ $user->role === $roleKey ? 'selected' : '' }}>
                    {{ $roleService->getRoleIcon($roleKey) }} {{ $roleName }}
                </option>
            @endforeach
        </select>
    </div>
@endif
