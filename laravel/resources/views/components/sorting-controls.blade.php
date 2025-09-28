@props([
    'action' => request()->url(),
    'sortOptions' => [],
    'currentSort' => 'created_at',
    'currentDirection' => 'desc',
    'totalCount' => 0,
    'showReset' => false
])

<div class="rounded-2xl border border-cyan-400/20 bg-slate-900/80 backdrop-blur-md p-4 shadow-lg">
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-cyan-300">Sort by:</span>

            <form method="GET" action="{{ $action }}" class="flex items-center gap-2" id="sortForm">
                <div class="relative">
                    <select name="sort" onchange="document.getElementById('sortForm').submit()"
                            class="px-3 py-2 pr-8 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 text-sm focus:border-cyan-400 focus:outline-none appearance-none">
                        @foreach($sortOptions as $value => $label)
                            <option value="{{ $value }}" {{ $currentSort === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute right-2 top-1/2 transform -translate-y-1/2 text-slate-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <button type="button" onclick="toggleSortDirection()"
                        class="p-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 text-cyan-400 transition-colors flex items-center gap-1"
                        title="{{ $currentDirection === 'asc' ? 'Ascending' : 'Descending' }}">
                    @if($currentDirection === 'asc')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                        <span class="text-xs hidden sm:block">ASC</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span class="text-xs hidden sm:block">DESC</span>
                    @endif
                </button>

                <input type="hidden" name="direction" value="{{ $currentDirection }}" id="sortDirection">

                @if($showReset)
                    <a href="{{ $action }}"
                       class="px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="text-sm text-slate-400 flex items-center gap-4">
            <span><span class="font-medium">{{ $totalCount }}</span> {{ Str::plural('item', $totalCount) }} found</span>
        </div>
    </div>
</div>

<script>
    function toggleSortDirection() {
        const directionInput = document.getElementById('sortDirection');
        const currentDirection = directionInput.value;
        directionInput.value = currentDirection === 'asc' ? 'desc' : 'asc';

        // Add loading state
        const eventsTable = document.getElementById('eventsTable');
        if (eventsTable) {
            eventsTable.style.opacity = '0.6';
            eventsTable.style.pointerEvents = 'none';
        }

        document.getElementById('sortForm').submit();
    }

    // Add loading state when sort dropdown changes
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.querySelector('select[name="sort"]');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const eventsTable = document.getElementById('eventsTable');
                if (eventsTable) {
                    eventsTable.style.opacity = '0.6';
                    eventsTable.style.pointerEvents = 'none';
                }
            });
        }
    });
</script>
