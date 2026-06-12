@extends('layouts.shareholder')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 p-6 print:bg-white print:p-0">
    <div class="max-w-6xl mx-auto">
        <!-- Header with Print Button -->
        <div class="flex items-center justify-between mb-6 print:hidden">
            <div class="flex items-center gap-4">
                <a href="{{ route('shareholder.dashboard') }}" class="p-3 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-arrow-left text-blue-600"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Member Bio Data</h2>
                    <p class="text-gray-600 text-sm">Complete member information</p>
                </div>
            </div>
            <button onclick="window.print()" class="px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl hover:shadow-xl transition-all font-bold">
                <i class="fas fa-print mr-2"></i>Print Bio Data
            </button>
        </div>

        <!-- Bio Data Display -->
        <div class="bg-white rounded-2xl shadow-xl print:shadow-none print:rounded-none">
            <!-- Print Header -->
            <div class="hidden print:block border-b-4 border-gradient pb-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="BSS Logo" class="w-20 h-20 object-contain" onerror="this.style.display='none'">
                        <div class="text-left">
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-transparent bg-clip-text">{{ config('app.name', 'BSS System') }}</h2>
                            <p class="text-sm text-gray-600 font-medium">Building Communities Through Savings</p>
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-600">
                        <p class="font-semibold">Date: {{ date('d M Y') }}</p>
                        <p>Member ID: {{ $member->member_id }}</p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white py-3 px-6 rounded-lg text-center">
                    <h1 class="text-2xl font-bold mb-1">MEMBERSHIP BIO DATA FORM</h1>
                    <p class="text-sm opacity-90">Official Member Information Record</p>
                </div>
            </div>

            <div class="p-8 print:p-6 space-y-8">
                <!-- Personal Details -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-user text-blue-600 print:hidden"></i> 
                        <span class="print:font-bold">PERSONAL DETAILS</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Full Name</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->full_name ?? $member->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">NIN Number</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->nin_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-gray-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                        <label class="block text-sm font-bold text-gray-600 mb-2 print:text-black">About Yourself</label>
                        <p class="text-gray-800 leading-relaxed print:text-sm">{{ $member->bioData->about_yourself ?? 'No information provided' }}</p>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-map-marked-alt text-purple-600 print:hidden"></i> 
                        <span class="print:font-bold">ADDRESS INFORMATION</span>
                    </h3>
                    
                    <!-- Present Address -->
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-700 mb-3 print:text-base print:underline">Present Address</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 print:gap-2">
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Region</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['region'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">District</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['district'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">County</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['county'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Sub-county</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['subcounty'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Parish/Ward</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['ward'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Village</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->present_address['village'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Permanent Address -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-700 mb-3 print:text-base print:underline">Permanent Address</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 print:gap-2">
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Region</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['region'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">District</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['district'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">County</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['county'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Sub-county</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['subcounty'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Parish/Ward</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['ward'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                                <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Village</label>
                                <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->permanent_address['village'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-phone text-green-600 print:hidden"></i> 
                        <span class="print:font-bold">CONTACT INFORMATION</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
                        <div class="bg-green-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Telephone</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ is_array($member->bioData->telephone ?? null) ? implode(', ', $member->bioData->telephone) : ($member->contact ?? 'N/A') }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Email</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->email ?? $member->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Age and Birth Information -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-birthday-cake text-pink-600 print:hidden"></i> 
                        <span class="print:font-bold">AGE AND BIRTH INFORMATION</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4 mb-6">
                        <div class="bg-pink-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Date of Birth</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->dob ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Nationality</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->nationality ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-bold text-gray-700 mb-3 print:text-base print:underline">Place of Birth</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 print:gap-2">
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Region</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['region'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">District</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['district'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">County</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['county'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Sub-county</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['subcounty'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Parish/Ward</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['ward'] ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-pink-50 p-3 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-xs font-bold text-gray-600 mb-1 print:text-black">Village</label>
                            <p class="font-semibold text-gray-800 print:text-sm">{{ $member->bioData->birth_place['village'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Other Particulars -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-info-circle text-orange-600 print:hidden"></i> 
                        <span class="print:font-bold">OTHER PARTICULARS</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
                        <div class="bg-orange-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Marital Status</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->marital_status ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Spouse Name</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->spouse_name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Next of Kin</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->next_of_kin ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Next of Kin NIN</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->next_of_kin_nin ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Parental Status -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-users text-indigo-600 print:hidden"></i> 
                        <span class="print:font-bold">PARENTAL STATUS</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
                        <div class="bg-indigo-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Father's Name</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->father_name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Mother's Name</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->mother_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Occupation -->
                <div class="border-b pb-6 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-briefcase text-yellow-600 print:hidden"></i> 
                        <span class="print:font-bold">OCCUPATION</span>
                    </h3>
                    <div class="bg-yellow-50 p-4 rounded-lg print:bg-white print:border print:border-gray-300">
                        <p class="text-gray-800 leading-relaxed print:text-sm">{{ $member->bioData->occupation ?? $member->occupation ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Declaration -->
                <div class="bg-blue-50 p-6 rounded-xl print:bg-white print:border-2 print:border-gray-400">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2 print:text-xl">
                        <i class="fas fa-pen-fancy text-red-600 print:hidden"></i> 
                        <span class="print:font-bold">DECLARATION</span>
                    </h3>
                    <p class="text-gray-700 mb-4 font-medium print:text-sm print:text-black">I declare that all particulars given above are true to the best of my knowledge.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
                        <div class="bg-white p-4 rounded-lg print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Signature</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base border-b-2 border-gray-300 pb-2">{{ $member->bioData->signature ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg print:border print:border-gray-300">
                            <label class="block text-sm font-bold text-gray-600 mb-1 print:text-black">Date</label>
                            <p class="text-lg font-semibold text-gray-800 print:text-base">{{ $member->bioData->declaration_date ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { font-size: 12px; }
    .border-gradient { border-image: linear-gradient(to right, #3b82f6, #a855f7, #ec4899) 1 !important; }
    .bg-gradient-to-r, .bg-gradient-to-br { 
        background: linear-gradient(to right, #3b82f6, #a855f7, #ec4899) !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .text-transparent { -webkit-background-clip: text !important; background-clip: text !important; color: transparent !important; }
}
</style>
@endsection