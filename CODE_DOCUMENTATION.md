# LaporSana - Code Documentation

## Project Overview

**LaporSana** is a comprehensive web-based facility reporting and management system designed for campus environments. The application integrates a Decision Support System (SPK) to provide intelligent recommendations for facility repair prioritization.

### Purpose
- Create and manage facility problem reports
- Track repair tasks and workflow
- Provide decision support for repair prioritization
- Manage users with role-based access control
- Monitor facility status and repair history

### Technology Stack
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates with AdminLTE theme
- **Database**: MySQL
- **Authentication**: Laravel Sanctum + JWT
- **UI Framework**: Bootstrap + AdminLTE
- **JavaScript Libraries**: jQuery, DataTables, Chart.js
- **File Generation**: DomPDF for reports, PhpSpreadsheet for Excel
- **Development Tools**: Laravel Pint (code formatting), PHPUnit (testing)

## System Architecture

### MVC Architecture
The application follows Laravel's MVC (Model-View-Controller) pattern:

```
app/
├── Http/Controllers/     # Business logic and request handling
├── Models/              # Database models and relationships
├── Middleware/          # Request filtering and authentication
└── Providers/           # Service providers

resources/views/         # Blade templates for UI
routes/                 # Application routing
database/
├── migrations/         # Database schema definitions
└── seeders/           # Database initial data
```

### Role-Based Access Control
The system implements 5 user roles with different permissions:

1. **Admin (role_id: 1)** - System administrator with full access
2. **Pelapor (role_id: 2)** - Report creators (students/staff)
3. **Teknisi (role_id: 3)** - Technicians who handle repairs
4. **Sarpras (role_id: 4)** - Infrastructure staff
5. **Dosen (role_id: 5)** - Lecturers with report validation rights

## Database Schema

### Core Tables

#### User Management
- **m_roles**: User roles definition
- **m_user**: User accounts with role assignment
- **m_status**: System-wide status definitions

#### Facility Management
- **m_lantai**: Floor/level definitions
- **m_ruangan**: Room definitions linked to floors
- **m_fasilitas**: Facility items within rooms

#### Reporting System
- **m_laporan**: Main report records
- **t_detail_laporan**: Report details with facility specifics
- **t_dukung_laporan**: Report support/endorsement system

#### Task Management
- **m_tugas**: Repair task assignments
- **t_detail_tugas**: Task details with damage assessment
- **m_riwayat_perbaikan**: Repair history and ratings

#### Decision Support System
- **m_kriteria**: SPK criteria definitions
- **t_rekomperbaikan**: SPK recommendations and scoring
- **t_spk_steps**: Step-by-step SPK calculation process

### Key Relationships

```sql
m_user (user_id) → m_laporan (user_id)
m_fasilitas (fasilitas_id) → t_detail_laporan (fasilitas_id)
m_laporan (laporan_id) → m_tugas (laporan_id)
m_tugas (tugas_id) → m_riwayat_perbaikan (tugas_id)
```

## Model Structure

### Core Models

#### UserModel
```php
// Authentication and user management
class UserModel extends Authenticatable
{
    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    
    // Relationships
    public function role()  // belongsTo RoleModel
    
    // Features: avatar handling, role-based permissions
}
```

#### LaporanModel
```php
// Main reporting entity
class LaporanModel extends Model
{
    protected $table = 'm_laporan';
    
    // Relationships
    public function details()  // hasMany LaporanDetail
    public function user()     // belongsTo UserModel
    public function status()   // belongsTo StatusModel
}
```

#### FasilitasModel
```php
// Facility management
class FasilitasModel extends Model
{
    protected $table = 'm_fasilitas';
    
    // Features: urgency levels, facility categorization
    // Relationships with ruangan (room)
}
```

## Controller Architecture

### Main Controllers

#### AuthController
- User authentication (login/logout)
- JWT token management
- Session handling

#### DashboardController
- Dashboard data aggregation
- Statistics calculation
- SPK data preparation
- Chart data generation

#### LaporanController
- Report CRUD operations
- Report validation workflow
- PDF export functionality
- DataTables integration

#### RekomendasiPerbaikan
- Decision Support System (SPK) implementation
- TOPSIS algorithm for repair prioritization
- Data calculation and ranking
- Step-by-step SPK visualization

#### UserController
- User management CRUD
- Role assignment
- Import/Export functionality
- Profile management

### Route Structure

```php
// Public routes
Route::get('login', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'postlogin']);

// Admin routes (role 1)
Route::middleware(['auth', 'authorize:1'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/spk', [RekomendasiPerbaikan::class, 'tampilkanSPK']);
    // User, Role, Facility management routes...
});

// Pelapor routes (role 2)
Route::middleware(['auth', 'authorize:2'])->group(function () {
    // Report creation and management
});

// Teknisi routes (role 3)
Route::middleware(['auth', 'authorize:3'])->group(function () {
    // Task management and completion
});
```

## Key Features

### 1. Facility Reporting System
- **Report Creation**: Users can create reports with facility details, photos, descriptions
- **Multi-level Validation**: Reports go through validation stages
- **Support System**: Other users can endorse/support reports
- **Photo Evidence**: Image upload for damage documentation

