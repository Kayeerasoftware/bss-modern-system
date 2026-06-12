@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.members.index') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-arrow-left text-blue-600"></i>
            </a>
            <div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Membership Bio Data Form</h2>
                <p class="text-gray-600 text-sm">Complete member bio data information</p>
            </div>
        </div>

        <form action="{{ route('admin.bio-data.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl p-8 space-y-6">
            @csrf
            
            <!-- Member Search -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-green-600"></i> Search Member
                </h3>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search Member</label>
                    <input type="text" id="member_search" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Type member name or ID..." autocomplete="off">
                    <input type="hidden" name="member_id" id="selected_member_id">
                    <div id="member_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto" style="display:none;"></div>
                </div>
                <div id="member_info" class="mt-4 p-4 bg-blue-50 rounded-lg" style="display:none;">
                    <p class="text-sm text-blue-800"><strong>Selected Member:</strong> <span id="member_display"></span></p>
                </div>
            </div>
            
            <!-- Personal Details -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i> Personal Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="full_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('full_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">NIN Number *</label>
                        <input type="text" name="nin_no" required maxlength="14" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('nin_no') }}">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">About Yourself</label>
                    <textarea name="about_yourself" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical" placeholder="Tell us about yourself, your interests, background, or any other relevant information...">{{ old('about_yourself') }}</textarea>
                </div>
            </div>

            <!-- Address -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-purple-600"></i> Address
                </h3>
                
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Present Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <input list="present_region_list" id="present_region" name="present_region" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type region">
                        <datalist id="present_region_list">
                            <option value="Central">
                            <option value="Eastern">
                            <option value="Northern">
                            <option value="Western">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <input list="present_district_list" id="present_district" name="present_district" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type district">
                        <datalist id="present_district_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">County</label>
                        <input list="present_county_list" id="present_county" name="present_county" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type county">
                        <datalist id="present_county_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sub-county</label>
                        <input list="present_subcounty_list" id="present_subcounty" name="present_subcounty" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type sub-county">
                        <datalist id="present_subcounty_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parish/Ward</label>
                        <input list="present_ward_list" id="present_ward" name="present_ward" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type parish/ward">
                        <datalist id="present_ward_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Village</label>
                        <input list="present_village_list" id="present_village" name="present_village" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type village">
                        <datalist id="present_village_list"></datalist>
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-gray-700 mb-3">Permanent Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <input list="permanent_region_list" id="permanent_region" name="permanent_region" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type region">
                        <datalist id="permanent_region_list">
                            <option value="Central">
                            <option value="Eastern">
                            <option value="Northern">
                            <option value="Western">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <input list="permanent_district_list" id="permanent_district" name="permanent_district" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type district">
                        <datalist id="permanent_district_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">County</label>
                        <input list="permanent_county_list" id="permanent_county" name="permanent_county" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type county">
                        <datalist id="permanent_county_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sub-county</label>
                        <input list="permanent_subcounty_list" id="permanent_subcounty" name="permanent_subcounty" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type sub-county">
                        <datalist id="permanent_subcounty_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parish/Ward</label>
                        <input list="permanent_ward_list" id="permanent_ward" name="permanent_ward" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type parish/ward">
                        <datalist id="permanent_ward_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Village</label>
                        <input list="permanent_village_list" id="permanent_village" name="permanent_village" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Select or type village">
                        <datalist id="permanent_village_list"></datalist>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-phone text-green-600"></i> Contacts
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Telephone</label>
                        <input type="tel" name="telephone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" value="{{ old('telephone') }}" placeholder="+256...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" value="{{ old('email') }}">
                    </div>
                </div>
            </div>

            <!-- Age and Birth -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-birthday-cake text-pink-600"></i> Age and Birth
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth *</label>
                        <input type="date" name="dob" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" value="{{ old('dob') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nationality</label>
                        <select id="nationality_select" name="nationality" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="Ugandan">Ugandan</option>
                            <option value="Kenyan">Kenyan</option>
                            <option value="Tanzanian">Tanzanian</option>
                            <option value="Rwandan">Rwandan</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div id="other_nationality_div" style="display:none;">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Specify Nationality *</label>
                        <input type="text" id="other_nationality" name="other_nationality" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter country">
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-gray-700 mb-3">Place of Birth</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <input list="birth_region_list" id="birth_region" name="birth_region" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type region">
                        <datalist id="birth_region_list">
                            <option value="Central">
                            <option value="Eastern">
                            <option value="Northern">
                            <option value="Western">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <input list="birth_district_list" id="birth_district" name="birth_district" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type district">
                        <datalist id="birth_district_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">County</label>
                        <input list="birth_county_list" id="birth_county" name="birth_county" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type county">
                        <datalist id="birth_county_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sub-county</label>
                        <input list="birth_subcounty_list" id="birth_subcounty" name="birth_subcounty" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type sub-county">
                        <datalist id="birth_subcounty_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parish/Ward</label>
                        <input list="birth_ward_list" id="birth_ward" name="birth_ward" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type parish/ward">
                        <datalist id="birth_ward_list"></datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Village</label>
                        <input list="birth_village_list" id="birth_village" name="birth_village" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Select or type village">
                        <datalist id="birth_village_list"></datalist>
                    </div>
                </div>
            </div>

            <!-- Other Particulars -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-orange-600"></i> Other Particulars
                </h3>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Marital Status *</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="marital_status" value="Single" required class="w-4 h-4 text-blue-600">
                            <span>Single</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="marital_status" value="Married" class="w-4 h-4 text-blue-600">
                            <span>Married</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="marital_status" value="Divorced" class="w-4 h-4 text-blue-600">
                            <span>Divorced</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="marital_status" value="Widowed" class="w-4 h-4 text-blue-600">
                            <span>Widowed</span>
                        </label>
                    </div>
                </div>
                <div id="spouse_details" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display:none;">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Spouse Name</label>
                        <input type="text" name="spouse_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" value="{{ old('spouse_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Spouse NIN Number</label>
                        <input type="text" name="spouse_nin" maxlength="14" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" value="{{ old('spouse_nin') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Next of Kin</label>
                        <input type="text" name="next_of_kin" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" value="{{ old('next_of_kin') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Next of Kin NIN Number</label>
                        <input type="text" name="next_of_kin_nin" maxlength="14" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" value="{{ old('next_of_kin_nin') }}">
                    </div>
                </div>
            </div>

            <!-- Parental Status -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-indigo-600"></i> Parental Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Father's Name</label>
                        <input type="text" name="father_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('father_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mother's Name</label>
                        <input type="text" name="mother_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('mother_name') }}">
                    </div>
                </div>
            </div>

            <!-- Children -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-child text-teal-600"></i> Children
                </h3>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Do you have children? *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_children" value="yes" required class="w-4 h-4 text-teal-600">
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="has_children" value="no" class="w-4 h-4 text-teal-600">
                            <span>No</span>
                        </label>
                    </div>
                </div>
                <div id="children_details" style="display:none;">
                    <div id="children_list"></div>
                    <button type="button" id="add_child_btn" class="mt-3 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all">
                        <i class="fas fa-plus mr-2"></i>Add Child
                    </button>
                </div>
            </div>

            <!-- Occupation -->
            <div class="border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase text-yellow-600"></i> Occupation
                </h3>
                <textarea name="occupation" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500" placeholder="Describe your occupation">{{ old('occupation') }}</textarea>
            </div>

            <!-- Declaration -->
            <div class="bg-blue-50 p-6 rounded-xl">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-pen-fancy text-red-600"></i> Declaration
                </h3>
                <p class="text-gray-700 mb-4 font-medium">I declare that all particulars given above are true to the best of my knowledge.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Signature (Type Full Name) *</label>
                        <input type="text" name="signature" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white" value="{{ old('signature') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                        <input type="date" name="declaration_date" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white" value="{{ old('declaration_date', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-2xl transition-all font-bold text-lg">
                    <i class="fas fa-save mr-2"></i>Submit Bio Data
                </button>
                <a href="{{ route('admin.members.index') }}" class="px-6 py-4 bg-gray-200 text-gray-700 rounded-xl hover:shadow-lg transition-all font-bold">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Cache for location data
const locationCache = new Map();
const debounceTimers = new Map();

// Debounce function
function debounce(func, delay, key) {
    clearTimeout(debounceTimers.get(key));
    debounceTimers.set(key, setTimeout(func, delay));
}

// Member search functionality
let searchTimeout;
let allMembers = [];
let membersLoaded = false;

window.addEventListener('DOMContentLoaded', loadAllMembers);

async function loadAllMembers() {
    try {
        const response = await fetch('{{ route("admin.members.search") }}?q=', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        if (response.ok) {
            allMembers = await response.json();
            membersLoaded = true;
            console.log('Loaded members:', allMembers.length);
        }
    } catch (error) {
        console.error('Error loading members:', error);
    }
}

document.getElementById('member_search').addEventListener('focus', function() {
    if (!membersLoaded) {
        document.getElementById('member_dropdown').innerHTML = '<div class="p-3 text-gray-500">Loading members...</div>';
        document.getElementById('member_dropdown').style.display = 'block';
        loadAllMembers().then(() => {
            const query = this.value.trim();
            if (query.length === 0) {
                showMemberDropdown(allMembers);
            } else {
                filterAndShowMembers(query);
            }
        });
    } else {
        const query = this.value.trim();
        if (query.length === 0) {
            showMemberDropdown(allMembers);
        } else {
            filterAndShowMembers(query);
        }
    }
});

document.getElementById('member_search').addEventListener('input', function() {
    const query = this.value.trim();
    clearTimeout(searchTimeout);
    
    if (!membersLoaded) return;
    
    if (query.length === 0) {
        showMemberDropdown(allMembers);
        return;
    }
    
    searchTimeout = setTimeout(() => filterAndShowMembers(query), 200);
});

document.getElementById('member_search').addEventListener('blur', function() {
    setTimeout(() => {
        document.getElementById('member_dropdown').style.display = 'none';
    }, 200);
});

function filterAndShowMembers(query) {
    const filtered = allMembers.filter(member => {
        const searchStr = query.toLowerCase();
        return (member.full_name && member.full_name.toLowerCase().includes(searchStr)) ||
               (member.member_id && member.member_id.toLowerCase().includes(searchStr)) ||
               (member.email && member.email.toLowerCase().includes(searchStr)) ||
               (member.contact && member.contact.toLowerCase().includes(searchStr)) ||
               (member.location && member.location.toLowerCase().includes(searchStr)) ||
               (member.occupation && member.occupation.toLowerCase().includes(searchStr));
    });
    showMemberDropdown(filtered);
}

function showMemberDropdown(members) {
    const dropdown = document.getElementById('member_dropdown');
    
    if (members.length === 0) {
        dropdown.innerHTML = '<div class="p-3 text-gray-500">No members found</div>';
        dropdown.style.display = 'block';
        return;
    }
    
    dropdown.innerHTML = members.map(member => {
        const details = [];
        if (member.member_id) details.push(`ID: ${member.member_id}`);
        if (member.email) details.push(member.email);
        if (member.contact) details.push(member.contact);
        if (member.location) details.push(member.location);
        if (member.occupation) details.push(member.occupation);
        if (member.status) details.push(`Status: ${member.status}`);
        
        return `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b" onclick="selectMember(${member.id}, '${member.full_name}', '${member.member_id}')">
            <div class="font-semibold text-gray-800">${member.full_name}</div>
            <div class="text-sm text-gray-600">${details.join(' | ')}</div>
        </div>`;
    }).join('');
    
    dropdown.style.display = 'block';
}

function selectMember(id, name, memberId) {
    document.getElementById('member_search').value = `${name} (${memberId})`;
    document.getElementById('selected_member_id').value = id;
    document.getElementById('member_dropdown').style.display = 'none';
    document.getElementById('member_display').textContent = `${name} (ID: ${memberId})`;
    document.getElementById('member_info').style.display = 'block';
    
    // Load full member data
    loadMemberData(id);
}

async function loadMemberData(id) {
    try {
        const response = await fetch(`{{ url('admin/members') }}/${id}/details`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const member = await response.json();
            populateForm(member);
        }
    } catch (error) {
        console.error('Error loading member data:', error);
    }
}

function populateForm(member) {
    console.log('Populating form with:', member);
    
    // Personal Details
    if (member.full_name) document.querySelector('input[name="full_name"]').value = member.full_name;
    if (member.nin_no) document.querySelector('input[name="nin_no"]').value = member.nin_no;
    if (member.about_yourself) document.querySelector('textarea[name="about_yourself"]').value = member.about_yourself;
    
    // Contacts
    if (member.contact) document.querySelector('input[name="telephone"]').value = member.contact;
    if (member.email) document.querySelector('input[name="email"]').value = member.email;
    
    // Age and Birth
    if (member.dob) document.querySelector('input[name="dob"]').value = member.dob;
    if (member.nationality) document.querySelector('select[name="nationality"]').value = member.nationality;
    
    // Present Address
    if (member.present_address) {
        if (member.present_address.region) document.getElementById('present_region').value = member.present_address.region;
        if (member.present_address.district) document.getElementById('present_district').value = member.present_address.district;
        if (member.present_address.county) document.getElementById('present_county').value = member.present_address.county;
        if (member.present_address.subcounty) document.getElementById('present_subcounty').value = member.present_address.subcounty;
        if (member.present_address.ward) document.getElementById('present_ward').value = member.present_address.ward;
        if (member.present_address.village) document.getElementById('present_village').value = member.present_address.village;
    }
    
    // Permanent Address
    if (member.permanent_address) {
        if (member.permanent_address.region) document.getElementById('permanent_region').value = member.permanent_address.region;
        if (member.permanent_address.district) document.getElementById('permanent_district').value = member.permanent_address.district;
        if (member.permanent_address.county) document.getElementById('permanent_county').value = member.permanent_address.county;
        if (member.permanent_address.subcounty) document.getElementById('permanent_subcounty').value = member.permanent_address.subcounty;
        if (member.permanent_address.ward) document.getElementById('permanent_ward').value = member.permanent_address.ward;
        if (member.permanent_address.village) document.getElementById('permanent_village').value = member.permanent_address.village;
    }
    
    // Birth Place
    if (member.birth_place) {
        if (member.birth_place.region) document.getElementById('birth_region').value = member.birth_place.region;
        if (member.birth_place.district) document.getElementById('birth_district').value = member.birth_place.district;
        if (member.birth_place.county) document.getElementById('birth_county').value = member.birth_place.county;
        if (member.birth_place.subcounty) document.getElementById('birth_subcounty').value = member.birth_place.subcounty;
        if (member.birth_place.ward) document.getElementById('birth_ward').value = member.birth_place.ward;
        if (member.birth_place.village) document.getElementById('birth_village').value = member.birth_place.village;
    }
    
    // Other fields
    if (member.marital_status) {
        const maritalRadio = document.querySelector(`input[name="marital_status"][value="${member.marital_status}"]`);
        if (maritalRadio) maritalRadio.checked = true;
    }
    if (member.spouse_name) document.querySelector('input[name="spouse_name"]').value = member.spouse_name;
    if (member.spouse_nin) document.querySelector('input[name="spouse_nin"]').value = member.spouse_nin;
    if (member.next_of_kin) document.querySelector('input[name="next_of_kin"]').value = member.next_of_kin;
    if (member.next_of_kin_nin) document.querySelector('input[name="next_of_kin_nin"]').value = member.next_of_kin_nin;
    if (member.father_name) document.querySelector('input[name="father_name"]').value = member.father_name;
    if (member.mother_name) document.querySelector('input[name="mother_name"]').value = member.mother_name;
    if (member.occupation) document.querySelector('textarea[name="occupation"]').value = member.occupation;
}

function clearForm() {
    document.getElementById('selected_member_id').value = '';
    document.getElementById('member_info').style.display = 'none';
}

// Element cache for performance
const elementCache = new Map();

function getElement(id) {
    if (!elementCache.has(id)) {
        elementCache.set(id, document.getElementById(id));
    }
    return elementCache.get(id);
}

// Batch location requests
const pendingRequests = new Map();

async function fetchLocationData(url, cacheKey) {
    if (locationCache.has(cacheKey)) {
        return locationCache.get(cacheKey);
    }
    
    if (pendingRequests.has(cacheKey)) {
        return pendingRequests.get(cacheKey);
    }
    
    const request = fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          locationCache.set(cacheKey, data);
          pendingRequests.delete(cacheKey);
          return data;
      })
      .catch(error => {
          console.error('Error fetching location data:', error);
          pendingRequests.delete(cacheKey);
          return [];
      });
    
    pendingRequests.set(cacheKey, request);
    return request;
}

function updateDatalist(listElement, options) {
    const fragment = document.createDocumentFragment();
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        fragment.appendChild(optionElement);
    });
    listElement.innerHTML = '';
    listElement.appendChild(fragment);
}

function clearSubsequentFields(prefix, level) {
    const fields = ['district', 'county', 'subcounty', 'ward', 'village'];
    const lists = ['district_list', 'county_list', 'subcounty_list', 'ward_list', 'village_list'];
    
    // Use requestAnimationFrame for better performance
    requestAnimationFrame(() => {
        for (let i = level; i < fields.length; i++) {
            const input = getElement(`${prefix}_${fields[i]}`);
            const list = getElement(`${prefix}_${lists[i]}`);
            if (input) input.value = '';
            if (list) list.innerHTML = '';
        }
    });
}

function setupCascadingDropdowns(prefix) {
    // Cache all elements at once
    const elements = {
        regionInput: getElement(`${prefix}_region`),
        districtInput: getElement(`${prefix}_district`),
        countyInput: getElement(`${prefix}_county`),
        subcountyInput: getElement(`${prefix}_subcounty`),
        wardInput: getElement(`${prefix}_ward`),
        districtList: getElement(`${prefix}_district_list`),
        countyList: getElement(`${prefix}_county_list`),
        subcountyList: getElement(`${prefix}_subcounty_list`),
        wardList: getElement(`${prefix}_ward_list`),
        villageList: getElement(`${prefix}_village_list`)
    };
    
    const { regionInput, districtInput, countyInput, subcountyInput, wardInput, 
            districtList, countyList, subcountyList, wardList, villageList } = elements;

    regionInput.addEventListener('input', function() {
        const region = this.value.trim();
        clearSubsequentFields(prefix, 0);
        
        if (region) {
            debounce(async () => {
                try {
                    const districts = await fetchLocationData(`/api/locations/districts/${encodeURIComponent(region)}`, `districts_${region}`);
                    updateDatalist(districtList, districts);
                } catch (error) {
                    console.error('Failed to load districts:', error);
                }
            }, 200, `${prefix}_region`);
        }
    });

    districtInput.addEventListener('input', function() {
        const district = this.value.trim();
        clearSubsequentFields(prefix, 1);
        
        if (district) {
            debounce(async () => {
                const counties = await fetchLocationData(`/api/locations/counties/${district}`, `counties_${district}`);
                updateDatalist(countyList, counties);
            }, 300, `${prefix}_district`);
        }
    });

    countyInput.addEventListener('input', function() {
        const district = districtInput.value.trim();
        const county = this.value.trim();
        clearSubsequentFields(prefix, 2);
        
        if (district && county) {
            debounce(async () => {
                const subcounties = await fetchLocationData(`/api/locations/subcounties?district=${district}&county=${county}`, `subcounties_${district}_${county}`);
                updateDatalist(subcountyList, subcounties);
            }, 300, `${prefix}_county`);
        }
    });

    subcountyInput.addEventListener('input', function() {
        const subcounty = this.value.trim();
        clearSubsequentFields(prefix, 3);
        
        if (subcounty) {
            debounce(async () => {
                const parishes = await fetchLocationData(`/api/locations/parishes?subcounty=${subcounty}`, `parishes_${subcounty}`);
                updateDatalist(wardList, parishes);
            }, 300, `${prefix}_subcounty`);
        }
    });

    wardInput.addEventListener('input', function() {
        const ward = this.value.trim();
        clearSubsequentFields(prefix, 4);
        
        if (ward) {
            debounce(async () => {
                const villages = await fetchLocationData(`/api/locations/villages?parish=${ward}`, `villages_${ward}`);
                updateDatalist(villageList, villages);
            }, 300, `${prefix}_ward`);
        }
    });
}

setupCascadingDropdowns('present');
setupCascadingDropdowns('permanent');
setupCascadingDropdowns('birth');

// Nationality other field toggle with cached elements
const nationalitySelect = getElement('nationality_select');
const otherDiv = getElement('other_nationality_div');
const otherInput = getElement('other_nationality');

if (nationalitySelect) {
    nationalitySelect.addEventListener('change', function() {
        if (this.value === 'Other') {
            otherDiv.style.display = 'block';
            otherInput.required = true;
        } else {
            otherDiv.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    });
}

// Marital status spouse details toggle
document.querySelectorAll('input[name="marital_status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const spouseDetails = document.getElementById('spouse_details');
        if (this.value === 'Married') {
            spouseDetails.style.display = 'grid';
        } else {
            spouseDetails.style.display = 'none';
        }
    });
});

// Children section
let childCount = 0;
document.querySelectorAll('input[name="has_children"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const childrenDetails = document.getElementById('children_details');
        if (this.value === 'yes') {
            childrenDetails.style.display = 'block';
            if (childCount === 0) addChild();
        } else {
            childrenDetails.style.display = 'none';
            document.getElementById('children_list').innerHTML = '';
            childCount = 0;
        }
    });
});

document.getElementById('add_child_btn').addEventListener('click', addChild);

function addChild() {
    childCount++;
    const childDiv = document.createElement('div');
    childDiv.className = 'border border-gray-300 rounded-lg p-4 mb-3';
    childDiv.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Child ${childCount}</h4>
            <button type="button" onclick="removeChild(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="children[${childCount}][name]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="children[${childCount}][dob]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="children[${childCount}][gender]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>
    `;
    document.getElementById('children_list').appendChild(childDiv);
}

function removeChild(btn) {
    btn.closest('.border').remove();
}
</script>
@endsection
