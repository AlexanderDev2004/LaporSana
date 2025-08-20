# LaporSana - System Architecture Overview

## Application Flow Diagram

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Browser   │────│   Load Balancer │────│   Web Server    │
│   (Frontend)    │    │    (Optional)   │    │   (Apache/Nginx)│
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                                        │
                                                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel Application                          │
├─────────────────────────────────────────────────────────────────┤
│  Middleware Layer                                               │
│  ├── Authentication (JWT/Session)                              │
│  ├── Authorization (Role-based)                                │
│  └── CSRF Protection                                           │
├─────────────────────────────────────────────────────────────────┤
│  Controllers Layer                                              │
│  ├── AuthController                                            │
│  ├── DashboardController                                       │
│  ├── LaporanController                                         │
│  ├── RekomendasiPerbaikan (SPK)                               │
│  ├── UserController                                            │
│  └── [Other Controllers]                                       │
├─────────────────────────────────────────────────────────────────┤
│  Models Layer (Eloquent ORM)                                   │
│  ├── UserModel                                                 │
│  ├── LaporanModel                                              │
│  ├── FasilitasModel                                            │
│  ├── TugasModel                                                │
│  └── [Other Models]                                            │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
                    ┌─────────────────┐
                    │  MySQL Database │
                    │                 │
                    │  ┌───────────┐  │
                    │  │ m_user    │  │
                    │  │ m_laporan │  │
                    │  │ m_tugas   │  │
                    │  │ m_fasilitas│ │
                    │  │ [etc...]  │  │
                    │  └───────────┘  │
                    └─────────────────┘
```

## Data Flow Architecture

### Report Creation Flow
```
User Input (Report) 
        │
        ▼
┌─────────────────┐
│  Validation     │
│  - Form Rules   │
│  - File Upload  │
│  - Permissions  │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Store Report   │
│  - m_laporan    │
│  - t_detail_laporan │
│  - File Storage │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Notification   │
│  - Admin Alert  │
│  - Status Update│
└─────────────────┘
```

### SPK (Decision Support System) Flow
```
Active Reports
        │
        ▼
┌─────────────────┐
│  Data Collection│
│  - Urgency      │
│  - Damage Level │
│  - Reporter Count│
│  - Repair Cost  │
│  - Role Points  │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  TOPSIS Algorithm│
│  - Normalize    │
│  - Apply Weights│
│  - Calculate    │
│  - Rank Results │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Recommendations│
│  - Priority List│
│  - Score Display│
│  - Export Report│
└─────────────────┘
```

## Database Entity Relationship

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   m_roles   │────│   m_user    │────│  m_laporan  │
│             │ 1:N│             │ 1:N│             │
│ roles_id    │    │ user_id     │    │ laporan_id  │
│ roles_nama  │    │ roles_id    │    │ user_id     │
│ roles_kode  │    │ username    │    │ status_id   │
│ poin_roles  │    │ name        │    │ tanggal_lapor│
└─────────────┘    │ password    │    │ jumlah_pelapor│
                   │ NIM/NIP     │    └─────────────┘
                   └─────────────┘           │
                                            │ 1:N
                                            ▼
                   ┌─────────────┐    ┌─────────────┐
                   │ m_fasilitas │────│t_detail_laporan│
                   │             │ 1:N│             │
                   │ fasilitas_id│    │ detail_id   │
                   │ ruangan_id  │    │ laporan_id  │
                   │ fasilitas_nama│  │ fasilitas_id│
                   │ tingkat_urgensi│ │ deskripsi   │
                   └─────────────┘    │ foto_bukti  │
                          │           └─────────────┘
                          │ N:1
                          ▼
                   ┌─────────────┐
                   │  m_ruangan  │
                   │             │
                   │ ruangan_id  │
                   │ lantai_id   │
                   │ ruangan_nama│
                   └─────────────┘
                          │ N:1
                          ▼
                   ┌─────────────┐
                   │  m_lantai   │
                   │             │
                   │ lantai_id   │
                   │ lantai_nama │
                   └─────────────┘
```

## User Role Permissions Matrix

