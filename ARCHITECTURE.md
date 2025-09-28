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
        I[Events]
    end
    
    subgraph "Data Layer"
        J[Eloquent Models]
        K[Database]
        L[Migrations]
    end
    
    A --> D
    B --> A
    C --> A
    D --> G
    E --> D
    F --> D
    G --> H
    H --> J
    J --> K
    L --> K
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
    
    D --> D1[Profile Edit]
    D --> D2[Avatar Upload]
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

## Role Management System Architecture

```mermaid
classDiagram
    class RoleManagementService {
        +ROLE_TRANSITIONS: array
        +canManageRoles() bool
        +canChangeRole(fromRole, toRole) bool
        +changeUserRole(user, newRole) array
        +getAvailableRoles() array
        +getRoleBadgeClass(role) string
        +getRoleIcon(role) string
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
        +getByRoleWithSorting(role, sortBy, direction) Collection
        +findById(id) User
    }
    
    class RoleSelectorComponent {
        +user: User
        +currentUserId: int
        +availableRoles: array
        +disabled: bool
        +render() View
    }
    
    class User {
        +ROLE_ADMIN: string
        +ROLE_USER: string
        +role: string
        +hasRole(role) bool
        +isAdmin() bool
        +isUser() bool
        +getRoles() array
    }
    
    UserController --> RoleManagementService : uses
    UserController --> UserRepository : uses
    UserController --> RoleSelectorComponent : passes data to
    RoleManagementService --> User : validates roles for
    RoleSelectorComponent --> RoleManagementService : uses for UI styling
```