### 2. Decision Support System (SPK)
- **TOPSIS Algorithm**: Multi-criteria decision analysis
- **Criteria**: Urgency, damage level, reporter count, repair cost, role points
- **Automated Ranking**: System generates repair priority recommendations
- **Step-by-step Calculation**: Transparent decision process

### 3. Task Management
- **Automatic Task Creation**: From validated reports
- **Technician Assignment**: Based on availability and expertise
- **Progress Tracking**: Status updates throughout repair process
- **Completion Rating**: Quality feedback system

### 4. User Management
- **Role-based Access**: Different interfaces per user type
- **Profile Management**: User information and avatar handling
- **Import/Export**: Bulk user operations via Excel
- **Authentication**: Secure login with JWT tokens

### 5. Reporting and Analytics
- **Dashboard Metrics**: Real-time statistics
- **Charts and Graphs**: Visual data representation
- **Export Capabilities**: PDF and Excel report generation
- **Historical Data**: Repair history and trends

## Workflow Examples

### Report Creation Workflow
1. **Pelapor** creates report with facility details
2. System validates facility existence and user permissions
3. Report enters "Pending" status
4. **Admin/Sarpras** validates report
5. If approved, report status changes to "Validated"
6. System automatically creates repair task
7. **Teknisi** assigned to handle repair
8. Task completion updates repair history

### SPK Calculation Process
1. System gathers criteria data from active reports
2. Normalizes decision matrix using TOPSIS algorithm
3. Applies weights to criteria
4. Calculates positive and negative ideal solutions
5. Computes relative closeness scores
6. Ranks alternatives by score
7. Generates recommendations for repair prioritization

## Installation and Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js and NPM (for frontend assets)

### Installation Steps
```bash
# Clone repository
git clone https://github.com/AlexanderDev2004/LaporSana.git
cd LaporSana

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laporsana
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run database migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed

# Install frontend dependencies
npm install

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Production Deployment
```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

## Security Features

### Authentication
- **JWT Token Authentication**: Secure API access
- **Session-based Authentication**: Web interface security
- **Password Hashing**: Bcrypt encryption
- **Role-based Authorization**: Middleware protection

### Input Validation
- **Form Request Validation**: Laravel validation rules
- **File Upload Security**: Type and size restrictions
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Blade template escaping

### Data Protection
- **CSRF Protection**: Token-based form security
- **Database Encryption**: Sensitive data protection
- **File Storage Security**: Proper directory permissions
- **Error Handling**: Secure error reporting

## API Endpoints

### Authentication Endpoints
```
POST /login - User authentication
GET /logout - User logout
```

### Data Endpoints (AJAX)
```
GET /admin/users/list - User DataTable data
GET /admin/laporan/list - Report DataTable data
GET /admin/dashboard/spk - SPK calculation data
POST /admin/perbarui-data - Update SPK data
```

## Testing

### Test Structure
```
tests/
├── Feature/          # Integration tests
│   └── ExampleTest.php
└── Unit/             # Unit tests
    └── ExampleTest.php
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## Performance Considerations

### Database Optimization
- **Indexed Columns**: Primary keys and foreign keys properly indexed
- **Query Optimization**: Eager loading for relationships
- **Pagination**: Large datasets handled with pagination

### Caching Strategy
- **Config Caching**: Production configuration caching
- **Route Caching**: Compiled route definitions
- **View Caching**: Compiled Blade templates

### Frontend Optimization
- **Asset Minification**: CSS and JS compression
- **Image Optimization**: Proper image sizing and formats
- **DataTables**: Server-side processing for large datasets

## Maintenance and Monitoring

### Logging
- **Application Logs**: Laravel logging system
- **Error Tracking**: Detailed error logging
- **User Activity**: Authentication and action logging

### Backup Strategy
- **Database Backups**: Regular automated backups
- **File Storage Backups**: User uploads and application files
- **Configuration Backups**: Environment and config files

### Updates and Patches
- **Dependency Updates**: Regular Composer updates
- **Security Patches**: Timely Laravel and PHP updates
- **Feature Updates**: Version-controlled deployments

## Contributing Guidelines

### Code Standards
- **PSR-12**: PHP coding standards
- **Laravel Pint**: Automated code formatting
- **DocBlocks**: Comprehensive code documentation

### Development Workflow
1. Create feature branch from main
2. Implement changes with tests
3. Run code formatting and tests
4. Submit pull request for review
5. Merge after approval and testing

### Commit Message Format
```
type(scope): description

feat(auth): add JWT token refresh
fix(reports): resolve validation error
docs(api): update endpoint documentation
```

## Support and Documentation

### Additional Resources
- [Laravel Documentation](https://laravel.com/docs)
- [AdminLTE Documentation](https://adminlte.io/docs)
- [DataTables Documentation](https://datatables.net/manual)

### Team Contacts
- **Full-stack Developer**: Alexander
- **Backend Developer**: NathanaelGracedo
- **Frontend Developer**: Fatikah002
- **UI/UX Designer**: Danennndraaa
- **Project Manager**: om-ica

---

*This documentation provides a comprehensive overview of the LaporSana codebase. For specific implementation details, refer to the inline code comments and Laravel documentation.*