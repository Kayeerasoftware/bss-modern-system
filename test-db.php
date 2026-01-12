<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Test database connection
    $pdo = $capsule->getConnection()->getPdo();
    echo "✅ Database connection successful\n";
    
    // Check if tables exist
    $tables = $capsule->select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "\n📋 Tables in database:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
    
    // Check members table
    $members = $capsule->select("SELECT COUNT(*) as count FROM members");
    echo "\n👥 Members count: " . $members[0]->count . "\n";
    
    if ($members[0]->count > 0) {
        $membersList = $capsule->select("SELECT member_id, full_name, email FROM members LIMIT 5");
        echo "\n📝 Sample members:\n";
        foreach ($membersList as $member) {
            echo "- {$member->member_id}: {$member->full_name} ({$member->email})\n";
        }
    }
    
    // Check users table
    $users = $capsule->select("SELECT COUNT(*) as count FROM users");
    echo "\n👤 Users count: " . $users[0]->count . "\n";
    
    if ($users[0]->count > 0) {
        $usersList = $capsule->select("SELECT name, email, role FROM users LIMIT 5");
        echo "\n📝 Sample users:\n";
        foreach ($usersList as $user) {
            echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}