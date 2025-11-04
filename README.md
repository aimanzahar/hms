# 🏥 Healthcare Management System (HMS)

A comprehensive web-based Hospital Management System built with PHP and SQLite, designed for Malaysian healthcare facilities. This system facilitates efficient management of patient records, doctor appointments, medical records, and billing operations.

## 📋 Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### For Patients
- **Patient Registration** - Self-registration with IC number verification
- **Appointment Booking** - Schedule appointments with available doctors
- **Medical Records** - View personal medical history and records
- **Bill Management** - View and track medical bills
- **Profile Management** - Update personal information and contact details
- **Appointment Tracking** - View upcoming, past, and cancelled appointments

### For Doctors
- **Dashboard** - Overview of daily appointments and patient statistics
- **Patient Management** - View complete patient list and profiles
- **Medical Records** - Create, view, and update patient medical records
- **Appointment Management** - View and manage scheduled appointments
- **Profile Management** - Update professional information and credentials

### General Features
- **Role-Based Access Control** - Secure authentication for patients, doctors, and admins
- **Malaysian Context** - Formatted for Malaysian date formats, currency (RM), and IC numbers
- **Responsive Design** - Mobile-friendly interface
- **SQLite Database** - Lightweight, portable database solution
- **Session Management** - Secure session handling

## 💻 System Requirements

- **Web Server**: Apache (XAMPP, WAMP, or similar)
- **PHP**: Version 7.4 or higher
- **Database**: SQLite 3 (included with PHP)
- **Extensions Required**:
  - PDO_SQLite
  - Session support
  - OpenSSL (for password hashing)

## 🚀 Installation

### Step 1: Clone or Download the Repository

```bash
# If using Git
git clone https://github.com/aimanzahar/hms.git

# Or download and extract the ZIP file
```

### Step 2: Set Up XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Copy the `hms` folder to `C:\xampp\htdocs\` (or your XAMPP htdocs directory)

### Step 3: Configure Apache

1. Open XAMPP Control Panel
2. Start the **Apache** service
3. Ensure port 80 is not being used by another application

### Step 4: Configure the Application

1. Navigate to `config/config.php`
2. Update the `BASE_URL` if needed (default is `/hms`):

```php
define('BASE_URL', '/hms');
```

3. Set the correct timezone (default is Asia/Kuala_Lumpur):

```php
date_default_timezone_set('Asia/Kuala_Lumpur');
```

### Step 5: Initialize the Database

1. Open your web browser
2. Navigate to: `http://localhost/hms/seed.php`
3. This will create the database and populate it with sample data

### Step 6: Access the System

Navigate to: `http://localhost/hms/`

## 🔧 Configuration

### Base URL Configuration

Edit `config/config.php`:

```php
// For root directory
define('BASE_URL', '');

// For subdirectory (e.g., /hms)
define('BASE_URL', '/hms');

// For custom domain
define('BASE_URL', 'https://yourdomain.com');
```

### Timezone Settings

Supported Malaysian timezones:
```php
date_default_timezone_set('Asia/Kuala_Lumpur');
```

### Database Location

The SQLite database is stored at: `data/hms.db`

To reset the database:
1. Delete `data/hms.db`
2. Run `seed.php` again

## 📖 Usage

### First Time Setup

After running the seed script, you can log in with these test accounts:

#### Doctor Accounts
| Email | Password | Specialization |
|-------|----------|----------------|
| dr.ahmad@hms.my | password | General Practitioner |
| dr.siti@hms.my | password | Pediatrician |
| dr.kumar@hms.my | password | Cardiologist |
| dr.tan@hms.my | password | Dermatologist |
| dr.wong@hms.my | password | Orthopedic Surgeon |

#### Patient Accounts
| Email | Password |
|-------|----------|
| ali@example.com | password |
| sarah@example.com | password |
| raj@example.com | password |
| mei@example.com | password |

### Patient Workflow

1. **Register**: Go to `/register.php` to create a new patient account
2. **Login**: Use your credentials at `/login.php`
3. **Book Appointment**: Navigate to `patient/book-appointment.php`
4. **View Medical Records**: Access your records at `patient/medical-records.php`
5. **Check Bills**: View billing information at `patient/bills.php`

### Doctor Workflow

1. **Login**: Use doctor credentials at `/login.php`
2. **Dashboard**: View daily schedule and patient statistics
3. **Manage Appointments**: Review and update appointment statuses
4. **Add Medical Records**: Create records for patients at `doctor/add-record.php`
5. **View Patient Details**: Access complete patient information and history

