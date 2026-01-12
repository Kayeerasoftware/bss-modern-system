<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bss_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ MySQL connection successful\n\n";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    // Check members
    if (in_array('members', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM members");
        $count = $stmt->fetchColumn();
        echo "\n👥 Members: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT member_id, full_name FROM members LIMIT 3");
            while ($row = $stmt->fetch()) {
                echo "  - {$row['member_id']}: {$row['full_name']}\n";
            }
        }
    } else {
        echo "\n❌ Members table missing\n";
    }
    
    // Check users
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "\n👤 Users: $count\n";
    } else {
        echo "\n❌ Users table missing\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure MySQL is running and database 'bss_system' exists\n";
}
?>