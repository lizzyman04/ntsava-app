# Ntsava CDN Architecture

## System Architecture Overview

A complete content delivery network platform with separation of concerns, global edge caching, and multi-tenant isolation.

### High-Level Architecture

```
[Global Edge Network - 300+ Points of Presence]
                          |
                          v
              [Cloudflare / Bunny / AWS / Custom]
                          |
                          v
              [Your Origin Infrastructure]
    +------------------+------------------+
    |                  |                  |
    v                  v                  v
[API Server]      [Storage]          [Database]
 api.domain        cdn.domain         MySQL/PgSQL
```

### Three-Tier Architecture Diagram

```mermaid
graph TB
    subgraph Edge["Global Edge Network (PoPs)"]
        PoP_Maputo["PoP Maputo (Mozambique)"]
        PoP_Tokyo["PoP Tokyo (Japan)"]
        PoP_NY["PoP New York (USA)"]
        PoP_London["PoP London (UK)"]
        PoP_SP["PoP Sao Paulo (Brazil)"]
    end

    subgraph Origin["Your Origin Infrastructure"]
        API["API Gateway<br/>api.yourdomain.com<br/>PHP 8.1+"]
        Auth["Auth Service<br/>Token Validation"]
        Image["Image Processor<br/>GD Library"]
        Queue["Job Queue<br/>Redis/Beanstalkd"]
    end

    subgraph Data["Data Layer"]
        DB[("Database<br/>MySQL/PostgreSQL")]
        FS[("File Storage<br/>Local/S3/R2")]
        Cache[("Cache<br/>Redis/Memcached")]
    end

    PoP_Maputo --> API
    PoP_Tokyo --> API
    PoP_NY --> API
    PoP_London --> API
    PoP_SP --> API
    
    API --> Auth
    API --> Image
    API --> Queue
    
    Auth --> DB
    Image --> FS
    Queue --> Cache
    
    FS --> PoP_Maputo
    FS --> PoP_Tokyo
    FS --> PoP_NY
    FS --> PoP_London
    FS --> PoP_SP

    classDef edge fill:#2E7D32,stroke:#1B5E20,color:#fff
    classDef origin fill:#1565C0,stroke:#0D47A1,color:#fff
    classDef data fill:#E65100,stroke:#BF360C,color:#fff
    
    class PoP_Maputo,PoP_Tokyo,PoP_NY,PoP_London,PoP_SP edge
    class API,Auth,Image,Queue origin
    class DB,FS,Cache data
```

## Component Breakdown

### 1. Edge Layer (CDN Points of Presence)

The edge layer consists of geographically distributed cache servers that serve content from locations closest to end users.

**Characteristics:**
- Geographic distribution: 300+ global locations (including Maputo, Mozambique)
- Cache storage: SSD/NVMe (varies by provider)
- Cache duration: Configurable (1 hour to 1 year)
- Eviction policy: LRU (Least Recently Used)
- SSL termination: At edge (free certificates with most providers)

**Supported CDN Providers:**

| Provider | Points of Presence | Free Tier | Best For |
|----------|-------------------|-----------|----------|
| Cloudflare | 300+ (including Maputo) | Unlimited bandwidth | General purpose, DDoS protection |
| Bunny.net | 120+ | $1 trial | Video streaming, gaming assets |
| AWS CloudFront | 400+ | 1TB/month free | AWS ecosystem integration |
| Akamai | 4,000+ | Enterprise only | Financial, healthcare, high security |
| Custom Varnish | Self-managed | Depends on infrastructure | Complete control, compliance |

### 2. Application Layer (API Origin)

The application layer handles all dynamic operations: upload, delete, authentication, and image processing.

**Directory Structure:**
```
app/
├── Core/                    # Core abstractions
│   ├── Auth.php            # Session/token management
│   ├── Uploader.php        # File handling and validation
│   ├── Mailer.php          # Email notifications
│   └── ORMHelper.php       # Database abstraction
├── Middleware/              # Request filters
│   └── ApiAuthMiddleware.php
└── router/                  # Route definitions (file-based)
    ├── api/v1/
    │   ├── upload.php
    │   ├── delete.php
    │   ├── info.php
    │   └── resize.php
    └── dashboard/           # Web interface routes
```

**Technology Stack Details:**
- Framework: Fluxor PHP (file-based routing, security-first)
- ORM: Cycle ORM (DataMapper pattern, database agnostic)
- Image Processing: GD Library (no external dependencies)

### 3. Data Layer

The data layer consists of three complementary storage systems.

