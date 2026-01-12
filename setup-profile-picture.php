<?php

echo "=== Profile Picture Feature Setup ===\n\n";

// Run migration
echo "Running migration...\n";
exec('php artisan migrate --path=database/migrations/2024_01_20_000003_add_profile_picture_to_members.php', $output, $return);

if ($return === 0) {
    echo "✓ Migration completed successfully\n\n";
} else {
    echo "✗ Migration failed\n";
    print_r($output);
    exit(1);
}

// Create storage link
echo "Creating storage link...\n";
exec('php artisan storage:link', $output2, $return2);

if ($return2 === 0) {
    echo "✓ Storage link created successfully\n\n";
} else {
    echo "Note: Storage link may already exist\n\n";
}

// Create profile_pictures directory
$dir = __DIR__ . '/storage/app/public/profile_pictures';
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
    echo "✓ Profile pictures directory created\n\n";
} else {
    echo "✓ Profile pictures directory already exists\n\n";
}

echo "=== Setup Complete! ===\n\n";
echo "Profile Picture Feature:\n";
echo "- Click on profile picture to upload\n";
echo "- Supports JPG, PNG, GIF (Max 2MB)\n";
echo "- Pictures stored in database\n";
echo "- Automatic display on dashboard\n\n";

echo "API Endpoints:\n";
echo "- POST /api/profile/upload-picture\n";
echo "- GET /api/profile/picture/{memberId}\n\n";

echo "Test the feature at: http://localhost:8000/shareholder-dashboard\n";
