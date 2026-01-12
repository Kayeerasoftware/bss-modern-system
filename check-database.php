<?php
// Simple database check script
$dbPath = __DIR__ . '/database/database.sqlite';

echo "BSS System Database Check\n";
echo "========================\n\n";

// Check if database file exists
if (!file_exists($dbPath)) {
    echo "❌ Database file does not exist at: $dbPath\n";
    echo "Please run: php artisan migrate:fresh --seed\n";
    exit(1);
}

echo "✅ Database file exists\n";
echo "File size: " . filesize($dbPath) . " bytes\n\n";

// Check if database has content
if (filesize($dbPath) < 1000) {
    echo "⚠️  Database file is very small (likely empty)\n";
    echo "Please run: php artisan migrate:fresh --seed\n";
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check tables
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables found: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    echo "\n";
    
    // Check members
    if (in_array('members', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM members");
        $memberCount = $stmt->fetchColumn();
        echo "👥 Members in database: $memberCount\n";
        
        if ($memberCount > 0) {
            $stmt = $pdo->query("SELECT member_id, full_name, email FROM members LIMIT 3");
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Sample members:\n";
            foreach ($members as $member) {
                echo "  - {$member['member_id']}: {$member['full_name']} ({$member['email']})\n";
            }
        }
    } else {
        echo "❌ Members table not found\n";
    }
    
    echo "\n";
    
    // Check users
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "👤 Users in database: $userCount\n";
        
        if ($userCount > 0) {
            $stmt = $pdo->query("SELECT name, email, role FROM users LIMIT 3");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Sample users:\n";
            foreach ($users as $user) {
                echo "  - {$user['name']} ({$user['email']}) - {$user['role']}\n";
            }
        }
    } else {
        echo "❌ Users table not found\n";
    }
    
    if ($memberCount > 0 && $userCount > 0) {
        echo "\n✅ Database appears to be properly set up!\n";
        echo "You can now run: php artisan serve\n";
    } else {
        echo "\n⚠️  Database exists but has no data\n";
        echo "Please run: php artisan migrate:fresh --seed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "Please run: php artisan migrate:fresh --seed\n";
}
?>