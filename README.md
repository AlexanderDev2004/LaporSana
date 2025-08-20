![LaporSana Logo](public/LaporSana.png)

## LaporSana - Campus Facility Reporting System

LaporSana adalah sistem pelaporan dan pengelolaan fasilitas kampus berbasis web yang dilengkapi dengan Sistem Pendukung Keputusan (SPK) untuk memberikan rekomendasi prioritas perbaikan fasilitas.

### 🌟 Key Features
- **Multi-Role System**: Admin, Pelapor, Teknisi, Sarpras, dan Dosen
- **Facility Reporting**: Sistem pelaporan kerusakan fasilitas dengan foto bukti
- **Decision Support System**: Algoritma TOPSIS untuk prioritas perbaikan
- **Task Management**: Pengelolaan tugas perbaikan dari laporan hingga selesai
- **Analytics Dashboard**: Dashboard dengan statistik dan grafik real-time
- **Export Capabilities**: Export laporan ke PDF dan Excel

### 🛠️ Technology Stack
- **Backend**: Laravel 10.x, PHP 8.1+
- **Frontend**: AdminLTE, Bootstrap, jQuery, Chart.js
- **Database**: MySQL 5.7+
- **Authentication**: JWT + Session-based
- **Libraries**: DataTables, DomPDF, PhpSpreadsheet

## 📚 Documentation

### Complete Documentation Set
- **[📖 CODE_DOCUMENTATION.md](CODE_DOCUMENTATION.md)** - Comprehensive code documentation
- **[🏗️ ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture and technical diagrams  
- **[⚡ DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md)** - Quick reference for developers

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js and NPM (optional, for frontend development)

### Installation
```bash
# Clone repository
git clone https://github.com/AlexanderDev2004/LaporSana.git
cd LaporSana

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laporsana
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

### 🧪 Testing
```bash
# Run all tests
php artisan test

# Code formatting
./vendor/bin/pint
```
## 👥 User Roles & Access

| Role | Description | Key Features |
|------|-------------|--------------|
| **Admin** | System administrator | Full system access, user management, SPK system |
| **Pelapor** | Report creators (students/staff) | Create reports, view own reports |
| **Teknisi** | Technicians | Handle repair tasks, update task status |
| **Sarpras** | Infrastructure staff | Validate reports, assign tasks |
| **Dosen** | Lecturers | Validate reports, support reports |

## 📊 System Architecture

### Application Structure
```
LaporSana/
├── app/
│   ├── Http/Controllers/    # Business logic
│   ├── Models/             # Database models
│   └── Middleware/         # Authentication & authorization
├── resources/views/        # Blade templates
├── database/migrations/    # Database schema
└── routes/                # Application routes
```

### Key Workflows
1. **Report Creation** → Validation → Task Assignment → Repair → Completion
2. **SPK Analysis** → Data Collection → TOPSIS Algorithm → Priority Ranking
3. **User Management** → Role Assignment → Permission Control

## 🔧 Development

### Common Commands
```bash
# Development
php artisan serve
php artisan test
./vendor/bin/pint

# Database
php artisan migrate:refresh
php artisan db:seed

# Cache management
php artisan cache:clear
php artisan config:clear
```

### Code Standards
- **PSR-12** PHP coding standards
- **Laravel conventions** for naming and structure
- **Comprehensive documentation** for all features

## 📈 Features Overview

### Reporting System
- Multi-step report creation with facility selection
- Photo evidence upload
- Report validation workflow
- Support/endorsement system

### Decision Support System (SPK)
- TOPSIS algorithm implementation
- Multi-criteria analysis (urgency, damage, cost, etc.)
- Automated priority ranking
- Step-by-step calculation transparency

### Task Management
- Automatic task creation from validated reports
- Technician assignment and tracking
- Progress monitoring and completion
- Quality rating system

### Analytics & Reporting
- Real-time dashboard with statistics
- Interactive charts and graphs
- Export capabilities (PDF/Excel)
- Historical trend analysis

## 🛡️ Security Features

- **Role-based Access Control** (RBAC)
- **JWT Token Authentication**
- **CSRF Protection**
- **Input Validation & Sanitization**
- **File Upload Security**
- **Database Query Protection**

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For support and questions:
- 📧 Create an issue in this repository
- 📖 Check the [documentation](CODE_DOCUMENTATION.md)
- 💬 Contact the development team

---

**LaporSana** - Making campus facility management smarter and more efficient through technology.

## 👥 Contributors

<div align="center">

| [![Alexander](https://github.com/AlexanderDev2004.png?size=100)](https://github.com/AlexanderDev2004) | [![NathanaelGracedo](https://github.com/NathanaelGracedo.png?size=100)](https://github.com/NathanaelGracedo) | [![Fatikah002](https://github.com/Fatikah002.png?size=100)](https://github.com/Fatikah002) | [![Danennndraaa](https://github.com/Danennndraaa.png?size=100)](https://github.com/Danennndraaa) | [![om-ica](https://github.com/om-ica.png?size=100)](https://github.com/om-ica) |
|:---:|:---:|:---:|:---:|:---:|
| [**Alexander**](https://github.com/AlexanderDev2004)<br><sub>Fullstack Developer</sub> | [**NathanaelGracedo**](https://github.com/NathanaelGracedo)<br><sub>Backend Developer</sub> | [**Fatikah002**](https://github.com/Fatikah002)<br><sub>Frontend Developer</sub> | [**Danennndraaa**](https://github.com/Danennndraaa)<br><sub>UI/UX Designer</sub> | [**om-ica**](https://github.com/om-ica)<br><sub>Project Manager</sub> |

</div>

