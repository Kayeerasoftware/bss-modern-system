$file = 'c:\Users\ASUS\Desktop\bss system\resources\views\admin-dashboard.blade.php'
$content = Get-Content $file -Raw

$old = @'
                        <input type="text" x-model="fundraisingMemberSearch" @input="filterFundraisingMembers()" @focus="showFundraisingDropdown = true" @click="showFundraisingDropdown = true" @click.away="showFundraisingDropdown = false" placeholder="Search member..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" autocomplete="off" required>


                    </div>
'@

$new = @'
                        <input type="text" x-model="fundraisingMemberSearch" @input="filterFundraisingMembers()" @focus="showFundraisingDropdown = true" @click="showFundraisingDropdown = true" @click.away="showFundraisingDropdown = false" placeholder="Search member..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" autocomplete="off" required>
                        <div x-show="showFundraisingDropdown && filteredFundraisingMembers.length > 0" class="absolute z-10 w-full mt-1 bg-white border-2 border-pink-300 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="member in filteredFundraisingMembers" :key="member.member_id">
                                <div @click="selectFundraisingMember(member)" class="px-4 py-2 hover:bg-pink-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <span class="font-semibold text-gray-800" x-text="member.member_id + ' - ' + member.full_name"></span>
                                </div>
                            </template>
                        </div>
                        <div x-show="fundraisingForm.member_id" class="mt-2 p-3 bg-pink-50 rounded-lg border border-pink-200">
                            <p class="text-sm text-gray-700">💰 Current Savings: <span class="font-bold text-pink-600" x-text="formatCurrency(members.find(m => m.member_id === fundraisingForm.member_id)?.savings || 0)"></span></p>
                        </div>
                    </div>
'@

$content = $content.Replace($old, $new)
Set-Content $file $content
Write-Host "Dropdown added"
