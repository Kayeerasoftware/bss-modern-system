@extends('layouts.member')

@section('title', 'My Documents')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-purple-50 p-3 md:p-6" x-data="documentsManager()">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 p-3 md:p-4 rounded-xl shadow-xl">
                        <i class="fas fa-folder-open text-white text-2xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-1">My Documents</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage and access your important documents</p>
                </div>
            </div>
            <button @click="showUploadModal = true" class="w-full md:w-auto px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 font-bold flex items-center justify-center gap-2 transform hover:scale-105">
                <i class="fas fa-upload"></i>Upload Document
            </button>
        </div>
    </div>

    <div class="relative h-2 bg-gray-200 rounded-full mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full animate-slide-right"></div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-2xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-3 md:p-4 text-white">
            <i class="fas fa-file text-2xl mb-2 opacity-80"></i>
            <p class="text-white/80 text-xs font-medium">Total Documents</p>
            <h3 class="text-xl md:text-3xl font-bold">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
            <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Verified</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $stats['verified'] }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-orange-500">
            <i class="fas fa-clock text-2xl text-orange-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Pending</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-purple-500">
            <i class="fas fa-database text-2xl text-purple-500 mb-2"></i>
            <p class="text-gray-600 text-xs font-medium">Total Size</p>
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ $stats['size'] }}</h3>
        </div>
    </div>

    <!-- Document Categories -->
    <div class="bg-white rounded-2xl shadow-xl p-4 mb-4 border border-gray-100">
        <div class="flex flex-wrap gap-2">
            <button @click="category = 'all'" :class="category === 'all' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                <i class="fas fa-th mr-1"></i>All Documents
            </button>
            <button @click="category = 'identity'" :class="category === 'identity' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                <i class="fas fa-id-card mr-1"></i>Identity
            </button>
            <button @click="category = 'financial'" :class="category === 'financial' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                <i class="fas fa-dollar-sign mr-1"></i>Financial
            </button>
            <button @click="category = 'contracts'" :class="category === 'contracts' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                <i class="fas fa-file-contract mr-1"></i>Contracts
            </button>
            <button @click="category = 'receipts'" :class="category === 'receipts' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-4 py-2 rounded-lg text-sm font-semibold transition transform hover:scale-105">
                <i class="fas fa-receipt mr-1"></i>Receipts
            </button>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 border border-gray-100">
        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-folder text-purple-600"></i>
            Document Library
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($documents as $document)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                        {{ $document->type === 'pdf' ? 'bg-red-100' : '' }}
                        {{ $document->type === 'image' ? 'bg-blue-100' : '' }}
                        {{ $document->type === 'doc' ? 'bg-purple-100' : '' }}">
                        <i class="fas 
                            {{ $document->type === 'pdf' ? 'fa-file-pdf text-red-600' : '' }}
                            {{ $document->type === 'image' ? 'fa-file-image text-blue-600' : '' }}
                            {{ $document->type === 'doc' ? 'fa-file-word text-purple-600' : '' }} text-2xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        {{ $document->status === 'verified' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $document->status === 'pending' ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $document->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($document->status) }}
                    </span>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">{{ $document->name }}</h4>
                <p class="text-sm text-gray-600 mb-3">{{ ucfirst($document->category) }}</p>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                    <span><i class="fas fa-calendar mr-1"></i>{{ $document->created_at->format('M d, Y') }}</span>
                    <span><i class="fas fa-file mr-1"></i>{{ $document->size }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ $document->url }}" target="_blank" class="flex-1 px-3 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition text-center text-sm font-semibold">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('member.documents.show', $document->id) }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center text-sm font-semibold">
                        <i class="fas fa-download"></i>
                    </a>
                    <form action="{{ route('member.documents.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="inline-block p-6 bg-purple-50 rounded-full mb-4">
                    <i class="fas fa-folder-open text-6xl text-purple-300"></i>
                </div>
                <p class="text-lg font-bold text-gray-700 mb-2">No documents yet</p>
                <p class="text-sm text-gray-500 mb-4">Upload your first document to get started</p>
                <button @click="showUploadModal = true" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-xl transition font-bold">
                    <i class="fas fa-upload mr-2"></i>Upload Document
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showUploadModal = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white"><i class="fas fa-upload mr-2"></i>Upload Document</h3>
                    <button @click="showUploadModal = false" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('member.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-tag mr-1"></i>Document Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-folder mr-1"></i>Category</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select Category</option>
                        <option value="identity">Identity</option>
                        <option value="financial">Financial</option>
                        <option value="contracts">Contracts</option>
                        <option value="receipts">Receipts</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file mr-1"></i>File</label>
                    <input type="file" name="file" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Max size: 10MB. Supported: PDF, JPG, PNG, DOC, DOCX</p>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="button" @click="showUploadModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function documentsManager() {
    return {
        showUploadModal: false,
        category: 'all'
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
@keyframes slide-right { 0% { width: 0%; } 100% { width: 100%; } }
.animate-slide-right { animation: slide-right 5s ease-out forwards; }
</style>
@endsection