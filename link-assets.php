<?php
/**
 * Public Asset Linker for InfinityFree
 * This creates symbolic links for public assets
 */

// Create symlinks for common asset directories
$links = [
    __DIR__ . '/storage/app/public' => __DIR__ . '/public/storage',
];

foreach ($links as $target => $link) {
    if (file_exists($link)) {
        echo "✓ Link already exists: $link<br>";
        continue;
    }
    
    if (@symlink($target, $link)) {
        echo "✓ Created symlink: $link → $target<br>";
    } else {
        // If symlink fails, try to copy
        if (!file_exists($link)) {
            mkdir($link, 0755, true);
        }
        echo "⚠ Symlink not supported. Created directory: $link<br>";
        echo "  You'll need to manually copy files from storage/app/public to public/storage<br>";
    }
}

echo "<br><strong>Asset linking complete!</strong><br>";
echo "<br><a href='/'>Go to Home</a>";
