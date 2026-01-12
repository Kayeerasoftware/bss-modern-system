<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bss_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Inserting test data...\n";
    
    // Insert users
    $pdo->exec("INSERT IGNORE INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES 
        ('Admin User', 'admin@bss.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW()),
        ('Manager User', 'manager@bss.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 1, NOW(), NOW()),
        ('Member User', 'member@bss.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 1, NOW(), NOW())");
    
    // Insert members
    $pdo->exec("INSERT IGNORE INTO members (member_id, full_name, email, location, occupation, contact, savings, loan, balance, savings_balance, role, password, created_at, updated_at) VALUES 
        ('BSS001', 'John Doe', 'john@bss.com', 'Kampala', 'Teacher', '+256700123456', 500000, 0, 500000, 500000, 'client', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS002', 'Jane Smith', 'jane@bss.com', 'Entebbe', 'Nurse', '+256700234567', 750000, 200000, 750000, 750000, 'shareholder', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS003', 'Robert Johnson', 'robert@bss.com', 'Jinja', 'Engineer', '+256700345678', 1200000, 500000, 1200000, 1200000, 'cashier', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW())");
    
    echo "✅ Test data inserted successfully\n";
    
    // Verify
    $stmt = $pdo->query("SELECT COUNT(*) FROM members");
    echo "Members count: " . $stmt->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>