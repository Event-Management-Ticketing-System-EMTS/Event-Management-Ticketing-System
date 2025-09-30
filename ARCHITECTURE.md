# EMTS - System Architecture Diagrams

## Overall System Architecture

```mermaid
graph TB
    subgraph "Presentation Layer"
        A[Blade Templates]
        B[Tailwind CSS]
        C[Alpine.js]
    end
    
    subgraph "Application Layer"
        D[Controllers]
        E[Middleware]
        F[Form Requests]
    end
    
    subgraph "Business Logic Layer"
        G[Services]
        H[Repositories]
        I[Observers]
        J[Strategies]
    end
    
    subgraph "Data Layer"
        K[Eloquent Models]
        L[Database]
        M[Migrations]
        N[Caching]
    end
    
    A --> D
    B --> A
    C --> A
    D --> G
    E --> D
    F --> D
    G --> H
    G --> I
    G --> J
    H --> K
    I --> K
    K --> L
    M --> L
    N --> K
```

## Design Patterns Flow

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
    F->>M: new User(validated_data)
    M->>DB: INSERT user
    DB-->>M: user_id
    M-->>F: User instance
    F-->>C: Created user
    C-->>U: Redirect to dashboard

    Note over U,DB: Event Sorting Flow (Repository + Service Pattern)
    U->>C: GET /events?sort=title&direction=asc
    C->>S: validateEventSortParameters()
    S-->>C: validated parameters
    C->>R: getAllWithSorting(sortBy, direction)
    R->>M: orderBy(field, direction)
    M->>DB: SELECT * FROM events ORDER BY...
    DB-->>M: sorted results
    M-->>R: Collection
    R-->>C: Events collection
    C-->>U: Rendered view with sorted events

    Note over U,DB: Role Management Flow (Strategy Pattern)
    U->>C: PATCH /users/{id}/role
    C->>S: changeUserRole(user, newRole, admin)
    S->>S: validate role transition rules
    S->>R: updateRole(userId, newRole)
    R->>M: update(['role' => newRole])
    M->>DB: UPDATE users SET role = ?
    DB-->>M: success
    M-->>R: updated user
    R-->>S: success
    S-->>C: role changed
    C-->>U: JSON success response

    Note over U,DB: Simple Ticket Availability Flow (Observer Pattern)
    U->>C: GET /events/{id}/availability
    C->>TS: getAvailability(eventId)
    TS->>Cache: get cached data
    Cache-->>TS: cached or null
    alt Cache Miss
        TS->>M: query tickets and capacity
        M->>DB: SELECT count(*) FROM tickets WHERE event_id = ?
        DB-->>M: ticket count
        M-->>TS: availability data
        TS->>Cache: store availability
    end
    TS-->>C: availability data
    C-->>U: JSON response

    Note over U,DB: Simple Ticket Purchase with Observer Pattern
    U->>C: POST /events/{id}/purchase-ticket
    C->>TS: purchaseTickets(eventId, quantity, userId)
    TS->>M: create ticket records
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
    TAC --> TPB[Ticket Progress Bar]
    TAC --> TPF[Ticket Purchase Form]
    TAC --> TRT[Real-time Ticker]
    
    D --> D1[Profile Edit]
    D --> D2[Avatar Upload]
    
    A --> E[Admin Views]
    E --> E1[User Management]
    E --> E2[User Details]
    E --> E3[Ticket Management]
    
    E1 --> RSC[Role Selector Component]
    E1 --> SSC[Sorting Controls Component]
    E1 --> USC[User Stats Component]
    
    E3 --> TSC[Ticket Stats Component]
    E3 --> TAM[Ticket Availability Monitor]
