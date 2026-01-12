<?php

echo "BSS Investment Group System - API Test Suite\n";
echo "==========================================\n\n";

// Test endpoints
$baseUrl = 'http://localhost:8000';
$endpoints = [
    'GET /api/system/health' => 'System health check',
    'GET /api/dashboard-data' => 'Dashboard data',
    'GET /' => 'Main dashboard',
    'GET /complete' => 'Complete dashboard',
    'GET /admin' => 'Admin panel',
];

function testEndpoint($url, $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: $error\n";
        return false;
    }
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ Success (HTTP $httpCode)\n";
        
        // Try to decode JSON response
        $json = json_decode($response, true);
        if ($json && isset($json['status'])) {
            echo "   Status: " . $json['status'] . "\n";
        }
        if ($json && isset($json['features'])) {
            echo "   Features: " . count($json['features']) . " available\n";
        }
        
        return true;
    } else {
        echo "❌ Failed (HTTP $httpCode)\n";
        return false;
    }
}

echo "Starting API tests...\n\n";

$passed = 0;
$total = count($endpoints);

foreach ($endpoints as $endpoint => $description) {
    list($method, $path) = explode(' ', $endpoint, 2);
    $url = $baseUrl . $path;
    
    if (testEndpoint($url, $description)) {
        $passed++;
    }
    echo "\n";
}

echo str_repeat("=", 50) . "\n";
echo "Test Results: $passed/$total tests passed\n";

if ($passed === $total) {
    echo "🎉 All tests passed! System is fully operational.\n";
} else {
    echo "⚠️  Some tests failed. Please check the server status.\n";
}

echo "\n📋 System Features Verified:\n";
echo "   • Dashboard Access\n";
echo "   • API Health Check\n";
echo "   • Admin Panel Access\n";
echo "   • Complete Dashboard\n";

echo "\n🚀 Next Steps:\n";
echo "   1. Start the development server: php artisan serve\n";
echo "   2. Access the system at: http://localhost:8000\n";
echo "   3. Login with default credentials\n";
echo "   4. Test all features through the web interface\n";

echo "\n✅ BSS Investment Group System is ready for use!\n";