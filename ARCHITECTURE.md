# EMTS - System Architecture Documentation

## 🏗️ Overall System Architecture

### Clean, Simple Architecture with Observer Pattern

```mermaid
graph TB
    subgraph "🎨 Presentation Layer"
        A[Blade Templates]
        B[Tailwind CSS]
        C[Alpine.js Components]
        D[AJAX Real-time Updates]
    end
    
    subgraph "🎮 Application Layer"  
        E[Controllers]
        F[Middleware]
        G[Form Requests]
        H[Route Handlers]
    end
    
    subgraph "🧠 Business Logic Layer"
        I[Simple Services]
        J[Observer Pattern]
        K[Event Listeners]
        L[Notification System]
    end
    
    subgraph "📊 Data Layer"
        M[Eloquent Models]
        N[SQLite Database]
        O[Migrations]
        P[Relationships]
    end
    
    A --> E
    B --> A
    C --> D
    D --> E
    E --> I
    F --> E
    G --> E
    H --> E
    I --> J
    J --> K
    K --> L
    I --> M
    J --> M
    M --> N
    O --> N
    P --> M
```

## 🔄 Observer Pattern Architecture

### Core Observer Implementation

The system uses Laravel's Observer Pattern for real-time ticket tracking and notifications:

```mermaid
graph LR
    subgraph "📦 Model Events"
        A[Ticket Created]
        B[Ticket Updated] 
        C[Ticket Deleted]
    end
    
    subgraph "👁️ Observer Layer"
        D[TicketObserver]
        E[updated method]
        F[created method]
        G[deleted method]
    end
    
    subgraph "🔧 Service Layer"
        H[SimpleTicketService]
        I[SimpleNotificationService]
        J[updateAvailability]
        K[notifyOrganizer]
    end
    
    subgraph "📱 Notification Flow"
        L[Create Notification]
        M[Update UI Badge]
        N[Real-time Updates]
    end
    
    A --> F
    B --> E  
    C --> G
    E --> H
    E --> I
    F --> H
    F --> I
    G --> H
    H --> J
    I --> K
    K --> L
    L --> M
    M --> N
```

### Notification System Flow

```mermaid
sequenceDiagram
    participant User as 👤 Customer
    participant Ticket as 🎫 Ticket Model
    participant Observer as 👁️ TicketObserver
    participant NotifService as 🔔 NotificationService
    participant Organizer as 👨‍💼 Organizer
    participant UI as 💻 Interface
    
    Note over User,UI: Ticket Cancellation with Auto-Notification
    
    User->>Ticket: Cancel ticket (status = 'cancelled')
    Ticket->>Observer: Fires 'updated' event
    Observer->>Observer: Detects status change to 'cancelled'
    Observer->>NotifService: notifyTicketCancellation(ticket)
    NotifService->>NotifService: Create notification record
    NotifService->>Organizer: Store notification in database
    NotifService->>UI: Update notification badge (+1)
    UI->>Organizer: Real-time notification appears
    
    Note over User,UI: Ticket Purchase with Auto-Notification
    
    User->>Ticket: Purchase ticket (creates new record)
    Ticket->>Observer: Fires 'created' event  
    Observer->>NotifService: notifyTicketPurchase(ticket)
    NotifService->>NotifService: Create notification record
    NotifService->>Organizer: Store notification in database
    NotifService->>UI: Update notification badge (+1)
    UI->>Organizer: Real-time revenue notification
```

```mermaid
sequenceDiagram
    participant U as User
    participant C as Controller
    participant F as Factory
    participant S as Service
    participant R as Repository
    participant M as Model
    participant DB as Database

    Note over U,DB: User Registration Flow (Factory Pattern)
    U->>C: POST /register
    C->>F: createUser(data, role)
## 🎯 Simple Service Layer Architecture

### Beginner-Friendly Design Patterns

Our system focuses on **simplicity and learning** rather than over-engineering:

```mermaid
graph TD
    subgraph "🎫 Ticket Management"
        A[SimpleTicketService]
        B[getAvailability]
        C[purchaseTickets]
        D[Cache Results]
    end
    
    subgraph "🔔 Notification System"
        E[SimpleNotificationService]
        F[notifyTicketCancellation]
        G[notifyTicketPurchase]
        H[getUnreadNotifications]
    end
    
    subgraph "👁️ Observer Pattern"
        I[TicketObserver]
        J[updated method]
        K[created method]
        L[Auto-trigger notifications]
    end
    
    A --> B
    A --> C
    A --> D
    E --> F
    E --> G
    E --> H
    I --> J
    I --> K
    J --> A
    J --> E
    K --> A
    K --> E
    L --> E