**Primary Database (MySQL/PostgreSQL):**

```sql
-- Optimized indexes for high throughput
CREATE INDEX idx_files_user_deleted ON files(user_id, deleted_at);
CREATE INDEX idx_tokens_hash_user ON api_tokens(token_hash, user_id);
CREATE INDEX idx_bandwidth_reset ON users(bandwidth_reset_month);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
```

**File Storage Structure:**
```
storage/
├── u/                      # User storage root
│   ├── {username}/         # Per-user directory (isolated)
│   │   ├── {path}/         # User-defined folders
│   │   │   └── {files}
│   │   └── ...
│   └── ...
└── cache/                  # Resized images cache
    ├── {md5_hash}.webp
    ├── {md5_hash}.jpg
    └── ...
```

## Request Flows

### Flow 1: File Upload

```mermaid
sequenceDiagram
    participant Client
    participant API as API Server
    participant Auth as Auth Service
    participant DB as Database
    participant FS as File Storage
    participant Queue as Job Queue

    Client->>API: POST /api/v1/upload
    Note over Client,API: Headers: X-User-UUID, X-Token<br/>Body: multipart/form-data

    API->>Auth: Validate token
    Auth->>DB: SELECT * FROM api_tokens WHERE token_hash = ?
    DB-->>Auth: Token data + user_id
    Auth-->>API: User object + permissions

    alt Has upload permission
        API->>API: Validate file (size, type, MIME)
        API->>DB: SELECT storage_used, storage_limit FROM users
        DB-->>API: Current usage
        
        alt Has quota available
            API->>FS: Store file in /storage/u/{username}/{path}
            FS-->>API: File stored
            API->>DB: INSERT INTO files (user_id, uuid, path, size)
            API->>DB: UPDATE users SET storage_used_bytes += size
            API->>Queue: Add to processing queue (optional)
            API-->>Client: 201 Created + CDN URL
        else Quota exceeded
            API-->>Client: 403 Forbidden (Quota exceeded)
        end
    else No permission
        API-->>Client: 403 Forbidden
    end
```

### Flow 2: File Access with CDN Caching

```mermaid
sequenceDiagram
    participant Client
    participant DNS as DNS Resolver
    participant PoP as CDN PoP (Maputo)
    participant Origin as Origin Server

    Client->>DNS: Resolve cdn.yourdomain.com
    DNS-->>Client: PoP IP (geo-routed to Maputo)
    Client->>PoP: GET /u/johndoe/photo.jpg
    
    alt Cache HIT
        PoP-->>Client: 200 OK
        Note over PoP,Client: Cache-Control: max-age=31536000<br/>CF-Cache-Status: HIT<br/>Served in <10ms
    else Cache MISS
        PoP->>Origin: GET /storage/u/johndoe/photo.jpg
        Origin-->>PoP: 200 OK + Cache Headers
        PoP->>PoP: Store in edge cache (SSD)
        PoP-->>Client: 200 OK
        Note over PoP,Origin: First request: 50-200ms<br/>Cache-Control: max-age=31536000<br/>CF-Cache-Status: MISS
    end
```

### Flow 3: Image Processing with Caching

```mermaid
sequenceDiagram
    participant Client
    participant PoP as CDN PoP
    participant API as API Server
    participant Processor as Image Processor
    participant Cache as Cache Storage
    participant FS as File Storage

    Client->>PoP: GET /u/johndoe/photo.jpg?w=800&format=webp
    PoP->>API: Forward to resize endpoint
    API->>API: Parse URL parameters
    API->>Processor: Process image (w=800, format=webp)
    
    alt Cached result exists
        Processor->>Cache: Check cache/{hash}.webp
        Cache-->>Processor: File exists (cache HIT)
        Processor-->>API: Return cached file
    else No cache
        Processor->>FS: Load original image
        FS-->>Processor: Original file
        Processor->>Processor: Resize to 800px
        Processor->>Processor: Convert to WebP
        Processor->>Processor: Apply filters (if any)
        Processor->>Cache: Store result (cache MISS)
        Processor-->>API: Return processed image
    end
    
    API-->>PoP: 200 OK (image/webp)
    PoP->>PoP: Cache processed image at edge
    PoP-->>Client: 200 OK
    Note over Client,PoP: Next request: Edge cache HIT
```

## Database Schema

### Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ FILES : owns
    USERS ||--o{ API_TOKENS : has
    USERS ||--|| CREDITS : has
    USERS ||--o{ USER_ROLES : assigned
    USERS }o--|| PLANS : subscribes
    USERS ||--o{ NOTIFICATIONS : receives
    CREDITS ||--o{ CREDIT_TRANSACTIONS : generates

    USERS {
        bigint id PK
        varchar36 uuid UK
        varchar100 name
        varchar50 username UK
        varchar255 email UK
        varchar255 password_hash
        int plan_id FK
        bigint storage_limit_bytes
        bigint storage_used_bytes
        bigint bandwidth_limit_bytes
        bigint bandwidth_used_bytes
        int bandwidth_reset_month
        varchar20 status
        text suspended_reason
        datetime suspended_at
        datetime last_login_at
        datetime created_at
        datetime updated_at
    }

    FILES {
        bigint id PK
        bigint user_id FK
        varchar36 uuid UK
        varchar500 storage_path
        varchar255 original_name
        bigint size_bytes
        varchar100 mime_type
        int width
        int height
        int duration_seconds
        datetime created_at
        datetime deleted_at
    }

    API_TOKENS {
        bigint id PK
        bigint user_id FK
        varchar255 token_hash UK
        varchar100 name
        json permissions
        datetime expires_at
        datetime last_used_at
        datetime created_at
    }

    PLANS {
        int id PK
        varchar50 name
        varchar50 slug UK
        text description
        bigint storage_limit_bytes
        bigint bandwidth_limit_bytes
        bigint max_file_size_bytes
        text allowed_mime_types
        decimal10_2 price
        varchar3 currency
        boolean is_active
        int sort_order
        datetime created_at
        datetime updated_at
    }

    CREDITS {
        bigint id PK
        bigint user_id FK
        decimal10_2 amount
        varchar3 currency
        datetime created_at
        datetime updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        varchar30 type
        varchar255 title
        text message
        boolean is_read
        json metadata
        datetime created_at
        datetime read_at
    }
```

### Indexing Strategy

```sql
-- Performance-critical indexes for high-traffic scenarios
CREATE INDEX idx_files_user_deleted ON files(user_id, deleted_at);
CREATE INDEX idx_files_created ON files(created_at DESC);
CREATE INDEX idx_tokens_hash ON api_tokens(token_hash);
CREATE INDEX idx_tokens_user_expires ON api_tokens(user_id, expires_at);
CREATE INDEX idx_users_bandwidth_reset ON users(bandwidth_reset_month);
CREATE INDEX idx_users_status_plan ON users(status, plan_id);
CREATE INDEX idx_notifications_user_unread ON notifications(user_id, is_read);
CREATE INDEX idx_credits_user ON credits(user_id);
CREATE INDEX idx_transactions_user_created ON credit_transactions(user_id, created_at);
```

## Database Agnostic Design

Ntsava uses Cycle ORM which provides database abstraction. The same code works on:

| Database | Version | Tested | Production Ready |
|----------|---------|--------|------------------|
| MySQL | 5.7+ | Yes | Yes |
| MariaDB | 10.2+ | Yes | Yes |
| PostgreSQL | 12+ | Yes | Yes |
| SQLite | 3.35+ | Yes | Development only |

**Configured drivers** (`db/core/connection.php`):

| Database | Version | Tested | Production Ready |
|----------|---------|--------|------------------|
| MySQL | 5.7+ | Yes | Yes |
| MariaDB | 10.2+ | Yes | Yes |
| SQLite | 3.35+ | Yes | Development only |

All queries use ORM methods — no vendor-specific SQL:

```php
$users = ORMHelper::getRepository(User::class)
    ->findAll(['status' => 'active']);

$file = ORMHelper::findOneBy(File::class, 'uuid', $uuid);
```

## Security Model

### Authentication Flow

```mermaid
graph LR
    subgraph Client
        A[Store Token] --> B[Add Headers]
    end
    
    subgraph Server
        C[Extract UUID + Token] --> D[Hash Token SHA256]
        D --> E{Valid Token?}
        E -->|Yes| F[Get User]
        E -->|No| G[Return 401]
        F --> H{Has Permission?}
        H -->|Yes| I[Execute Request]
        H -->|No| J[Return 403]
    end
    
    B --> C
    I --> K[Return Response]
    G --> K
    J --> K
```

### Permission Matrix

| Operation | Required Permission | Token Types | Rate Limit (per hour) |
|-----------|--------------------|-------------|----------------------|
| Upload file | `upload` | API, Web | 100 |
| Delete file | `delete` | API, Web | 50 |
| Get file info | `read` | API, Web | 1000 |
| List files | `read` | API, Web | 100 |
| Generate token | `admin` | Web only | 10 |
| Delete account | `owner` | Web only | 1 |

### Security Headers Configuration

```apache
# .htaccess security headers (Apache)
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "DENY"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Content-Security-Policy "default-src 'self'"
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
```

```nginx
# Nginx security headers
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "DENY" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## Caching Strategy

### Multi-Level Cache Architecture

```mermaid
graph TB
    subgraph Browser["Browser Cache (Client)"]
        B1[Memory Cache]
        B2[Disk Cache]
    end
    
    subgraph Edge["CDN Edge Cache"]
        E1[RAM Cache - Fast]
        E2[SSD Cache - Large]
    end
    
    subgraph Origin["Origin Cache"]
        O1[Redis/Memcached]
        O2[OPcache - PHP]
    end
    
    subgraph Storage["Persistent Storage"]
        S1[Disk Storage]
        S2[Backup Storage]
    end
    
    B1 --> B2
    B2 --> E1
    E1 --> E2
    E2 --> O1
    O1 --> O2
    O2 --> S1
    S1 --> S2
```

### Cache Headers by Content Type

| Content Type | Cache TTL | CDN TTL | Browser TTL | Stale-While-Revalidate |
|--------------|-----------|---------|-------------|------------------------|
| Images (JPEG/PNG) | 1 year | 30 days | 7 days | 1 day |
| Images (WebP/AVIF) | 1 year | 30 days | 7 days | 1 day |
| Videos (MP4/WebM) | 1 year | 90 days | 30 days | 7 days |
| CSS/JavaScript | 1 year | 30 days | 7 days | 1 hour |
| API Responses | No cache | No cache | No cache | N/A |
| HTML pages | 1 hour | 1 hour | 1 hour | 5 minutes |

### Cache Invalidation

**CDN edge cache:** Governed by `Cache-Control: public, max-age=31536000, immutable` headers set in `storage/.htaccess`. No programmatic CDN purge is implemented. Stale content is avoided by appending a 6-character random suffix to every uploaded filename (e.g., `photo_a1b2c3.jpg`), so each upload gets a unique URL.

**Resize cache (`storage/cache/`):** Automatically pruned on each `ResizeService` instantiation — any cached file older than 7 days is deleted before serving new requests.

## Scalability Patterns

### Horizontal Scaling Architecture

```mermaid
graph TB
    LB[Load Balancer<br/>HAProxy / Nginx]
    
    subgraph API_Servers["API Server Cluster"]
        A1[API Node 1<br/>4 CPU / 8GB RAM]
        A2[API Node 2<br/>4 CPU / 8GB RAM]
        A3[API Node N<br/>4 CPU / 8GB RAM]
    end
    
    subgraph Database["Database Cluster"]
        DB_M[Master - Write Operations]
        DB_S1[Slave 1 - Read Operations]
        DB_S2[Slave 2 - Read Operations]
    end
    
    subgraph Storage["Storage Layer"]
        FS1[Storage Node 1<br/>SSD - Hot Data]
        FS2[Storage Node 2<br/>SSD - Hot Data]
        FS3[Storage Node N<br/>HDD - Cold Data]
    end
    
    LB --> A1
    LB --> A2
    LB --> A3
    
    A1 --> DB_M
    A1 --> DB_S1
    A2 --> DB_M
    A2 --> DB_S2
    A3 --> DB_M
    A3 --> DB_S1
    
    A1 --> FS1
    A2 --> FS2
    A3 --> FS3
```

### Auto-Scaling Triggers

```
Scale Up (Add nodes):
  - CPU usage > 70% for 5 minutes
  - Memory usage > 80% for 5 minutes
  - Requests per second > 1000 per node
  - Queue length > 10,000 pending jobs
  - Response time > 500ms for 5 minutes

Scale Down (Remove nodes):
  - CPU usage < 30% for 30 minutes
  - Memory usage < 50% for 30 minutes
  - Requests per second < 100 per node
  - Minimum 2 nodes running (high availability)
```

### Geographic Data Distribution

```
Hot Storage (SSD - 3x replication):
  - Recent files (< 30 days old)
  - Frequently accessed (> 100 views/day)
  - Replicated to 3 geographic regions
  - Access time: < 10ms

Warm Storage (HDD - 2x replication):
  - Older files (30-365 days old)
  - Infrequently accessed (10-100 views/day)
  - Replicated to 2 regions
  - Access time: 20-50ms

Cold Storage (Archive - 1x + backup):
  - Old files (> 365 days old)
  - Rarely accessed (< 10 views/day)
  - Single region + offline backup
  - Access time: 100-500ms (retrieval from archive)
```

## Monitoring & Observability

### Key Performance Indicators (KPIs)

```
Application Metrics:
  - Request rate (by endpoint: upload, delete, info, resize)
  - Error rate (4xx client errors, 5xx server errors)
  - Response time (p50, p95, p99 percentiles)
  - Active users (by plan: Free, Plus, Pro, Business)
  - Storage usage (per user, per plan, global total)
  - Bandwidth consumption (per user, per region)

CDN Metrics:
  - Cache hit ratio (per Point of Presence)
  - Bandwidth served (edge vs origin comparison)
  - Popular content (top 100 files by requests)
  - Geographic distribution (requests by country/city)
  - PoP performance (latency by location: Maputo, Tokyo, etc.)

Infrastructure Metrics:
  - CPU / Memory usage per node
  - Database connection pool utilization
  - Disk I/O and available space
  - Network throughput (inbound/outbound)
```

### Health Verification

There is no dedicated `/api/v1/health` endpoint. To verify the application is up, send a request to the root and confirm an HTTP 200 response:

```bash
curl -I https://api.yourdomain.com/
# Expected: HTTP/2 200
```

To verify database connectivity directly:

```bash
php -r "new PDO('mysql:host=localhost;dbname=ntsava_cdn', 'user', 'pass');"
echo $?  # 0 = success
```

## Best Practices

### Performance Optimization

1. Use CDN caching for all static assets (images, videos, CSS, JS)
2. Enable compression (Brotli preferred over Gzip, 20-30% better)
3. Implement lazy loading for images below the fold
4. Use modern formats (WebP/AVIF) with JPEG/PNG fallback
5. Set appropriate cache TTLs based on content volatility
6. Monitor cache hit ratios daily, investigate if below 85%

### Security Hardening

1. Block PHP execution in storage directory completely
2. Validate file types by MIME, not just file extension
3. Implement rate limiting per token and per user
4. Use prepared statements (ORM handles this automatically)
5. Encrypt sensitive data (tokens, API keys, personal info)
6. Run regular security audits (monthly recommended)

### Cost Optimization

1. Leverage CDN free tiers (Cloudflare offers unlimited)
2. Use S3/R2-compatible storage for cost-effective archiving
3. Implement lifecycle policies (auto-delete files older than X days)
4. Monitor bandwidth usage by user, alert on anomalies
5. Compress images on upload (reduce origin storage costs)
6. Remove unused cached files weekly (cron job)

## Deployment Architecture Decision Tree

```mermaid
graph TD
    Start[Start] --> Q1{Monthly<br/>Visitors?}
    
    Q1 -->|< 100K| Small[Single Server<br/>Shared Hosting / VPS]
    Q1 -->|100K - 1M| Medium[Multi-Server<br/>Load Balanced]
    Q1 -->|> 1M| Large[Global CDN +<br/>Multi-Region]
    
    Small --> Q2{Storage<br/>Size?}
    Q2 -->|< 500GB| S1[Single VPS<br/>Local Storage]
    Q2 -->|> 500GB| S2[VPS + S3/R2<br/>Object Storage]
    
    Medium --> Q3{Availability<br/>Required?}
    Q3 -->|99.9%| M1[2 App Servers<br/>1 DB Master + 1 Replica]
    Q3 -->|99.99%| M2[3+ App Servers<br/>DB Cluster + Redis Cache]
    
    Large --> Q4{Budget?}
    Q4 -->|$$| L1[Cloudflare Enterprise<br/>AWS Global Accelerator]
    Q4 -->|$$$| L2[Custom PoPs<br/>Varnish + BGP Anycast]
    
    S1 --> Deploy[Deploy Ntsava CDN]
    S2 --> Deploy
    M1 --> Deploy
    M2 --> Deploy
    L1 --> Deploy
    L2 --> Deploy
```

---

## Summary

Ntsava CDN provides a production-ready, database-agnostic content delivery platform that scales from a single server to global edge networks. The architecture emphasizes:

- Separation of concerns (API vs static storage)
- Database agnosticism (MySQL, PostgreSQL, SQLite)
- CDN agnosticism (Cloudflare, Bunny, AWS, Akamai, Custom)
- Security-first design (token auth, permissions, validation)
- Performance optimization (multi-level caching, CDN edge)
- Monitoring and observability (health checks, metrics)

[Back to Top](#ntsava-cdn-architecture)