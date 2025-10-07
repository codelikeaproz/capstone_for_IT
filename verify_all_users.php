<?php
// Script to verify all users in the database
echo "Verifying all users...\n";

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
    
    // Update all users to set email_verified_at to current timestamp
    $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE email_verified_at IS NULL");
    $stmt->execute();
    $rowCount = $stmt->rowCount();
    
    echo "Verified $rowCount users.\n\n";
    
    // Show updated users
    $stmt = $pdo->query("SELECT id, email, email_verified_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    echo "Updated verification status:\n";
    foreach ($users as $user) {
        echo "Email: {$user['email']} - Verified: " . ($user['email_verified_at'] ? 'Yes' : 'No') . "\n";
    }
    
    echo "\nAll users have been verified! You can now log in with any test account.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}