```

## 🔄 Complete Data Flow Diagrams

### Ticket Purchase with Automatic Notifications

```mermaid
sequenceDiagram
    participant U as 👤 Customer
    participant C as 🎮 Controller
    participant TS as 🎫 TicketService
    participant M as 📊 Model
    participant DB as 🗄️ Database
    participant TO as 👁️ Observer
    participant NS as 🔔 NotificationService
    participant O as 👨‍💼 Organizer

    Note over U,O: Complete Ticket Purchase Flow
    
    U->>C: POST /events/{id}/purchase-ticket
    C->>TS: purchaseTickets(eventId, quantity, userId)
    TS->>M: create new ticket record
    M->>DB: INSERT INTO tickets
    DB-->>M: ticket created with ID
    M-->>TO: Ticket::created event fired
    TO->>TS: updateAvailability(eventId)
    TO->>NS: notifyTicketPurchase(ticket)
    
    par Update Availability
        TS->>TS: clear cache for event
        TS->>M: recalculate availability
        M->>DB: COUNT tickets for event
        DB-->>M: current ticket count
        M-->>TS: updated availability
    and Create Notification
        NS->>NS: build purchase notification
        NS->>M: create notification record
        M->>DB: INSERT INTO notifications
        DB-->>M: notification created
        M-->>O: notification appears in UI
    end
    
    TS-->>C: purchase success + updated availability
    C-->>U: JSON success response
```

### Ticket Cancellation with Organizer Alerts

```mermaid
sequenceDiagram
    participant U as 👤 Customer
    participant C as 🎮 Controller
    participant M as 📊 Model
    participant DB as 🗄️ Database
    participant TO as 👁️ Observer
    participant NS as 🔔 NotificationService
    participant O as 👨‍💼 Organizer

    Note over U,O: Complete Ticket Cancellation Flow
    
    U->>C: PATCH /tickets/{id}/cancel
    C->>M: update ticket status to 'cancelled'
    M->>DB: UPDATE tickets SET status = 'cancelled'
    DB-->>M: status updated
    M-->>TO: Ticket::updated event fired
    TO->>TO: detect status change to 'cancelled'
    TO->>NS: notifyTicketCancellation(ticket)
    
    NS->>NS: get event organizer
    NS->>NS: build cancellation notification
    NS->>M: create notification record
    M->>DB: INSERT INTO notifications
    DB-->>M: notification created
    M-->>O: alert appears with customer details
    
    TO-->>C: cancellation processed
    C-->>U: cancellation confirmed
```
    M->>DB: INSERT INTO tickets
    DB-->>M: tickets created
    M-->>TO: Ticket::created event
    TO->>TS: updateAvailability(eventId)
    TS->>Cache: clear cache
    TS-->>C: purchase success
    C-->>U: JSON success response
```

## Component Hierarchy

