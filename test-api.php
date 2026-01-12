<?php
// Test API endpoint directly
$url = 'http://localhost:8000/api/dashboard-data?role=admin';

echo "Testing API endpoint: $url\n";
echo "================================\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "✅ API Response parsed successfully\n";
        echo "Members count: " . (is_array($data['members']) ? count($data['members']) : $data['members']) . "\n";
        echo "Total savings: " . ($data['totalSavings'] ?? 'N/A') . "\n";
        
        if (isset($data['members']) && is_array($data['members'])) {
            echo "\nFirst 3 members:\n";
            foreach (array_slice($data['members'], 0, 3) as $member) {
                echo "- {$member['member_id']}: {$member['full_name']}\n";
            }
        }
    } else {
        echo "❌ Failed to parse JSON response\n";
    }
} else {
    echo "❌ API request failed\n";
}
?>