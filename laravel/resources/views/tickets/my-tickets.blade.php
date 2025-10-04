<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 5px rgba(34, 211, 238, 0.5)' },
                            '100%': { boxShadow: '0 0 20px rgba(34, 211, 238, 0.8)' },
                        }
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .ticket-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(34, 211, 238, 0.5), transparent);
        }
        
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 0 15px rgba(34, 211, 238, 0.3);
        }
        
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .confirmed { background-color: rgb(52, 211, 153); }
        .pending { background-color: rgb(251, 191, 36); }
        .cancelled { background-color: rgb(248, 113, 113); }
        .paid { background-color: rgb(52, 211, 153); }
        .failed { background-color: rgb(248, 113, 113); }
        
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        
        .shine-effect::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(30deg);
            transition: all 0.6s;
            opacity: 0;
        }
        
        .shine-effect:hover::after {
            opacity: 1;
            top: -30%;
            left: -30%;
        }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(2) {
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100">
    <!-- Enhanced Background with floating elements -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(75%_60%_at_90%_0%,rgba(34,211,238,0.12),transparent_60%)]"></div>
        <div class="absolute inset-0 opacity-[0.05] [mask-image:linear-gradient(to_bottom,black,transparent)]">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        
        <!-- Floating elements -->
        <div class="absolute top-1/4 left-1/4 w-4 h-4 rounded-full bg-cyan-400/20 floating-element"></div>
        <div class="absolute top-1/3 right-1/4 w-6 h-6 rounded-full bg-purple-400/10 floating-element"></div>
        <div class="absolute bottom-1/4 left-1/3 w-5 h-5 rounded-full bg-blue-400/15 floating-element"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-10">
        <!-- Enhanced Header with improved styling -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div class="text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/50 border border-cyan-400/20 mb-3">
                    <i class="fas fa-ticket-alt text-cyan-300 text-xs"></i>
                    <p class="text-xs text-slate-300">Your Tickets</p>
                </div>
                <h1 class="text-4xl font-bold tracking-tight bg-gradient-to-r from-cyan-300 to-sky-300 bg-clip-text text-transparent">
                    My Tickets
                </h1>
                <p class="mt-2 text-slate-400 max-w-md">Manage and view all your event tickets in one place</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <a href="{{ route('events.index') }}"
                   class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-cyan-400/30 bg-gradient-to-r from-slate-800/80 to-slate-900/80 hover:from-slate-700/80 hover:to-slate-800/80 transition-all duration-300 shadow-lg shine-effect">
                    <i class="fas fa-calendar-alt text-cyan-300"></i>
                    <span>Browse Events</span>
                </a>
                <a href="{{ route('user.dashboard') }}"
                   class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-slate-800 to-slate-900 hover:from-slate-700 hover:to-slate-800 border border-cyan-400/20 transition-all duration-300 shadow-lg shine-effect">
                    <i class="fas fa-arrow-left text-cyan-300"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>

        @if($tickets->count() > 0)
            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <div class="rounded-2xl bg-gradient-to-br from-slate-800/60 to-slate-900/60 backdrop-blur-md border border-cyan-400/20 p-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Total Tickets</p>
                            <p class="text-2xl font-bold text-slate-100 mt-1">{{ $tickets->count() }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-cyan-500/10">
                            <i class="fas fa-ticket-alt text-cyan-300 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl bg-gradient-to-br from-slate-800/60 to-slate-900/60 backdrop-blur-md border border-cyan-400/20 p-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Confirmed</p>
                            <p class="text-2xl font-bold text-slate-100 mt-1">
                                {{ $tickets->where('status', 'confirmed')->count() }}
                            </p>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-500/10">
                            <i class="fas fa-check-circle text-emerald-300 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl bg-gradient-to-br from-slate-800/60 to-slate-900/60 backdrop-blur-md border border-cyan-400/20 p-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm">Total Spent</p>
                            <p class="text-2xl font-bold text-cyan-300 mt-1">
                                ${{ number_format($tickets->sum('total_price'), 2) }}
                            </p>
                        </div>
                        <div class="p-3 rounded-xl bg-sky-500/10">
                            <i class="fas fa-dollar-sign text-sky-300 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Ticket Cards -->
            <div class="space-y-6">
                @foreach($tickets as $ticket)
                    <article class="ticket-card rounded-2xl bg-gradient-to-br from-slate-900/80 via-slate-900/70 to-slate-800/80 backdrop-blur-md border border-cyan-400/20 shadow-xl p-6 relative overflow-hidden">
                        <!-- Corner accent -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-cyan-500/10 to-transparent rounded-bl-full"></div>
                        
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5 relative z-10">
                            <!-- Left: Event + meta -->
                            <div class="min-w-0 md:flex-1">
                                <a href="{{ route('events.show', $ticket->event->id) }}"
                                   class="group inline-flex items-center gap-3 mb-4">
                                    <div class="p-2 rounded-lg bg-cyan-500/10 group-hover:bg-cyan-500/20 transition">
                                        <i class="fas fa-music text-cyan-300"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-cyan-300 group-hover:text-cyan-200 transition truncate">
                                        {{ $ticket->event->title }}
                                    </h3>
                                    <svg class="h-4 w-4 text-cyan-300/70 group-hover:text-cyan-200 transition transform group-hover:translate-x-1"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 3.293a1 1 0 011.414 0l4 4a1
                                        1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586
                                        9H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0
                                        010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>

                                <dl class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4 transition hover:bg-slate-800/90">
                                        <dt class="text-slate-400 flex items-center gap-2">
                                            <i class="far fa-calendar text-cyan-300"></i>
                                            Event Date
                                        </dt>
                                        <dd class="mt-2 font-medium text-slate-200">
                                            {{ \Carbon\Carbon::parse($ticket->event->event_date)->format('M d, Y') }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4 transition hover:bg-slate-800/90">
                                        <dt class="text-slate-400 flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-cyan-300"></i>
                                            Venue
                                        </dt>
                                        <dd class="mt-2 font-medium text-slate-200">
                                            {{ $ticket->event->venue }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-slate-800/70 ring-1 ring-cyan-400/10 p-4 transition hover:bg-slate-800/90">
                                        <dt class="text-slate-400 flex items-center gap-2">
                                            <i class="fas fa-tag text-cyan-300"></i>
                                            Unit Price
                                        </dt>
                                        <dd class="mt-2 font-medium text-cyan-300">
                                            ${{ number_format($ticket->event->price, 2) }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-hashtag text-slate-400"></i>
                                        <span class="text-slate-400">Quantity:</span>
                                        <span class="font-semibold text-slate-200">{{ $ticket->quantity }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-receipt text-slate-400"></i>
                                        <span class="text-slate-400">Total Price:</span>
                                        <span class="font-semibold text-cyan-300">
                                            ${{ number_format($ticket->total_price, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-clock text-slate-400"></i>
                                        <span class="text-slate-400">Purchased:</span>
                                        <span class="font-semibold text-slate-200">
                                            {{ $ticket->purchase_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Status + actions -->
                            <div class="flex flex-col items-end gap-4 shrink-0">
                                <!-- Status badges with icons -->
                                <div class="flex flex-col gap-2">
                                    <span @class([
                                        'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium ring-1',
                                        'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20' => $ticket->status === 'confirmed',
                                        'bg-amber-500/10  text-amber-300  ring-amber-500/20'   => $ticket->status === 'pending',
                                        'bg-rose-500/10   text-rose-300   ring-rose-500/20'    => $ticket->status === 'cancelled',
                                        'bg-slate-600/20  text-slate-300  ring-slate-500/20'   => !in_array($ticket->status, ['confirmed','pending','cancelled']),
                                    ])>
                                        <span class="status-dot {{ $ticket->status }}"></span>
                                        {{ ucfirst($ticket->status) }}
                                    </span>

                                    <span @class([
                                        'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium ring-1',
                                        'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20' => $ticket->payment_status === 'paid',
                                        'bg-amber-500/10  text-amber-300  ring-amber-500/20'    => $ticket->payment_status === 'pending',
                                        'bg-rose-500/10   text-rose-300   ring-rose-500/20'     => $ticket->payment_status === 'failed',
                                        'bg-slate-600/20  text-slate-300  ring-slate-500/20'    => !in_array($ticket->payment_status, ['paid','pending','failed']),
                                    ])>
                                        <span class="status-dot {{ $ticket->payment_status }}"></span>
                                        Payment: {{ ucfirst($ticket->payment_status) }}
                                    </span>
                                </div>

                                <!-- Cancel button (uses named route via data-url) -->
                                @if($ticket->status !== 'cancelled' && $ticket->event->event_date > now())
                                    <button
                                        data-url="{{ route('tickets.cancel', $ticket->id) }}"
                                        onclick="cancelTicket(this)"
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 ring-1 ring-rose-300/30 shadow-sm transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-times"></i>
                                        Cancel Ticket
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <!-- Enhanced Empty state -->
            <div class="max-w-2xl mx-auto text-center py-20">
                <div class="mx-auto mb-6 inline-flex h-24 w-24 items-center justify-center rounded-2xl
                            bg-gradient-to-br from-cyan-500/15 to-sky-500/15 ring-1 ring-cyan-400/30 shadow-lg">
                    <span class="text-4xl">🎫</span>
                </div>
                <h2 class="text-3xl font-bold text-slate-200">No Tickets Yet</h2>
                <p class="mt-3 text-slate-400 max-w-md mx-auto">
                    You haven't purchased any tickets yet. Explore our events to find something you'll love.
                </p>
                <a href="{{ route('events.index') }}"
                   class="mt-8 inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500
                          hover:from-cyan-400 hover:to-sky-400 text-white font-medium shadow-lg transition-all duration-300 transform hover:scale-105 shine-effect">
                    <i class="fas fa-search"></i>
                    Browse Events
                </a>
            </div>
        @endif
    </div>

    <script>
        function cancelTicket(btn) {
            const url = btn.dataset.url;
            if (!url) return alert('Missing cancel URL.');

            if (!confirm('Are you sure you want to cancel this ticket? This action cannot be undone.')) return;

            const tokenTag = document.querySelector('meta[name="csrf-token"]');
            const token = tokenTag ? tokenTag.getAttribute('content') : '';

            // Add loading state to button
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
            btn.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(async (r) => {
                // If backend returns HTML (419/500), avoid JSON parse crash
                if (!r.ok) {
                    const text = await r.text();
                    throw new Error(`HTTP ${r.status} – ${text.slice(0, 200)}`);
                }
                return r.json();
            })
            .then((data) => {
                alert(data.message || 'Ticket cancelled successfully.');
                if (data.success) location.reload();
            })
            .catch((err) => {
                console.error(err);
                alert('Could not cancel ticket. Please refresh and try again.');
            })
            .finally(() => {
                // Reset button state
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
        
        // Add subtle animation to stats cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statsCards = document.querySelectorAll('.grid > div');
            statsCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
            
            // Add animation to ticket cards
            const ticketCards = document.querySelectorAll('.ticket-card');
            ticketCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 500 + (index * 100));
            });
        });
    </script>
</body>
</html>