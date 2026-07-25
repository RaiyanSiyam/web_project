# UIU Mart — Enterprise Retail Management System

A production-style retail and point-of-sale management system built with native Object-Oriented PHP and a custom MVC architecture. This system allows organizations to securely manage employees, roles, inventory, and storefront POS operations efficiently.

---

## What is UIU Mart?

UIU Mart is a full-stack enterprise web application that digitizes physical storefront operations. It replaces traditional, fragmented retail software by combining secure employee management, role-based access control, and a strict point-of-sale concurrency system. It utilizes asynchronous JavaScript (Fetch API) to provide a seamless, page-refresh-free experience for administrators and staff.

---

## Roles

| Role        | Description                                                                                            |
| ----------- | -------------------------------------------------------------------------------------------------------- |
| **ADMIN**   | System Administrator. Full access. Can onboard/edit/delete staff, adjust salaries, and manage the entire platform. |
| **MANAGER** | Store Manager. Can oversee inventory, view financial reports, and manage daily analytics.               |
| **CASHIER** | Point of Sale operator. Strictly restricted to the POS terminal for processing customer transactions.   |

---

## What Each Role Can Do

### Admin

- Create, update, delete Employee profiles
- Assign dynamic system privilege roles (Admin, Manager, Cashier)
- View real-time active session statuses (Online/Offline)
- Manage system-wide access and security overrides

### Manager

- View inventory and low-stock alerts
- Generate and view sales analytics and reports
- Manage product pricing and stock quantities

### Cashier

- Log securely into the isolated POS terminal
- Process customer transactions and cart totals
- Cannot access management panels or employee records

---

## How The Employee Onboarding & Auth Process Works

```
1. Admin logs into the dashboard and navigates to the Employee Directory.
2. Admin fills out the onboarding form with Name, Email, Salary, and Role.
3. System intercepts form submission via JS Fetch API and sends it to UserController.
4. Backend verifies Admin session token, hashes password via BCrypt, and inserts via PDO.
5. System returns a JSON success object, triggering a UI Toast Notification without reloading the page.
6. Cashier attempts to log in with new credentials.
7. System checks active sessions (Concurrency Guard: Max 4 active Cashiers).
8. If < 4, session is regenerated (Session Fixation guard) and Cashier is routed to POS.
```

---

## Tech Stack

| Technology          | Purpose                            |
| -------------------- | ----------------------------------- |
| PHP 8.x (OOP)        | Server-side language & core logic   |
| Custom MVC           | Application architecture            |
| MySQL / MariaDB      | Relational Database                 |
| PDO                  | Database abstraction & security     |
| Vanilla JavaScript   | Client-side async logic (ES6+)      |
| HTML5 / CSS3         | Layout (Grid/Flexbox) & Animations  |

---

## Project Structure

```
uiu_mart/
│
├── app/
│   ├── config/
│   │   └── db.php               # Singleton database connection wrapper
│   ├── controllers/
│   │   ├── AuthController.php   # Session, login, logout, concurrency limits
│   │   └── UserController.php   # Employee CRUD and JSON response handling
│   └── models/
│       └── User.php             # Raw SQL queries (PDO) for users table
│
├── assets/
│   └── css/
│       └── main.css             # Global styles, UI tokens, toast animations
│
├── includes/
│   ├── auth_guard.php           # Session security verifier (protects pages)
│   ├── navbar.php               # Top navigation header
│   └── sidebar.php              # Side navigation menu
│
└── pages/
    └── users/
        └── index.php            # Employee Directory view & JS Modal logic
```

---

## Architecture

```
Client Request (Browser / Fetch API)
    ↓
Controller Layer (Validates input, checks RBAC session)
    ↓
Model Layer (Prepares PDO Statements)
    ↓
Database (MySQL)
    ↓
Controller Layer (Serializes array to JSON)
    ↓
Client Response (JS updates DOM & triggers Toast)
```

---

## Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/uiu-mart.git
```

### 2. Move to local server

Move the uiu_mart folder into your local server's web directory (e.g., htdocs for XAMPP or www for WAMP).

### 3. Create MySQL database

Open phpMyAdmin (or your SQL CLI) and run:

```sql
CREATE DATABASE uiu_mart;
```

### 4. Import the Schema

Run the following core table setup in your new database:

```sql
CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
role_id INT DEFAULT 3,
first_name VARCHAR(50) NOT NULL,
last_name VARCHAR(50) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
role ENUM('admin', 'manager', 'cashier') NOT NULL DEFAULT 'cashier',
phone VARCHAR(20) DEFAULT NULL,
salary DECIMAL(10,2) DEFAULT NULL,
status VARCHAR(20) DEFAULT 'active',
last_activity DATETIME DEFAULT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL DEFAULT NULL
);
```

### 5. Create the Superuser (Admin)

```sql
INSERT INTO users (role_id, first_name, last_name, email, password_hash, role, status)
VALUES (1, 'System', 'Administrator', 'admin@uiumart.com', '$2y$10$vEUYPcb66xwTKWES5R6y1O88.Gtgp0/SqdEAGISv0cpq.RhAyx8O6', 'admin', 'active');
```

*(Default Password for this hash is your previously set local environment password).*

### 6. Configure environment

Update app/config/db.php with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your DB username
define('DB_PASS', ''); // Your DB password
define('DB_NAME', 'uiu_mart');
```

### 7. Run the Application

Open your browser and navigate to: http://localhost/uiu_mart/

---

## Application Route Reference

Instead of a REST API, UIU Mart uses an internal action-routing mechanism via its custom MVC controllers.

### Authentication Controller (AuthController.php)

| Method | Endpoint / Action | Description                             | Auth Required |
| ------ | ------------------ | ----------------------------------------- | -------------- |
| POST   | `?action=login`    | Authenticates user, checks concurrency    | No             |
| GET    | `?action=logout`   | Destroys session, redirects to login      | Yes            |

### User Controller (UserController.php)

| Method | Endpoint / Action    | Description                                  | Role Required |
| ------ | --------------------- | ---------------------------------------------- | -------------- |
| GET    | `index()` (Internal)  | Fetches all employees for the view              | Admin          |
| POST   | `?action=create`      | Creates new employee, hashes password           | Admin          |
| POST   | `?action=update`      | Updates profile data (and password if set)      | Admin          |
| POST   | `?action=delete`      | Soft/Hard deletes user from database             | Admin          |

---

## Security & Concurrency Controls

### Hardware Concurrency Guard

To simulate physical register constraints, the system checks the last_activity column.

```sql
SELECT COUNT(*) FROM users WHERE role = 'cashier' AND last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
```

*If this evaluates to >= 4, new Cashier logins are temporarily blocked.*

### Anti-Vulnerability Implementations

- **SQL Injection:** Neutralized via 100% strict usage of PDO Prepared Statements.
- **Session Fixation:** Neutralized via session_regenerate_id(true) upon successful authentication.
- **XSS (Cross-Site Scripting):** Neutralized via htmlspecialchars() wrapping on all data rendered to the DOM.
- **Rainbow Table Attacks:** Neutralized using BCrypt hashing algorithm for all passwords.

---

## Future Updates

### 🔴 High Priority

- [ ] **Data Export** — Allow Admin to export the Employee Roster to CSV/Excel formats.
- [ ] **Pagination** — Implement server-side pagination for the Active Roster table to handle scaling.

### 🟡 Medium Priority

- [ ] **Activity Logs** — Create an audit trail tracking which admin created/deleted specific users.
- [ ] **Password Recovery** — Implement a secure token-based password reset via email.

### 🟢 Nice To Have

- [ ] **Dark Mode** — Toggleable CSS variables for a dark dashboard theme.
- [ ] **Profile Avatars** — Support for uploading user profile images.

---

## License

MIT License

