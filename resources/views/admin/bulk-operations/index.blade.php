@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50 p-3 md:p-6">
    <div class="mb-4 md:mb-6">
        <div class="flex items-center gap-2 md:gap-4">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                <div class="relative bg-gradient-to-br from-blue-600 to-indigo-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                    <i class="fas fa-tasks text-white text-xl md:text-3xl"></i>
                </div>
            </div>
            <div>
                <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-1 md:mb-2">Bulk Operations</h1>
                <p class="text-gray-600 text-xs md:text-sm font-medium">Perform batch operations on multiple records</p>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-blue-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Operations...</span>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Total Members</p>
                <p class="text-2xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Last Import</p>
                <p class="text-sm font-bold text-green-600">Never</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                <p class="text-sm text-gray-600">Messages Sent</p>
                <p class="text-2xl font-bold text-purple-600">0</p>
            </div>
        </div>

        <!-- Operation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Import Members -->
            <div class="border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition hover:border-blue-400">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-file-import text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Import Members</h3>
                        <p class="text-xs text-gray-600">Upload CSV file</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Bulk import members from CSV file with all details</p>
                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-2"></i>Import CSV
                </button>
            </div>

            <!-- Export Members -->
            <div class="border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition hover:border-green-400">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-file-export text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Export Members</h3>
                        <p class="text-xs text-gray-600">Download CSV</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Export all member data to CSV format</p>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </button>
            </div>

            <!-- Bulk SMS -->
            <div class="border-2 border-orange-200 rounded-lg p-6 hover:shadow-lg transition hover:border-orange-400">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-sms text-orange-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Bulk SMS</h3>
                        <p class="text-xs text-gray-600">Send to multiple</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Send SMS messages to selected members</p>
                <button class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-paper-plane mr-2"></i>Send SMS
                </button>
            </div>

            <!-- Bulk Email -->
            <div class="border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition hover:border-purple-400">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-envelope-bulk text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Bulk Email</h3>
                        <p class="text-xs text-gray-600">Email campaign</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Send emails to multiple members at once</p>
                <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-envelope mr-2"></i>Send Email
                </button>
            </div>

            <!-- Bulk Update -->
            <div class="border-2 border-red-200 rounded-lg p-6 hover:shadow-lg transition hover:border-red-400">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-edit text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Bulk Update</h3>
                        <p class="text-xs text-gray-600">Update records</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Update multiple member records at once</p>
                <button class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-sync mr-2"></i>Bulk Update
                </button>
            </div>

            <!-- Bulk Delete -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition hover:border-gray-400">
                <div class="flex items-center mb-4">
                    <div class="bg-gray-100 p-3 rounded-lg mr-3">
                        <i class="fas fa-trash-alt text-gray-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Bulk Delete</h3>
                        <p class="text-xs text-gray-600">Remove records</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">Delete multiple inactive members</p>
                <button class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-trash mr-2"></i>Bulk Delete
                </button>
            </div>
        </div>

        <!-- CSV Template Download -->
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-blue-800">Need CSV Template?</h4>
                    <p class="text-sm text-blue-600">Download sample CSV format for member import</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i>Download Template
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slide-right {
    0% { width: 0%; }
    100% { width: 100%; }
}
.animate-slide-right {
    animation: slide-right 5s ease-out forwards;
}
@keyframes slide-text {
    0% { left: 0%; opacity: 1; }
    95% { opacity: 1; }
    100% { left: 100%; opacity: 0; }
}
.animate-slide-text {
    animation: slide-text 5s ease-out forwards;
}
</style>
@endsection
