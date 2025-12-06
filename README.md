# HRM-Pro - Multi-Tenant HR Management SaaS

A comprehensive Multi-Tenant HR Management System built with **Laravel 11**, featuring attendance tracking, payroll processing, leave management, recruitment (ATS), performance management, and asset tracking.

## 🚀 Features

### Core Modules
- **Multi-Tenancy**: Single database with tenant_id scoping
- **Organization Management**: Departments, Designations, Shifts
- **Employee Management**: Complete employee lifecycle from onboarding to exit
- **Attendance System**: Biometric integration, manual punch, geo-fencing
- **Leave Management**: Multiple leave types, accrual, approval workflow
- **Payroll Processing**: Automated salary calculation with LOP, overtime, tax
- **Recruitment (ATS)**: Job postings, candidate pipeline, interview scheduling
- **Performance Management**: Goals, KPIs, appraisal cycles
- **Asset Management**: Track company assets assigned to employees
- **Document Center**: Employee documents and company policies
- **Employee Self-Service**: Dedicated portal for employees

### Key Capabilities
- ✅ Biometric device integration via API
- ✅ Automated attendance calculation (late, early departure, overtime)
- ✅ Complex payroll generation with tax slabs
- ✅ Leave accrual (monthly/yearly) and carry forward
- ✅ Multi-level approval workflows
- ✅ Role-based access control (Super Admin, Company Admin, Manager, Employee)
- ✅ Tenant-specific branding (logo, colors)
- ✅ Subscription management with feature flags

## 📋 Requirements

- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js & NPM (for frontend assets)
- Laravel 11

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd HR
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database:
```env
DB_DATABASE=hrm_pro
DB_USERNAME=root
DB_PASSWORD=your_password

APP_DOMAIN=hrm-pro.test
TENANT_IDENTIFICATION=subdomain
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Seed Database (Optional)
```bash
php artisan db:seed --class=SuperAdminSeeder
php artisan db:seed --class=DemoTenantSeeder
```

### 6. Build Frontend Assets
```bash
npm run dev
# or for production
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🏗️ Architecture

### Multi-Tenancy Strategy
- **Single Database** with `tenant_id` column in all tenant-scoped tables
- **Subdomain-based** tenant identification (e.g., `acme.hrm-pro.test`)
- **Global Scope** automatically filters queries by current tenant
- **Tenant Middleware** identifies and sets tenant context

### Core Services

#### AttendanceCalculatorService
```php
// Sync biometric data
$attendance = $attendanceService->syncBiometricData([
    'device_id' => 'BIO001',
    'employee_code' => 'EMP001',
    'timestamp' => '2025-11-27 09:15:00',
]);

// Calculate metrics
$attendanceService->calculateAttendanceMetrics($attendance);
```

#### PayrollGeneratorService
```php
// Generate payroll for an employee
$payroll = $payrollService->generatePayroll($employee, 11, 2025);

// Bulk generation
$results = $payrollService->generateBulkPayroll(11, 2025);

// Generate PDF payslip
$path = $payrollService->generatePayslipPDF($payroll);
```

#### LeaveAccrualService
```php
// Accrue monthly leaves (run via cron)
$leaveService->accrueMonthlyLeaves();

// Process carry forward
$leaveService->processCarryForward(2024);
```

## 🔌 API Integration

### Biometric Device Integration

**Endpoint**: `POST /api/biometric-push`

**Headers**:
```
Authorization: Bearer {device_api_token}
Content-Type: application/json
```

**Request Body**:
```json
{
    "device_id": "BIO001",
    "employee_code": "EMP001",
    "timestamp": "2025-11-27 09:15:00",
    "latitude": 12.9716,
    "longitude": 77.5946
}
```

**Response**:
```json
{
    "success": true,
    "message": "Attendance recorded successfully",
    "data": {
        "attendance_id": 123,
        "employee_name": "John Doe",
        "punch_in": "2025-11-27 09:15:00",
        "status": "present",
        "is_late": true,
        "late_minutes": 15
    }
}
```

## 📊 Database Schema

### Key Tables
- `tenants` - Company/organization data
- `users` - Authentication (linked to tenants)
- `employees` - Employee master data
- `attendances` - Daily attendance records
- `payrolls` - Monthly payroll records
- `leave_requests` - Leave applications
- `leave_balances` - Leave balance tracking
- `candidates` - Recruitment pipeline
- `assets` - Company asset inventory
- `appraisals` - Performance reviews

See [implementation_plan.md](./docs/implementation_plan.md) for complete schema.

## 🔐 Security

- **Tenant Isolation**: Automatic scoping prevents cross-tenant data access
- **API Authentication**: Token-based auth for biometric devices
- **Data Encryption**: Sensitive data (salary, banking) encrypted at rest
- **Role-Based Access**: Policies control access to each module
- **Audit Logging**: Track all critical operations

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --filter=AttendanceCalculatorServiceTest
php artisan test --filter=PayrollGeneratorServiceTest
php artisan test --filter=TenantScopingTest
```

## 📅 Scheduled Tasks

Add to your cron:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled jobs:
- **Daily**: Process attendance status for previous day
- **Monthly**: Accrue monthly leaves (1st of month)
- **Yearly**: Accrue yearly leaves (Jan 1)
- **Yearly**: Process leave carry forward (Jan 1)

## 🎨 Frontend Stack

- **Blade Templates** with Livewire for reactivity
- **Tailwind CSS** for styling
- **Alpine.js** for lightweight interactions
- **FilamentPHP** (optional) for admin panel

## 📝 License

This project is proprietary software. All rights reserved.

## 👥 Support

For support, email support@hrm-pro.com or create an issue in the repository.

---

**Built with ❤️ using Laravel 11**
