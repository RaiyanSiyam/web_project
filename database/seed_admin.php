<?php
// database/seed_admin.php

// Show errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/config/db.php';

echo "<h2>UIU Mart Database Seeder</h2>";

try {
    $db = Database::getInstance();

    // 1. Ensure the 'Admin' role exists
    $roleQuery = "SELECT id FROM roles WHERE role_name = 'Admin'";
    $stmt = $db->query($roleQuery);
    $role = $stmt->fetch();

    if (!$role) {
        $insertRole = "INSERT INTO roles (role_name) VALUES ('Admin')";
        $db->exec($insertRole);
        $roleId = $db->lastInsertId();
        echo "<p>✅ Created 'Admin' role (ID: $roleId)</p>";
    } else {
        $roleId = $role['id'];
        echo "<p>ℹ️ 'Admin' role already exists (ID: $roleId)</p>";
    }

    // 2. Check if the default admin user already exists
    $adminEmail = 'admin@uiumart.com';
    $userQuery = "SELECT id FROM users WHERE email = :email";
    $stmt = $db->prepare($userQuery);
    $stmt->execute(['email' => $adminEmail]);
    
    if ($stmt->fetch()) {
        echo "<p>⚠️ Admin user (<strong>$adminEmail</strong>) already exists. Seeding skipped to prevent duplicates.</p>";
    } else {
        // 3. Create the admin user
        $passwordPlain = 'Admin@123';
        $passwordHash = password_hash($passwordPlain, PASSWORD_BCRYPT);
        
        $insertUser = "INSERT INTO users (role_id, first_name, last_name, email, password_hash, status) 
                       VALUES (:role_id, :first_name, :last_name, :email, :password_hash, 'active')";
        
        $stmt = $db->prepare($insertUser);
        $stmt->execute([
            'role_id' => $roleId,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => $adminEmail,
            'password_hash' => $passwordHash
        ]);
        
        echo "<div style='background:#dcfce7; padding:15px; border-radius:8px; border:1px solid #10b981; max-width: 400px;'>";
        echo "<h3 style='margin-top:0; color:#047857;'>✅ Success! Admin Seeded.</h3>";
        echo "<p><strong>Email:</strong> $adminEmail</p>";
        echo "<p><strong>Password:</strong> $passwordPlain</p>";
        echo "</div>";
    }

    echo "<p style='color:red; font-weight:bold;'>SECURITY WARNING: Please delete this file (database/seed_admin.php) after running it!</p>";
    echo "<a href='../login.php' style='display:inline-block; margin-top:10px; padding:10px 20px; background:#000; color:#fff; text-decoration:none; border-radius:6px;'>Go to Login</a>";

} catch (PDOException $e) {
    echo "<p style='color:red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
}
?>