## 👥 User Roles

### Patient
- Register and manage own account
- Book and manage appointments
- View personal medical records
- View bills
- Update profile information

### Doctor
- View and manage appointments
- Access patient records
- Create and update medical records
- View patient list and details
- Manage professional profile

### Admin (Future Implementation)
- Manage users (doctors and patients)
- System configuration
- Reports and analytics
- Billing management

## 📁 Project Structure

```
hms/
├── assets/
│   └── css/
│       └── style.css          # Main stylesheet
├── config/
│   ├── config.php             # Application configuration
│   └── database.php           # Database connection and schema
├── data/
│   └── hms.db                 # SQLite database file
├── doctor/
│   ├── dashboard.php          # Doctor dashboard
│   ├── appointments.php       # Doctor's appointment list
│   ├── patients.php           # Patient management
│   ├── medical-records.php    # Medical records list
│   ├── add-record.php         # Create new medical record
│   ├── view-patient.php       # Patient details
│   ├── view-appointment.php   # Appointment details
│   ├── view-record.php        # Medical record details
│   └── profile.php            # Doctor profile management
├── patient/
│   ├── dashboard.php          # Patient dashboard
│   ├── appointments.php       # Patient's appointment list
│   ├── book-appointment.php   # Book new appointment
│   ├── medical-records.php    # Patient's medical records
│   ├── bills.php              # Billing information
│   ├── view-appointment.php   # Appointment details
│   ├── view-record.php        # Medical record details
│   └── profile.php            # Patient profile management
├── index.php                  # Main entry point
├── login.php                  # Login page
├── register.php               # Patient registration
├── logout.php                 # Logout handler
├── seed.php                   # Database seeding script
└── README.md                  # This file
```

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('doctor', 'patient', 'admin')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Doctors Table
```sql
CREATE TABLE doctors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    specialization TEXT NOT NULL,
    phone TEXT NOT NULL,
    license_number TEXT UNIQUE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Patients Table
```sql
CREATE TABLE patients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    ic_number TEXT UNIQUE NOT NULL,
    phone TEXT NOT NULL,
    address TEXT,
    date_of_birth DATE NOT NULL,
    gender TEXT CHECK(gender IN ('male', 'female')),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Appointments Table
```sql
CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    doctor_id INTEGER NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT NOT NULL,
    status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'confirmed', 'completed', 'cancelled')),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
```

### Medical Records Table
```sql
CREATE TABLE medical_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    doctor_id INTEGER NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment TEXT NOT NULL,
    prescription TEXT,
    notes TEXT,
    record_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
```

### Bills Table
```sql
CREATE TABLE bills (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    appointment_id INTEGER,
    description TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status TEXT DEFAULT 'unpaid' CHECK(status IN ('paid', 'unpaid', 'pending')),
    bill_date DATE NOT NULL,
    paid_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);
```

## 🔌 API Endpoints

While this is primarily a server-rendered application, the main routes are:

### Public Routes
- `GET /login.php` - Login page
- `POST /login.php` - Authenticate user
- `GET /register.php` - Registration page
- `POST /register.php` - Create new patient account
- `GET /logout.php` - Logout user

### Patient Routes (Authenticated)
- `GET /patient/dashboard.php` - Patient dashboard
- `GET /patient/appointments.php` - View appointments
- `GET /patient/book-appointment.php` - Book appointment form
- `POST /patient/book-appointment.php` - Create appointment
- `GET /patient/medical-records.php` - View medical records
- `GET /patient/bills.php` - View bills
- `GET /patient/profile.php` - View/edit profile

### Doctor Routes (Authenticated)
- `GET /doctor/dashboard.php` - Doctor dashboard
- `GET /doctor/appointments.php` - View appointments
- `GET /doctor/patients.php` - View patient list
- `GET /doctor/medical-records.php` - View all medical records
- `GET /doctor/add-record.php` - Create medical record form
- `POST /doctor/add-record.php` - Save medical record
- `GET /doctor/profile.php` - View/edit profile

## 🔒 Security Features

### Authentication
- Password hashing using PHP's `password_hash()` with bcrypt
- Session-based authentication
- Role-based access control (RBAC)

### Input Validation
- Prepared statements to prevent SQL injection
- Server-side validation for all user inputs
- Password strength requirements (minimum 6 characters)

### Session Security
- Session ID regeneration on login
- Secure session configuration
- Automatic session timeout

