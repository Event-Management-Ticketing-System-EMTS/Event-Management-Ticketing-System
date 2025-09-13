// Event Management Ticketing System - Main Application
// This file will be modified by different teams

class EventManager {
    constructor() {
        this.events = [];
        this.tickets = [];
    }

    // Team A will work on this feature
    createEvent(eventData) {
        console.log("Creating event...");
        
        // Team A Implementation: Event creation logic
        if (!eventData.name || !eventData.date || !eventData.venue) {
            throw new Error("Event must have name, date, and venue");
        }
        
        const event = {
            id: this.events.length + 1,
            name: eventData.name,
            date: new Date(eventData.date),
            venue: eventData.venue,
            capacity: eventData.capacity || 100,
            price: eventData.price || 0,
            createdAt: new Date()
        };
        
        this.events.push(event);
        console.log(`Event "${event.name}" created successfully with ID: ${event.id}`);
        return event;
    }

    // Team B will work on this feature
    bookTicket(eventId, userInfo) {
        console.log("Booking ticket...");
        // TODO: Implement ticket booking logic
        return null;
    }

    // Team C will work on this feature
    generateReport() {
        console.log("Generating report...");
        // TODO: Implement reporting logic
        return null;
    }
}

module.exports = EventManager;