```mermaid
graph TD
    A[App Layout] --> B[Auth Views]
    A --> C[Event Views]
    A --> D[Profile Views]
    
    B --> B1[Login Form]
    B --> B2[Register Form]
    B --> B3[Reset Password]
    
    C --> C1[Events Index]
    C --> C2[Event Create/Edit]
    C --> C3[Event Details]
    
    C1 --> SC[Sorting Controls Component]
    C1 --> TC[Table Component]
    C1 --> PC[Pagination Component]
    
    C3 --> TAC[Ticket Availability Component]
## 🎨 Component Hierarchy

### Modern Blade Component Structure

```mermaid
graph TD
    A[🏠 App Layout] --> B[🔐 Auth Views]
    A --> C[🎪 Event Views] 
    A --> D[👤 Profile Views]
    A --> E[🔔 Notification Views]
    
    B --> B1[Login Form]
    B --> B2[Register Form]
    B --> B3[Reset Password]
    
    C --> C1[📋 Events Index]
    C --> C2[✏️ Event Create/Edit]
    C --> C3[📄 Event Details]
    
    C1 --> SC[🔽 Sorting Controls]
    C1 --> TC[📊 Table Component]
    C1 --> PC[📑 Pagination]
    
    C3 --> TAC[🎫 Ticket Availability]
    TAC --> TPB[📊 Progress Bar]
    TAC --> TPF[💳 Purchase Form]
    TAC --> TRT[⚡ Real-time Updates]
    
    D --> D1[✏️ Profile Edit]
    D --> D2[🖼️ Avatar Upload]
    
    E --> E1[🔔 Notification Center]
    E --> E2[📬 Notification Cards]
    E --> E3[🔵 Notification Badges]
    
    E1 --> NFC[📋 Notification Filter]
    E1 --> NLC[📜 Notification List]
    E2 --> NRC[✅ Mark Read Button]
    E2 --> NDC[📊 Notification Data]
