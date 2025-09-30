{{-- Real-time Ticket Availability Component --}}
@props(['event'])

<div class="ticket-availability-widget" data-event-id="{{ $event->id }}">
    <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
        <h3 class="text-lg font-semibold text-white mb-3">{{ $event->title }}</h3>

        {{-- Availability Status --}}
        <div class="availability-status mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-300">Available Tickets:</span>
                <span class="available-count text-xl font-bold text-cyan-400">
                    {{ $event->available_tickets }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-slate-700 rounded-full h-2">
                <div class="availability-bar bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full transition-all duration-500"
                     style="width: {{ $event->availability_percentage }}%"></div>
            </div>

            <div class="flex justify-between text-xs text-slate-400 mt-1">
                <span>{{ $event->tickets_sold }} sold</span>
                <span>{{ $event->total_tickets }} total</span>
            </div>
        </div>

        {{-- Purchase Form --}}
        @if($event->available_tickets > 0)
            <form class="ticket-purchase-form" data-event-id="{{ $event->id }}">
                @csrf
                <div class="flex gap-2 mb-3">
                    <select name="quantity" class="quantity-select flex-1 bg-slate-700 border border-slate-600 rounded px-3 py-2 text-white">
                        @for($i = 1; $i <= min(10, $event->available_tickets); $i++)
                            <option value="{{ $i }}">{{ $i }} ticket{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>

                    <button type="submit" class="purchase-btn bg-cyan-600 hover:bg-cyan-700 px-4 py-2 rounded text-white font-medium transition-colors">
                        Buy Now
                    </button>
                </div>

                <div class="total-price text-sm text-slate-300">
                    Total: $<span class="price-amount">{{ $event->price }}</span>
                </div>
            </form>
        @else
            <div class="sold-out-message bg-red-900/20 border border-red-500/30 rounded p-3 text-center">
                <span class="text-red-400 font-medium">🎫 SOLD OUT</span>
            </div>
        @endif

        {{-- Status Messages --}}
        <div class="status-message mt-3" style="display: none;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.querySelector('.ticket-availability-widget[data-event-id="{{ $event->id }}"]');
    const eventId = widget.dataset.eventId;

    // Real-time availability updates every 10 seconds
    setInterval(() => {
        updateAvailability(eventId);
    }, 10000);

    // Handle purchase form
    const purchaseForm = widget.querySelector('.ticket-purchase-form');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', handlePurchase);

        // Update price when quantity changes
        const quantitySelect = widget.querySelector('.quantity-select');
        quantitySelect.addEventListener('change', updateTotalPrice);
    }

    function updateAvailability(eventId) {
        fetch(`/api/tickets/availability/${eventId}`)
            .then(response => response.json())
            .then(data => {
                // Update available count
                const availableCount = widget.querySelector('.available-count');
                availableCount.textContent = data.available_count;

                // Update progress bar
                const progressBar = widget.querySelector('.availability-bar');
                const totalTickets = {{ $event->total_tickets }};
                const percentage = (data.available_count / totalTickets) * 100;
                progressBar.style.width = percentage + '%';

                // Update quantity options
                updateQuantityOptions(data.available_count);

                // Show sold out message if needed
                if (data.available_count === 0) {
                    showSoldOut();
                }
            })
            .catch(error => {
                console.error('Failed to update availability:', error);
            });
    }

    function handlePurchase(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const quantity = formData.get('quantity');

        // Disable button during purchase
        const button = e.target.querySelector('.purchase-btn');
        button.disabled = true;
        button.textContent = 'Processing...';

        fetch('/api/tickets/purchase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                event_id: eventId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            showMessage(data.message, data.success ? 'success' : 'error');

            if (data.success) {
                // Update availability immediately
                updateAvailability(eventId);
            }
        })
        .catch(error => {
            showMessage('Purchase failed. Please try again.', 'error');
        })
        .finally(() => {
            button.disabled = false;
            button.textContent = 'Buy Now';
        });
    }

    function updateTotalPrice() {
        const quantity = widget.querySelector('.quantity-select').value;
        const price = {{ $event->price }};
        const total = (quantity * price).toFixed(2);
        widget.querySelector('.price-amount').textContent = total;
    }

    function updateQuantityOptions(available) {
        const select = widget.querySelector('.quantity-select');
        select.innerHTML = '';

        const maxQuantity = Math.min(10, available);
        for (let i = 1; i <= maxQuantity; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `${i} ticket${i > 1 ? 's' : ''}`;
            select.appendChild(option);
        }
    }

    function showSoldOut() {
        const form = widget.querySelector('.ticket-purchase-form');
        if (form) {
            form.style.display = 'none';
        }

        const soldOutMsg = widget.querySelector('.sold-out-message');
        if (!soldOutMsg) {
            const msg = document.createElement('div');
            msg.className = 'sold-out-message bg-red-900/20 border border-red-500/30 rounded p-3 text-center';
            msg.innerHTML = '<span class="text-red-400 font-medium">🎫 SOLD OUT</span>';
            widget.querySelector('.ticket-availability-widget > div').appendChild(msg);
        }
    }

    function showMessage(message, type) {
        const messageDiv = widget.querySelector('.status-message');
        messageDiv.textContent = message;
        messageDiv.className = `status-message mt-3 p-2 rounded text-sm ${
            type === 'success' ? 'bg-green-900/20 border border-green-500/30 text-green-400' :
            'bg-red-900/20 border border-red-500/30 text-red-400'
        }`;
        messageDiv.style.display = 'block';

        // Hide after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
});
</script>
