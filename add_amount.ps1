$file = 'c:\Users\ASUS\Desktop\bss system\resources\views\admin-dashboard.blade.php'
$content = Get-Content $file -Raw

# Add x-model to amount field
$content = $content.Replace(
    '<input type="number" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" min="1000" required>',
    '<input type="number" x-model="fundraisingForm.amount" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" min="1000" placeholder="Enter amount" required>'
)

Set-Content $file $content
Write-Host "Amount field updated"