### Data Protection
- User passwords are never stored in plain text
- Email uniqueness validation
- IC number uniqueness validation

### Best Practices
```php
// Always use prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// Password verification
if (password_verify($password, $user['password'])) {
    // Login successful
}

// Role checking
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}
```

## 🐛 Troubleshooting

### Database Connection Issues

**Problem**: "Database connection failed"

**Solution**:
1. Ensure the `data/` directory has write permissions
2. Check if PDO_SQLite extension is enabled in PHP
3. Verify `php.ini` has `extension=pdo_sqlite` enabled

### Page Not Found (404)

**Problem**: Pages return 404 errors

**Solution**:
1. Check `BASE_URL` in `config/config.php`
2. Ensure Apache `mod_rewrite` is enabled (if using .htaccess)
3. Verify the correct path in your browser

### Login Issues

**Problem**: Cannot login with credentials

**Solution**:
1. Run `seed.php` to reset and populate database
2. Clear browser cookies and cache
3. Check if sessions are working: `<?php session_start(); phpinfo(); ?>`

### Permission Errors

**Problem**: "Unable to create database file"

**Solution**:
```bash
# Windows (run as administrator)
icacls "f:\xampp\htdocs\hms\data" /grant Users:F

# Or manually:
# Right-click data folder → Properties → Security → Edit → Add Full Control
```

### Blank Pages

**Problem**: Pages load blank with no errors

**Solution**:
1. Enable error reporting in `php.ini`:
   ```ini
   display_errors = On
   error_reporting = E_ALL
   ```
2. Check Apache error logs: `xampp/apache/logs/error.log`
3. Verify all `require_once` paths are correct

### Styling Issues

**Problem**: Pages load without CSS styling

**Solution**:
1. Check if `assets/css/style.css` exists
2. Verify the CSS path in HTML matches `BASE_URL`
3. Clear browser cache (Ctrl+F5)

## 📊 Sample Data

The `seed.php` script creates:
- 5 Doctor accounts with various specializations
- 10 Patient accounts with diverse profiles
- Sample appointments
- Medical records
- Billing records

All sample users have the password: `password`

## 🛠️ Development

### Adding New Features

1. **Create a new page**: Copy an existing page structure
2. **Add database tables**: Update `database.php` schema
3. **Implement functionality**: Use existing patterns for consistency
4. **Test thoroughly**: Check all user roles

### Coding Standards

- Use PSR-12 coding style
- Comment complex logic
- Use prepared statements for all database queries
- Validate all user inputs
- Follow DRY (Don't Repeat Yourself) principle

## 🔄 Future Enhancements

- [ ] Admin panel for system management
- [ ] Email notifications for appointments
- [ ] SMS reminders (via Malaysian SMS gateway)
- [ ] Report generation (PDF export)
- [ ] Advanced search and filtering
- [ ] Prescription printing
- [ ] Lab results management
- [ ] Pharmacy integration
- [ ] Multiple language support (Bahasa Malaysia, English, Chinese)
- [ ] Dark mode theme
- [ ] API for mobile applications
- [ ] Payment gateway integration

## 📝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit your changes: `git commit -am 'Add new feature'`
4. Push to the branch: `git push origin feature-name`
5. Submit a pull request

### Contribution Guidelines

- Follow the existing code style
- Write clear commit messages
- Test your changes thoroughly
- Update documentation as needed
- Ensure backward compatibility

## 📄 License

This project is open-source and available under the MIT License.

```
MIT License

Copyright (c) 2025 HMS Development Team

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 👨‍💻 Authors

- **Aiman Zahar** - Initial development - [aimanzahar](https://github.com/aimanzahar)

## 🙏 Acknowledgments

- Malaysian healthcare standards and practices
- XAMPP for providing an easy PHP development environment
- SQLite for lightweight database solution
- All contributors and testers

## 📞 Support

For issues, questions, or suggestions:

- **GitHub Issues**: [Create an issue](https://github.com/aimanzahar/hms/issues)
- **Email**: support@hms.my
- **Documentation**: This README and inline code comments

## 📚 Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [SQLite Documentation](https://www.sqlite.org/docs.html)
- [XAMPP Documentation](https://www.apachefriends.org/docs/)
- [Malaysian Medical Council](https://www.mmc.gov.my/)

---

**Version**: 1.0.0  
**Last Updated**: November 4, 2025  
**Status**: Active Development

Made with ❤️ for Malaysian Healthcare
