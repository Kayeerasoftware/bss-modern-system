<?php
/**
 * Manual Storage Link Creator for InfinityFree
 * Upload this file to your root directory and run it once via browser
 * Then delete it for security
 */

// Create symbolic link manually
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

// Check if link already exists
if (file_exists($link)) {
    if (is_link($link)) {
        echo "✓ Storage link already exists!<br>";
    } else {
        echo "✗ A file/folder named 'storage' exists in public directory. Please remove it first.<br>";
        exit;
    }
} else {
    // Try to create symbolic link
    if (@symlink($target, $link)) {
        echo "✓ Storage link created successfully!<br>";
    } else {
        // If symlink fails (InfinityFree doesn't support it), copy files instead
        echo "⚠ Symbolic links not supported. Creating physical copy...<br>";
        
        // Create the directory if it doesn't exist
        if (!file_exists($link)) {
            mkdir($link, 0755, true);
        }
        
        echo "✓ Storage directory created at public/storage<br>";
        echo "<br><strong>IMPORTANT:</strong> You'll need to manually copy files from storage/app/public to public/storage when uploading files.<br>";
    }
}

echo "<br><strong>Database Configuration:</strong><br>";
echo "Make sure your .env file has correct database credentials:<br>";
echo "- DB_HOST (from InfinityFree panel)<br>";
echo "- DB_DATABASE (your database name)<br>";
echo "- DB_USERNAME (your database username)<br>";
echo "- DB_PASSWORD (your database password)<br>";

echo "<br><br><strong>⚠ DELETE THIS FILE AFTER RUNNING!</strong>";
?>
