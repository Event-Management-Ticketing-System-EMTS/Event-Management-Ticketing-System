<!-- Simple Ticket Availability Component -->
<!-- Just copy this into your event details page -->

<div class="ticket-availability" data-event-id="{{ $event->id }}">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Ticket Availability</h3>

        <!-- Availability Display -->
        <div id="availability-info">
            <div class="mb-4">
                <div class="flex justify-between mb-2">
                    <span>Available Tickets:</span>
                    <span id="available-count">Loading...</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="availability-bar" class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    <span id="availability-percentage">0%</span> available
                </div>
            </div>
        </div>

        <!-- Purchase Form -->
        <div id="purchase-form" class="mt-4">
            <form id="ticket-purchase-form">
                @csrf
                <div class="flex gap-2">
                    <input type="number"
                           id="ticket-quantity"
                           min="1"
                           max="10"
                           value="1"
                           class="border rounded px-3 py-2 w-20">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Buy Tickets
                    </button>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <div id="message-area" class="mt-4 hidden">
            <div id="message" class="p-3 rounded"></div>
        </div>
    </div>
</div>

<script>
// Simple JavaScript for real-time updates
class SimpleTicketTracker {
    constructor(eventId) {
        this.eventId = eventId;
        this.updateInterval = 10000; // Update every 10 seconds
        this.init();
    }

    init() {
        this.loadAvailability();
        this.setupPurchaseForm();
        this.startAutoUpdate();
    }

    // Load current availability
    async loadAvailability() {
        try {
            const response = await fetch(`/api/events/${this.eventId}/availability`);
            const data = await response.json();

            if (data.error) {
                this.showMessage('Error loading availability', 'error');
                return;
            }

            this.updateDisplay(data);
        } catch (error) {
            console.error('Error loading availability:', error);
            this.showMessage('Error loading availability', 'error');
        }
    }

    // Update the display with new data
    updateDisplay(data) {
        document.getElementById('available-count').textContent =
            `${data.available_tickets} / ${data.total_capacity}`;

        document.getElementById('availability-percentage').textContent =
            `${data.availability_percentage}%`;

        const bar = document.getElementById('availability-bar');
        bar.style.width = `${data.availability_percentage}%`;

        // Change color based on availability
        if (data.availability_percentage > 50) {
            bar.className = 'bg-green-600 h-2 rounded-full';
        } else if (data.availability_percentage > 20) {
            bar.className = 'bg-yellow-600 h-2 rounded-full';
        } else {
            bar.className = 'bg-red-600 h-2 rounded-full';
        }

        // Hide form if sold out
        const form = document.getElementById('purchase-form');
        if (data.is_sold_out) {
            form.style.display = 'none';
            this.showMessage('Event is sold out!', 'error');
        } else {
            form.style.display = 'block';
        }
    }

    // Setup purchase form
    setupPurchaseForm() {
        const form = document.getElementById('ticket-purchase-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const quantity = document.getElementById('ticket-quantity').value;

            try {
                const response = await fetch(`/api/events/${this.eventId}/purchase`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                    },
                    body: JSON.stringify({ quantity: parseInt(quantity) })
                });

                const data = await response.json();

                if (data.success) {
                    this.showMessage(data.message, 'success');
                    this.updateDisplay(data.availability);
                } else {
                    this.showMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Purchase error:', error);
                this.showMessage('Error purchasing tickets', 'error');
            }
        });
    }

    // Show success/error messages
    showMessage(message, type) {
        const messageArea = document.getElementById('message-area');
        const messageDiv = document.getElementById('message');

        messageDiv.textContent = message;
        messageDiv.className = `p-3 rounded ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;

        messageArea.classList.remove('hidden');

        // Hide after 5 seconds
        setTimeout(() => {
            messageArea.classList.add('hidden');
        }, 5000);
    }

    // Start automatic updates
    startAutoUpdate() {
        setInterval(() => {
            this.loadAvailability();
        }, this.updateInterval);
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    const eventId = document.querySelector('.ticket-availability').dataset.eventId;
    new SimpleTicketTracker(eventId);
});
</script>
