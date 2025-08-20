# LaporSana - Developer Quick Reference

## Quick Start Commands

### Initial Setup
```bash
# Clone and setup
git clone https://github.com/AlexanderDev2004/LaporSana.git
cd LaporSana
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Development Commands
```bash
# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database operations
php artisan migrate:refresh
php artisan db:seed
php artisan migrate:status
```

## Important File Locations

### Configuration
- `.env` - Environment configuration
- `config/app.php` - Application settings
- `config/database.php` - Database configuration
- `config/auth.php` - Authentication settings

### Models
- `app/Models/UserModel.php` - User management
- `app/Models/LaporanModel.php` - Report system
- `app/Models/FasilitasModel.php` - Facility management
- `app/Models/TugasModel.php` - Task management

### Controllers
- `app/Http/Controllers/AuthController.php` - Authentication
- `app/Http/Controllers/DashboardController.php` - Dashboard logic
- `app/Http/Controllers/LaporanController.php` - Report management
- `app/Http/Controllers/RekomendasiPerbaikan.php` - SPK system

### Views
- `resources/views/admin/` - Admin interface
- `resources/views/pelapor/` - Reporter interface
- `resources/views/teknisi/` - Technician interface
- `resources/views/auth/` - Authentication pages

### Routes
- `routes/web.php` - Web routes
- `routes/api.php` - API routes

## Database Quick Reference

### Key Tables
```sql
-- User management
m_roles         # User roles
m_user          # User accounts

-- Facility structure
m_lantai        # Floor/levels
m_ruangan       # Rooms
m_fasilitas     # Facility items

-- Reporting system
m_laporan       # Main reports
t_detail_laporan # Report details
t_dukung_laporan # Report endorsements

-- Task management
m_tugas         # Repair tasks
t_detail_tugas  # Task details
m_riwayat_perbaikan # Repair history

-- SPK system
m_kriteria      # SPK criteria
t_rekomperbaikan # SPK recommendations
```

### Common Queries
```php
// Get user with role
$user = UserModel::with('role')->find($id);

// Get reports with details
$reports = LaporanModel::with(['details.fasilitas', 'user', 'status'])->get();

// Get facilities by room
$facilities = FasilitasModel::where('ruangan_id', $roomId)->get();

// Get active tasks
$tasks = TugasModel::where('status_id', 1)->with('laporan')->get();
```

## API Endpoints Reference

### Authentication
```http
POST /login          # User login
GET  /logout         # User logout
```

### Data Tables (AJAX)
```http
GET /admin/users/list     # Users DataTable
GET /admin/laporan/list   # Reports DataTable
GET /admin/tugas/list     # Tasks DataTable
```

### SPK System
```http
GET  /admin/spk           # SPK dashboard
POST /admin/perbarui-data # Update SPK data
GET  /admin/spk/spk_steps # Step-by-step view
```

## Role-Based URLs

### Admin Routes (role_id: 1)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/roles` - Role management
- `/admin/lantai` - Floor management
- `/admin/ruangan` - Room management
- `/admin/fasilitas` - Facility management
- `/admin/spk` - SPK system

### Pelapor Routes (role_id: 2)
- `/pelapor/dashboard` - Reporter dashboard
- `/pelapor/laporan` - Create/view reports
- `/pelapor/profile` - Profile management

### Teknisi Routes (role_id: 3)
- `/teknisi/dashboard` - Technician dashboard
- `/teknisi/tugas` - Task management
- `/teknisi/riwayat` - Repair history

### Sarpras Routes (role_id: 4)
- `/sarpras/dashboard` - Infrastructure dashboard
- `/sarpras/validasi` - Report validation
- `/sarpras/tugas` - Task assignment

## Code Style Guide

### Naming Conventions
```php
// Models: PascalCase with 'Model' suffix
class UserModel extends Model {}

// Controllers: PascalCase with 'Controller' suffix
class LaporanController extends Controller {}

// Methods: camelCase
public function createReport() {}

// Variables: camelCase
$reportData = [];

// Database tables: snake_case with prefix
m_user, t_detail_laporan
```

### File Organization
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin-specific controllers
│   │   ├── Pelapor/        # Reporter controllers
│   │   └── Teknisi/        # Technician controllers
│   ├── Middleware/
│   └── Requests/
├── Models/                 # Eloquent models
└── Providers/             # Service providers

resources/
├── views/
│   ├── admin/             # Admin views
│   ├── pelapor/           # Reporter views
│   ├── teknisi/           # Technician views
│   └── layouts/           # Layout templates
└── lang/                  # Language files
```

## Common Patterns

### Controller Pattern
```php
class ExampleController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Page Title',
            'list' => ['Home', 'Current Page']
        ];
        
        $active_menu = 'menu_key';
        
        return view('page.view', compact('breadcrumb', 'active_menu'));
    }
    
    public function list(Request $request)
    {
        $items = Model::select('columns')->with('relations');
        
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('actions', function($item) {
                return view('components.actions', compact('item'));
            })
            ->make(true);
    }
}
```

### Model Relationships
```php
// One-to-Many
public function details()
{
    return $this->hasMany(DetailModel::class, 'foreign_key', 'local_key');
}

// Many-to-One
public function parent()
{
    return $this->belongsTo(ParentModel::class, 'foreign_key', 'parent_key');
}

// Many-to-Many
public function supports()
{
    return $this->belongsToMany(UserModel::class, 'pivot_table');
}
```

### Validation Pattern
```php
$request->validate([
    'field' => 'required|string|max:255',
    'email' => 'required|email|unique:table,column',
    'file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
]);
```

## Debugging Tips

### Common Issues
```php
// Check user role
dd(Auth::user()->role);

// Debug query
DB::enableQueryLog();
// ... your queries
dd(DB::getQueryLog());

// Check route parameters
dd($request->all());

// Verify middleware
dd($request->user());
```

### Log Locations
- `storage/logs/laravel.log` - Application logs
- `storage/logs/laravel-{date}.log` - Daily logs

### Useful Artisan Commands
```bash
# View routes
php artisan route:list

# Check configuration
php artisan config:show

# Debug events
php artisan event:list

# Database inspection
php artisan tinker
>>> User::all()
>>> DB::table('m_user')->get()
```

## Environment Variables

### Required Environment Variables
```env
APP_NAME=LaporSana
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laporsana
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=...
JWT_ALGO=HS256
JWT_TTL=60
```

## Testing Guidelines

### Test Structure
```php
class ExampleTest extends TestCase
{
    public function test_user_can_create_report()
    {
        $user = UserModel::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/pelapor/laporan', [
                'fasilitas_id' => 1,
                'deskripsi' => 'Test report'
            ]);
            
        $response->assertStatus(302);
        $this->assertDatabaseHas('m_laporan', [
            'user_id' => $user->user_id
        ]);
    }
}
```

### Running Tests
```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=test_user_can_create_report

# With coverage
php artisan test --coverage
```

---

*This quick reference guide provides essential information for developers working with the LaporSana codebase. For detailed information, refer to the main documentation files.*