```

## 🗄️ Database Relationships & Schema

### Complete Entity Relationship Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role
        boolean email_verified
        string avatar_path
        string remember_token
        timestamp created_at
        timestamp updated_at
    }
    
    EVENTS {
        bigint id PK
        string title
        text description
        date event_date
        time start_time
        time end_time
        string venue
        string address
        string city
        integer capacity
        decimal price
        enum status
        bigint organizer_id FK
        string image_path
        timestamp created_at
        timestamp updated_at
    }
    
    TICKETS {
        bigint id PK
        bigint event_id FK
        bigint user_id FK
        integer quantity
        decimal total_price
        timestamp purchase_date
        enum status
        timestamp created_at
        timestamp updated_at
    }
    
    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string title
        text message
        string type
        boolean is_read
        json data
        timestamp created_at
        timestamp updated_at
    }
    
    LOGIN_LOGS {
        bigint id PK
        bigint user_id FK
        string email
        boolean success
        string ip_address
        text user_agent
        timestamp created_at
    }
    
    %% Relationships
    USERS ||--o{ EVENTS : "organizes"
    USERS ||--o{ TICKETS : "purchases"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ LOGIN_LOGS : "generates"
    EVENTS ||--o{ TICKETS : "has bookings"
```
        bigint organizer_id FK
        string image_path
        timestamp created_at
        timestamp updated_at
    }
    
    LOGIN_LOG {
        bigint id PK
        bigint user_id FK
        string email
        boolean success
        string ip
        text user_agent
        timestamp created_at
    }
    
    USER ||--o{ EVENT : "organizes (1:many)"
    USER ||--o{ LOGIN_LOG : "logs (1:many)"
```

## Authentication Flow

```mermaid
stateDiagram-v2
    [*] --> Guest
    Guest --> Authenticating : Login Attempt
    Authenticating --> Admin : role = admin
    Authenticating --> Organizer : role = organizer
    Authenticating --> RegularUser : role = user
    Authenticating --> Guest : Failed Authentication
## ⚙️ Observer Pattern Deep Dive

### Why Observer Pattern for Beginners?

The Observer Pattern is perfect for learning because it's **simple** and **automatic**:

```mermaid
graph LR
    subgraph "🎯 Problem"
        A[Manual Updates]
        B[Forgotten Notifications]  
        C[Tight Coupling]
        D[Code Duplication]
    end
    
    subgraph "✅ Observer Solution"
        E[Automatic Updates]
        F[Never Miss Events]
        G[Loose Coupling]
        H[Single Responsibility]
    end
    
    A --> E
    B --> F
    C --> G
    D --> H
```

### Observer Pattern Benefits

- ✅ **Easy to understand** - One service, one observer, one controller

- ✅ **Automatic notifications** - No manual trigger needed
- ✅ **Single responsibility** - Each class has one job
- ✅ **Extensible** - Easy to add new notification types
- ✅ **Testable** - Simple to mock and test
- ✅ **Laravel native** - Uses framework's built-in observer system

### Code Structure Benefits

```mermaid
graph TD
    subgraph "👨‍🎓 Beginner Friendly"
        A[Clear Class Names]
        B[Single Purpose Methods]
        C[Easy to Debug]
        D[Minimal Configuration]
    end
    
    subgraph "🚀 Professional Quality"
        E[Design Pattern]
        F[Automatic Processing]
        G[Real-time Updates]
        H[Scalable Architecture]
    end
    
    A --> E
    B --> F
    C --> G
    D --> H
```

## 🔄 Real-time Update Architecture

### AJAX Polling System

Our real-time updates use simple AJAX polling for maximum compatibility:

```mermaid
sequenceDiagram
    participant Browser as 🌐 Browser
    participant Controller as 🎮 Controller
    participant Service as 🔧 Service
    participant Cache as 📦 Cache
    participant DB as 🗄️ Database
    
    Note over Browser,DB: Real-time Ticket Availability Updates
    
    loop Every 10 seconds
        Browser->>Controller: GET /events/{id}/availability
        Controller->>Service: getAvailability(eventId)
        Service->>Cache: check cached availability
        
        alt Cache Hit
            Cache-->>Service: return cached data
        else Cache Miss
            Service->>DB: COUNT tickets WHERE event_id = ?
            DB-->>Service: current count
            Service->>Cache: store for 30 seconds
        end
        
        Service-->>Controller: availability data
        Controller-->>Browser: JSON response
        Browser->>Browser: update progress bar & stats
    end
    
    Note over Browser,DB: Background Notification Badge Updates
    
    loop Every 15 seconds  
        Browser->>Controller: GET /notifications/unread-count
        Controller->>Service: getUnreadCount(userId)
        Service->>DB: COUNT notifications WHERE is_read = false
        DB-->>Service: unread count
        Service-->>Controller: count data
        Controller-->>Browser: JSON response
        Browser->>Browser: update notification badge
    end
```
        +render() View
    }
    
    EventController --> SortingService : uses
    EventController --> EventRepository : uses
    EventController --> SortingControlsComponent : passes data to
    SortingService --> EventRepository : validates parameters for
```

## Simplified Ticket Availability System Architecture ⭐ **BEGINNER FRIENDLY**

```mermaid
classDiagram
    class SimpleTicketService {
        +getAvailability(eventId) array
        +purchaseTickets(eventId, quantity, userId) bool
        +updateAvailability(eventId) void
        -clearAvailabilityCache(eventId) void
    }
    
    class TicketObserver {
        -ticketService: SimpleTicketService
        +created(ticket) void
        +updated(ticket) void
        +deleted(ticket) void
    }
    
    class SimpleTicketController {
        -ticketService: SimpleTicketService
        +getAvailability(eventId) JsonResponse
        +purchaseTickets(request, eventId) JsonResponse
    }
    
    class SimpleTicketAvailabilityComponent {
        +eventId: int
        +refreshInterval: int
        +render() View
        +updateAvailability() void
        +handlePurchase() void
    }
    
    TicketObserver --> SimpleTicketService : triggers
    SimpleTicketController --> SimpleTicketService : uses
    SimpleTicketAvailabilityComponent --> SimpleTicketController : calls API
```

**Key Benefits of Simplified Design:**
- ✅ **Easy to understand** - One service, one observer, one controller
- ✅ **Observer Pattern** - Automatic updates when tickets change
- ✅ **Caching** - Fast performance with simple cache strategy
- ✅ **Real-time UI** - Updates every 10 seconds automatically
- ✅ **No complexity** - No strategy interfaces or multiple implementations

## Role Management System Architecture

```mermaid
sequenceDiagram
    participant A as Admin
    participant UI as Role Selector
    participant UC as UserController
    participant RMS as RoleManagementService
    participant UR as UserRepository
    participant DB as Database

    Note over A,DB: Role Change Flow (Strategy Pattern)
    A->>UI: Select new role for user
    UI->>UC: PATCH /users/{id}/role
    UC->>RMS: changeUserRole(user, newRole, admin)
    
    Note over RMS: Strategy: Validate admin permissions
    RMS->>RMS: validateAdminRole(admin)
    
    Note over RMS: Strategy: Prevent self-modification
    RMS->>RMS: preventSelfModification(user, admin)
    
    Note over RMS: Strategy: Validate role transition
    RMS->>RMS: canTransitionToRole(currentRole, newRole)
    
    RMS->>UR: updateRole(userId, newRole)
    UR->>DB: UPDATE users SET role = newRole
    DB-->>UR: success
    UR-->>RMS: updated
    RMS-->>UC: success
    UC-->>UI: JSON success response
    UI-->>A: Success notification & UI update
```

```mermaid
classDiagram
    class RoleManagementService {
        +ROLE_TRANSITIONS: array
        +ROLE_COLORS: array
## 🚀 Implementation Benefits

### Why This Architecture Works for Learners

```mermaid
graph TD
    subgraph "📚 Learning Benefits"
        A[Clear Separation of Concerns]
        B[Single Design Pattern Focus]
        C[Real-world Applicability]
        D[Easy to Extend]
    end
    
    subgraph "💼 Professional Quality"
        E[Industry Standard Patterns]
        F[Scalable Architecture]
        G[Maintainable Code]
        H[Testable Components]
    end
    
    A --> E
    B --> F
    C --> G
    D --> H
```

### Technology Stack Integration

- **Laravel Framework**: Robust foundation with built-in Observer support
- **SQLite Database**: Lightweight, perfect for learning and development
- **Tailwind CSS**: Utility-first styling for rapid UI development
- **Alpine.js**: Minimal JavaScript framework for reactive components
- **AJAX Polling**: Simple real-time updates without WebSocket complexity

## 🔮 Future Extensions

### Easy Enhancement Opportunities

The current architecture makes it simple to add:

```mermaid
graph LR
    subgraph "🎯 Current Features"
        A[Ticket Management]
        B[Notifications]
        C[Real-time Updates]
    end
    
    subgraph "🚀 Easy Extensions"
        D[Email Notifications]
        E[SMS Alerts]
        F[Push Notifications]
        G[Analytics Dashboard]
        H[Payment Integration]
        I[QR Code Tickets]
    end
    
    A --> D
    B --> E
    C --> F
    A --> G
    B --> H
    C --> I
```

### Adding New Notification Types

Simply extend the Observer and add new methods:

```php
// In TicketObserver.php
public function updated(Ticket $ticket)
{
    // Existing cancellation logic...
    
    // New: Refund processed notification
    if ($ticket->wasChanged('refund_status') && $ticket->refund_status === 'processed') {
        $this->notificationService->notifyRefundProcessed($ticket);
    }
    
    // New: Event reminder notification  
    if ($ticket->event->event_date->isToday()) {
        $this->notificationService->notifyEventReminder($ticket);
    }
}
```

This architecture grows with your learning journey! 🌱 
        A --> A3[System Config]
        
        O --> O1[Own Events Only]
        O --> O2[Event Creation]
        
        U --> U1[Event Browsing]
        U --> U2[Profile Management]
    end
    
    subgraph "Security Measures"
        S1[Admin Privilege Validation]
        S2[Self-Role Prevention]
This architecture grows with your learning journey! 🌱

---

## 📖 Documentation Summary

This **Event Management & Ticketing System** demonstrates how to build professional-quality applications using simple, beginner-friendly design patterns. The **Observer Pattern** serves as the foundation for both real-time ticket availability and organizer notifications, proving that one well-implemented pattern can power multiple features effectively.

**Key Learning Outcomes:**
- ✅ Observer Pattern for automatic event handling
- ✅ Service Layer Pattern for clean business logic  
- ✅ Real-time updates with AJAX polling
- ✅ Database relationships and migrations
- ✅ Component-based UI architecture
- ✅ Professional documentation practices

Perfect for students learning Laravel, design patterns, and modern web development! 🚀