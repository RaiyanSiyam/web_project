UIU Mart — Enterprise Retail Management SystemA production-style retail and point-of-sale management system built with native Object-Oriented PHP and a custom MVC architecture. This system allows store administrators to seamlessly manage employees, roles, inventory, and point-of-sale (POS) operations securely.What is UIU Mart?UIU Mart is a full-stack enterprise web application that digitizes physical storefront operations. It replaces traditional, fragmented retail software by combining secure employee management, role-based access control, and a strict point-of-sale concurrency system. It utilizes asynchronous JavaScript (Fetch API) to provide a seamless, page-refresh-free experience for administrators and staff.RolesRoleDescriptionADMINSystem Administrator. Full access. Can onboard/edit/delete staff, adjust salaries, and manage the entire platform.MANAGERStore Manager. Can oversee inventory, view financial reports, and manage daily analytics.CASHIERPoint of Sale operator. Strictly restricted to the POS terminal for processing customer transactions.What Each Role Can DoAdminCreate, update, delete Employee profilesAssign dynamic system privilege roles (Admin, Manager, Cashier)View real-time active session statuses (Online/Offline)Manage system-wide access and security overridesManagerView inventory and low-stock alertsGenerate and view sales analytics and reportsManage product pricing and stock quantitiesCashierLog securely into the isolated POS terminalProcess customer transactions and cart totalsCannot access management panels or employee recordsHow The Employee Onboarding & Auth Process Works1. Admin logs into the dashboard and navigates to the Employee Directory.
2. Admin fills out the onboarding form with Name, Email, Salary, and Role.
3. System intercepts form submission via JS Fetch API and sends it to UserController.
4. Backend verifies Admin session token, hashes password via BCrypt, and inserts via PDO.
5. System returns a JSON success object, triggering a UI Toast Notification without reloading the page.
6. Cashier attempts to log in with new credentials.
7. System checks active sessions (Concurrency Guard: Max 4 active Cashiers).
8. If < 4, session is regenerated (Session Fixation guard) and Cashier is routed to POS.
Tech StackTechnologyPurposePHP 8.x (OOP)Server-side language & core logicCustom MVCApplication architectureMySQL / MariaDBRelational DatabasePDODatabase abstraction & securityVanilla JavaScriptClient-side async logic (ES6+)HTML5 / CSS3Layout (Grid/Flexbox) & AnimationsProject Structureuiu_mart/
│
├── app/
│   ├── config/
│   │   └── db.php                # Singleton database connection wrapper
│   ├── controllers/
│   │   ├── AuthController.php    # Session, login, logout, concurrency limits
│   │   └── UserController.php    # Employee CRUD and JSON response handling
│   └── models/
│       └── User.php              # Raw SQL queries (PDO) for users table
│
├── assets/
│   └── css/
│       └── main.css              # Global styles, UI tokens, toast animations
│
├── includes/
│   ├── auth_guard.php            # Session security verifier (protects pages)
│   ├── navbar.php                # Top navigation header
│   └── sidebar.php               # Side navigation menu
│
└── pages/
    └── users/
        └── index.php             # Employee Directory view & JS Modal logic
ArchitectureClient Request (Browser / Fetch API)
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
Installation & Setup1. Clone the repositorygit clone https://github.com/yourusername/uiu-mart.git
2. Move to local serverMove the uiu_mart folder into your local server's web directory (e.g., htdocs for XAMPP or www for WAMP).3. Create MySQL databaseOpen phpMyAdmin (or your SQL CLI) and run:CREATE DATABASE uiu_mart;
4. Import the SchemaRun the following core table setup in your new database:CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'cashier') NOT NULL DEFAULT 'cashier',
    phone VARCHAR(20) DEFAULT NULL,
    salary DECIMAL(10,2) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'active',
    last_activity DATETIME DEFAULT NULL
);
5. Create the Superuser (Admin)INSERT INTO users (first_name, last_name, email, password_hash, role) 
VALUES ('System', 'Administrator', 'admin@uiumart.com', '$2y$10$vEUYPcb66xwTKWES5R6y1O88.Gtgp0/SqdEAGISv0cpq.RhAyx8O6', 'admin');
-- Default Password for this hash is the one previously set in your environment.
6. Configure environmentUpdate app/config/db.php with your database credentials:define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your DB username
define('DB_PASS', '');     // Your DB password
define('DB_NAME', 'uiu_mart');
7. Run the ApplicationOpen your browser and navigate to: http://localhost/uiu_mart/Core Application Routes & ActionsInstead of a REST API, UIU Mart uses an internal action-routing mechanism via its controllers.Authentication Controller (AuthController.php)MethodEndpoint / ActionDescriptionAuth RequiredPOST?action=loginAuthenticates user, checks concurrencyNoGET?action=logoutDestroys session, redirects to loginYesUser Controller (UserController.php)MethodEndpoint / ActionDescriptionRole RequiredGETindex() (Internal)Fetches all employees for the viewAdminPOST?action=createCreates new employee, hashes passwordAdminPOST?action=updateUpdates profile data (and password if set)AdminPOST?action=deleteSoft/Hard deletes user from databaseAdminSecurity & Concurrency ControlsHardware Concurrency GuardTo simulate physical register constraints, the system checks the last_activity column.SELECT COUNT(*) FROM users WHERE role = 'cashier' AND last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
If this evaluates to >= 4, new Cashier logins are temporarily blocked.Anti-Vulnerability ImplementationsSQL Injection: Neutralized via 100% strict usage of PDO Prepared Statements.Session Fixation: Neutralized via session_regenerate_id(true) upon successful authentication.XSS (Cross-Site Scripting): Neutralized via htmlspecialchars() wrapping on all data rendered to the DOM.Rainbow Table Attacks: Neutralized using BCrypt hashing algorithm for all passwords.Future Updates🔴 High Priority[ ] Data Export — Allow Admin to export the Employee Roster to CSV/Excel formats.[ ] Pagination — Implement server-side pagination for the Active Roster table to handle scaling.🟡 Medium Priority[ ] Activity Logs — Create an audit trail tracking which admin created/deleted specific users.[ ] Password Recovery — Implement a secure token-based password reset via email.🟢 Nice To Have[ ] Dark Mode — Toggleable CSS variables for a dark dashboard theme.[ ] Profile Avatars — Support for uploading user profile images.LicenseMIT License
