<?php
$host = '127.0.0.1';
$dbname = 'bss_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully\n";
    
    // Clear existing data
    $pdo->exec("DELETE FROM members");
    $pdo->exec("DELETE FROM users");
    $pdo->exec("DELETE FROM loans");
    $pdo->exec("DELETE FROM transactions");
    $pdo->exec("DELETE FROM projects");
    
    // Insert users
    $pdo->exec("INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES 
        ('Admin User', 'admin@bss.com', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW()),
        ('Manager User', 'manager@bss.com', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 1, NOW(), NOW()),
        ('Member User', 'member@bss.com', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 1, NOW(), NOW())");
    
    // Insert members
    $pdo->exec("INSERT INTO members (member_id, full_name, email, location, occupation, contact, savings, loan, balance, savings_balance, role, password, created_at, updated_at) VALUES 
        ('BSS001', 'John Doe', 'john@bss.com', 'Kampala', 'Teacher', '+256700123456', 500000.00, 0.00, 500000.00, 500000.00, 'client', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS002', 'Jane Smith', 'jane@bss.com', 'Entebbe', 'Nurse', '+256700234567', 750000.00, 200000.00, 750000.00, 750000.00, 'shareholder', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS003', 'Robert Johnson', 'robert@bss.com', 'Jinja', 'Engineer', '+256700345678', 1200000.00, 500000.00, 1200000.00, 1200000.00, 'cashier', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS004', 'Mary Wilson', 'mary@bss.com', 'Mbarara', 'Doctor', '+256700456789', 2000000.00, 0.00, 2000000.00, 2000000.00, 'td', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
        ('BSS005', 'David Brown', 'david@bss.com', 'Gulu', 'Business Owner', '+256700567890', 3000000.00, 1000000.00, 3000000.00, 3000000.00, 'ceo', '$2y$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW())");
    
    // Insert loans
    $pdo->exec("INSERT INTO loans (loan_id, member_id, amount, purpose, repayment_months, interest, monthly_payment, status, created_at, updated_at) VALUES 
        ('LOAN001', 'BSS002', 200000.00, 'Business expansion', 12, 10000.00, 17500.00, 'approved', NOW(), NOW()),
        ('LOAN002', 'BSS003', 500000.00, 'Home improvement', 24, 25000.00, 21875.00, 'approved', NOW(), NOW()),
        ('LOAN003', 'BSS005', 1000000.00, 'Equipment purchase', 24, 50000.00, 43750.00, 'pending', NOW(), NOW())");
    
    // Insert transactions
    $pdo->exec("INSERT INTO transactions (transaction_id, member_id, amount, type, created_at, updated_at) VALUES 
        ('TXN001', 'BSS001', 100000.00, 'deposit', NOW(), NOW()),
        ('TXN002', 'BSS002', 150000.00, 'deposit', NOW(), NOW()),
        ('TXN003', 'BSS003', 200000.00, 'deposit', NOW(), NOW())");
    
    // Insert projects
    $pdo->exec("INSERT INTO projects (project_id, name, budget, timeline, description, progress, roi, risk_score, created_at, updated_at) VALUES 
        ('PRJ001', 'Community Water Project', 5000000.00, '2024-12-31', 'Installing clean water systems', 65, 12.5, 30, NOW(), NOW()),
        ('PRJ002', 'Education Support Program', 3000000.00, '2024-06-30', 'Providing scholarships', 100, 8.0, 20, NOW(), NOW()),
        ('PRJ003', 'Healthcare Initiative', 8000000.00, '2025-03-31', 'Mobile health clinics', 15, 15.0, 40, NOW(), NOW())");
    
    echo "✅ Data inserted successfully!\n";
    
    // Verify data
    $stmt = $pdo->query("SELECT COUNT(*) FROM members");
    echo "Members: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "Users: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM loans");
    echo "Loans: " . $stmt->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>