| Feature/Action              | Admin | Pelapor | Teknisi | Sarpras | Dosen |
|----------------------------|-------|---------|---------|---------|-------|
| Create Reports             |   ✓   |    ✓    |    ✓    |    ✓    |   ✓   |
| View All Reports           |   ✓   |    ✗    |    ✓    |    ✓    |   ✓   |
| Validate Reports           |   ✓   |    ✗    |    ✗    |    ✓    |   ✓   |
| Assign Tasks              |   ✓   |    ✗    |    ✗    |    ✓    |   ✗   |
| Complete Tasks            |   ✗   |    ✗    |    ✓    |    ✗    |   ✗   |
| View SPK Results          |   ✓   |    ✗    |    ✓    |    ✓    |   ✗   |
| Manage Users              |   ✓   |    ✗    |    ✗    |    ✗    |   ✗   |
| Manage Facilities         |   ✓   |    ✗    |    ✗    |    ✓    |   ✗   |
| Export Reports            |   ✓   |    ✗    |    ✓    |    ✓    |   ✓   |
| Dashboard Analytics       |   ✓   |    ✗    |    ✓    |    ✓    |   ✗   |

## Technology Stack Dependencies

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Layer                           │
├─────────────────────────────────────────────────────────────┤
│  AdminLTE 3.x │ Bootstrap 5.x │ jQuery 3.x │ Chart.js      │
│  DataTables   │ Font Awesome  │ SweetAlert │ Select2       │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                    Backend Layer                            │
├─────────────────────────────────────────────────────────────┤
│            Laravel 10.x Framework                           │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │ Eloquent ORM│ │   Blade     │ │  Artisan    │           │
│  │             │ │  Templates  │ │   CLI       │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
│                                                             │
│  Third-party Packages:                                      │
│  ├── tymon/jwt-auth (Authentication)                       │
│  ├── yajra/laravel-datatables (Data Tables)               │
│  ├── barryvdh/laravel-dompdf (PDF Generation)             │
│  ├── phpoffice/phpspreadsheet (Excel Operations)          │
│  └── spatie/laravel-ignition (Error Handling)             │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                   Database Layer                            │
├─────────────────────────────────────────────────────────────┤
│               MySQL 5.7+ / MariaDB                         │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │   Master    │ │  User Data  │ │   Reports   │           │
│  │    Data     │ │             │ │    Data     │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
└─────────────────────────────────────────────────────────────┘
```

## Deployment Architecture

### Development Environment
```
Developer Machine
├── Git Repository
├── PHP 8.1+ with Extensions
├── Composer Dependencies
├── Node.js + NPM
├── Local MySQL Database
└── Laravel Development Server
```

### Production Environment
```
Production Server
├── Web Server (Apache/Nginx)
├── PHP-FPM Process Manager
├── MySQL Database Server
├── File Storage (Local/Cloud)
├── SSL Certificate
├── Backup System
└── Monitoring Tools
```

## Security Architecture

### Authentication Flow
```
User Login Request
        │
        ▼
┌─────────────────┐
│  Validate       │
│  Credentials    │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Generate JWT   │
│  Token & Session│
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Set Session    │
│  & Return Token │
└─────────────────┘
```

### Authorization Middleware
```
Incoming Request
        │
        ▼
┌─────────────────┐
│  Check Auth     │
│  Status         │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Verify Role    │
│  Permissions    │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│  Allow/Deny     │
│  Access         │
└─────────────────┘
```

## Performance Optimization Strategies

### Database Optimization
- **Indexes**: All foreign keys and frequently queried columns
- **Query Optimization**: Eager loading relationships
- **Connection Pooling**: Efficient database connections
- **Caching**: Redis/Memcached for session and query caching

### Application Optimization
- **OPCache**: PHP bytecode caching
- **Route Caching**: Pre-compiled route definitions
- **Config Caching**: Cached configuration files
- **View Caching**: Compiled Blade templates

### Frontend Optimization
- **Asset Bundling**: Webpack/Vite compilation
- **Minification**: CSS/JS compression
- **CDN Integration**: Static asset delivery
- **Lazy Loading**: Progressive content loading

---

*This technical architecture documentation complements the main code documentation and provides a visual understanding of the system structure and data flow.*