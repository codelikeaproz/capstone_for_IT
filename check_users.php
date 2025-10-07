<?php
// Simple script to check users in the database
echo "Checking database connection...\n";

try {
    // Database configuration from .env
    $host = 'localhost';
    $port = '5432';
    $database = 'capstone_project';
    $username = 'postgres';
    $password = 'Admin';
    
    echo "Connecting to database: $database on $host:$port\n";
    
    // Create PDO connection
    $dsn = "pgsql:host=$host;port=$port;dbname=$database;";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "Database connection successful!\n\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_name = 'users'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "Error: Users table does not exist\n";
        exit(1);
    }
    
    // Get all users
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    echo "Found " . count($users) . " users:\n\n";
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['first_name']} {$user['last_name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Role: {$user['role']}\n";
        echo "Verified: " . ($user['email_verified_at'] ? 'Yes (' . $user['email_verified_at'] . ')' : 'No') . "\n";
        echo "2FA Code: " . ($user['two_factor_code'] ?? 'None') . "\n";
        echo "Failed Attempts: {$user['failed_login_attempts']}\n";
        echo "Locked Until: " . ($user['locked_until'] ?? 'Not locked') . "\n";
        echo "------------------------\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}