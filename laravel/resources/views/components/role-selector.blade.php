{{-- Role Selector Component --}}
@props(['user', 'roleService'])

<div class="role-selector inline-block">
    @if($user->id === auth()->id())
        {{-- Current user cannot change their own role --}}
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleService->getRoleColor($user->role) }}">
            {{ $roleService->getRoleIcon($user->role) }} {{ ucfirst($user->role) }}
        </span>
    @else
        {{-- Role dropdown for other users --}}
        @if(count($roleService->getAvailableRoles($user->role)) > 0)
            <div class="flex items-center gap-2">
                {{-- Current role badge --}}
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-300 ring-1 ring-slate-600">
                    {{ $roleService->getRoleIcon($user->role) }} {{ ucfirst($user->role) }}
                </span>

                {{-- Change role dropdown --}}
                <div class="relative">
                    <select
                        class="role-change-select appearance-none bg-slate-800 border border-slate-600 rounded-md py-1 px-3 pr-8 text-sm leading-tight focus:outline-none focus:bg-slate-700 focus:border-cyan-400 text-slate-200"
                        data-user-id="{{ $user->id }}"
                        data-current-role="{{ $user->role }}"
                    >
                        <option value="" selected class="bg-slate-800 text-slate-400">Change to...</option>
                        @foreach($roleService->getAvailableRoles($user->role) as $availableRole)
                            <option value="{{ $availableRole }}" class="bg-slate-800 text-slate-200">
                                {{ $roleService->getRoleIcon($availableRole) }} {{ ucfirst($availableRole) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @else
            {{-- Static display when no role transitions available --}}
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-300 ring-1 ring-slate-600">
                {{ $roleService->getRoleIcon($user->role) }} {{ ucfirst($user->role) }}
            </span>
        @endif
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.role-change-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newRole = this.value;
            const currentRole = this.dataset.currentRole;

            // Skip if no role selected (placeholder option)
            if (!newRole || newRole === currentRole) {
                this.value = ''; // Reset to placeholder
                return;
            }

            // Show loading state
            this.disabled = true;
            this.style.opacity = '0.6';

            fetch(`/users/${userId}/role`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ role: newRole })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                        notification.textContent = data.message;
                        document.body.appendChild(notification);

                        // Remove notification after 3 seconds
                        setTimeout(() => notification.remove(), 3000);

                        // Refresh page to update role badge styling
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        // Silent error handling - just reset the dropdown
                        console.error('Role update failed:', data.error);
                        this.value = ''; // Reset to placeholder
                        this.disabled = false;
                        this.style.opacity = '1';
                    }
                })
                .catch(error => {
                    console.error('Network error:', error);
                    // Silent error handling
                    this.value = ''; // Reset to placeholder
                    this.disabled = false;
                    this.style.opacity = '1';
                });
        });
    });
});
</script>