```

## Database Relationships

```mermaid
erDiagram
    USER {
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
    
    EVENT {
        bigint id PK
        string title
        text description
        date event_date
        time start_time
        time end_time
        string venue
        string address
        string city
        integer total_tickets
        integer tickets_sold
        decimal price
        enum status
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
    
    Admin --> AdminDashboard
    Organizer --> OrganizerDashboard
    RegularUser --> UserDashboard
    
    AdminDashboard --> [*] : Logout
    OrganizerDashboard --> [*] : Logout
    UserDashboard --> [*] : Logout
```

## Sorting System Architecture

```mermaid
classDiagram
    class SortingService {
        +EVENT_SORT_OPTIONS: array
        +ALLOWED_DIRECTIONS: array
        +validateEventSortParameters(sortBy, direction) array
        +getEventSortOptions() array
        +isDefaultSort(sortBy, direction) bool
        +getOppositeDirection(direction) string
        -validateSortBy(sortBy, allowedSorts) string
        -validateDirection(direction) string
    }
    
    class EventRepository {
        -model: Event
        +getAllWithSorting(sortBy, direction) Collection
        +getByOrganizerWithSorting(organizerId, sortBy, direction) Collection
        +getPublishedWithSorting(sortBy, direction) Collection
    }
    
    class EventController {
        -eventRepository: EventRepository
        -sortingService: SortingService
        +index(request) View
        +create() View
        +store(request) RedirectResponse
        +show(event) View
        +edit(event) View
        +update(request, event) RedirectResponse
        +destroy(event) RedirectResponse
    }
    
    class SortingControlsComponent {
        +action: string
        +sortOptions: array
        +currentSort: string
        +currentDirection: string
        +totalCount: int
        +showReset: bool
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
        +ROLE_ICONS: array
        +changeUserRole(user, newRole, admin) bool
        +getAvailableRoles(currentRole) array
        +getRoleColor(role) string
        +getRoleIcon(role) string
        -canTransitionToRole(currentRole, newRole) bool
    }
    
    class UserController {
        -userRepository: UserRepository
        -sortingService: SortingService
        -roleManagementService: RoleManagementService
        +index(request) View
        +show(id) View
        +updateRole(request, id) JsonResponse
    }
    
    class UserRepository {
        -model: User
        +getAllWithSorting(sortBy, direction) Collection
        +findById(id) User
        +countByRole(role) int
        +getRecentUsers(days) Collection
        +updateRole(userId, role) bool
    }
    
    class RoleSelectorComponent {
        +user: User
        +roleService: RoleManagementService
        +render() View
        +handleRoleChange() void
    }
    
    UserController --> RoleManagementService : uses
    UserController --> UserRepository : uses
    RoleSelectorComponent --> RoleManagementService : uses
    RoleManagementService --> UserRepository : updates through
```

## Security & Access Control

```mermaid
graph TD
    subgraph "Role Hierarchy"
        A[Admin] --> O[Organizer]
        O --> U[User]
    end
    
    subgraph "Access Control Matrix"
        A --> A1[User Management]
        A --> A2[Event Oversight] 
        A --> A3[System Config]
        
        O --> O1[Own Events Only]
        O --> O2[Event Creation]
        
        U --> U1[Event Browsing]
        U --> U2[Profile Management]
    end
    
    subgraph "Security Measures"
        S1[Admin Privilege Validation]
        S2[Self-Role Prevention]
        S3[Role Transition Rules]
        S4[CSRF Protection]
        S5[Input Validation]
    end
```

## Caching Architecture for Real-time Systems

```mermaid
graph TD
    A[User Request] --> B[Controller]
    B --> C[TicketAvailabilityService]
    C --> D{Cache Hit?}
    D -->|Yes| E[Return Cached Data]
    D -->|No| F[Execute Strategy]
    F --> G[Database Query]
    G --> H[Calculate Availability]
    H --> I[Store in Cache]
    I --> J[Return Fresh Data]
    
    K[Observer Event] --> L[Invalidate Cache]
    L --> M[Trigger Recalculation]
    M --> N[Update Cache]
    
    subgraph "Cache Keys"
        CK1[event_availability_{id}]
        CK2[event_tickets_{id}]
        CK3[user_tickets_{userId}]
    end
    
    subgraph "Cache TTL"
        T1[Availability: 30 seconds]
        T2[Tickets: 5 minutes]
        T3[User Data: 10 minutes]
    end
```

## Performance Optimization Strategies

- **Database Indexing**: Optimized indexes on frequently queried columns
- **Query Optimization**: Efficient joins and selective loading
- **Caching Layers**: Multi-level caching for hot data
- **Observer Pattern**: Event-driven cache invalidation
- **Strategy Pattern**: Pluggable business logic for different scenarios
- **Component-based UI**: Reusable components with real-time updates
- **AJAX Polling**: Efficient client-side data refresh without page reloads