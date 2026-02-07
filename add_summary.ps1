$file = 'c:\Users\ASUS\Desktop\bss system\resources\views\admin-dashboard.blade.php'
$content = Get-Content $file -Raw

$old = @'
                        <textarea x-model="fundraisingForm.purpose" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition resize-y" rows="2" placeholder="Fundraising purpose" required></textarea>

                    </div>

                </div>
'@

$new = @'
                        <textarea x-model="fundraisingForm.purpose" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition resize-y" rows="2" placeholder="Fundraising purpose" required></textarea>
                    </div>

                    <!-- Notification Checkbox -->
                    <div class="md:col-span-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="fundraisingForm.send_notification" class="w-5 h-5 text-pink-600 rounded mr-3">
                            <span class="text-sm font-medium text-gray-700">📧 Send notification to member</span>
                        </label>
                    </div>

                    <!-- Fundraising Summary -->
                    <div class="md:col-span-2 p-4 bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border-2 border-pink-200">
                        <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-receipt text-pink-600"></i>
                            Fundraising Summary
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Campaign:</span>
                                <span class="font-semibold text-gray-800" x-text="fundraisingForm.campaign_name || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Contribution Amount:</span>
                                <span class="font-semibold text-gray-800" x-text="formatCurrency(fundraisingForm.amount || 0)"></span>
                            </div>
                            <div class="flex justify-between border-t-2 border-pink-300 pt-2 mt-2">
                                <span class="font-bold text-gray-800">Total Contribution:</span>
                                <span class="font-bold text-pink-600 text-lg" x-text="formatCurrency(fundraisingForm.amount || 0)"></span>
                            </div>
                        </div>
                    </div>
                </div>
'@

$content = $content.Replace($old, $new)
Set-Content $file $content
Write-Host "Notification and summary added"
