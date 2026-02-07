$file = 'c:\Users\ASUS\Desktop\bss system\resources\views\admin-dashboard.blade.php'
$content = Get-Content $file -Raw

# Find and replace campaign name input (in fundraising modal context)
$pattern = '(<i class="fas fa-tag text-gray-400 mr-1"></i>Campaign Name \*\s+</label>\s+<input type="text" )class="w-full'
$replacement = '$1x-model="fundraisingForm.campaign_name" class="w-full'
$content = $content -replace $pattern, $replacement

# Find and replace purpose textarea
$pattern2 = '(<i class="fas fa-align-left text-gray-400 mr-1"></i>Purpose \*\s+</label>\s+<textarea )class="w-full'
$replacement2 = '$1x-model="fundraisingForm.purpose" class="w-full'
$content = $content -replace $pattern2, $replacement2

Set-Content $file $content
Write-Host "Campaign and purpose fields updated"
