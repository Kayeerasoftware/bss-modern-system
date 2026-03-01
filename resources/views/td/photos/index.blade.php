@extends('layouts.td')

@section('title', 'Photo Gallery Management')

@section('content')
<div x-data="photoManager()" x-init="init()" class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-3 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl md:rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-indigo-600 p-2 md:p-4 rounded-xl md:rounded-2xl shadow-xl">
                        <i class="fas fa-images text-white text-xl md:text-3xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-600 bg-clip-text text-transparent mb-1 md:mb-2">Photo Gallery Management</h1>
                    <p class="text-gray-600 text-xs md:text-sm font-medium">Manage organization photos displayed on dashboard</p>
                </div>
            </div>
            <div class="flex gap-1.5 md:gap-2 w-full md:w-auto">
                <button @click="showUploadModal = true" class="flex-1 md:flex-none px-3 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-upload"></i><span>Upload Photos</span>
                </button>
                <button class="px-3 md:px-5 py-2 md:py-2.5 bg-white text-gray-700 rounded-lg md:rounded-xl hover:shadow-xl transition-all duration-300 shadow-md border border-gray-200 text-xs md:text-sm font-bold flex items-center justify-center gap-1 md:gap-2 transform hover:scale-105">
                    <i class="fas fa-file-export"></i><span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Animated Separator Line -->
    <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-4 md:mb-6">
        <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full animate-slide-right"></div>
        <span class="absolute -top-6 text-2xl md:text-3xl text-purple-600 font-bold animate-slide-text whitespace-nowrap z-10">Loading Photos data...</span>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4" x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 md:mb-4">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-[8px] md:text-[10px] font-medium mb-0.5">Total Photos</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $photos->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-images text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[8px] md:text-[10px] font-medium mb-0.5">Project Photos</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $projectPhotos->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-project-diagram text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-[8px] md:text-[10px] font-medium mb-0.5">Meeting Photos</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $meetingPhotos->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-handshake text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2 md:p-3 text-white shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-[8px] md:text-[10px] font-medium mb-0.5">Active Photos</p>
                    <h3 class="text-base md:text-xl font-bold">{{ $photos->where('is_active', true)->count() }}</h3>
                </div>
                <div class="bg-white/20 p-1.5 md:p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-eye text-sm md:text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gradient-to-br from-purple-50 via-indigo-50 to-pink-50 rounded-2xl shadow-lg border border-purple-100 overflow-hidden mb-4">
        <div class="bg-white/60 backdrop-blur-sm p-3">
            <div class="bg-white/80 rounded-xl p-2.5 border border-purple-100">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-5 relative">
                        <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-purple-400 text-xs"></i>
                        <input type="text" x-model="searchQuery" @input="filterPhotos()" placeholder="Search photos by title or description..." class="w-full pl-8 pr-2 py-1.5 text-xs border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                    </div>
                    <div class="md:col-span-2 relative">
                        <i class="fas fa-filter absolute left-2.5 top-1/2 transform -translate-y-1/2 text-indigo-400 text-xs"></i>
                        <select x-model="filterType" @change="filterPhotos()" class="w-full pl-8 pr-2 py-1.5 text-xs border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all appearance-none bg-white">
                            <option value="all">All Types</option>
                            @foreach($photoTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 relative">
                        <i class="fas fa-toggle-on absolute left-2.5 top-1/2 transform -translate-y-1/2 text-pink-400 text-xs"></i>
                        <select x-model="filterStatus" @change="filterPhotos()" class="w-full pl-8 pr-2 py-1.5 text-xs border border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent bg-white">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex gap-1.5">
                        <button @click="resetFilters()" class="flex-1 px-2 py-1.5 text-xs bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:shadow-md transition-all duration-300 font-semibold">
                            <i class="fas fa-redo mr-1"></i>Reset
                        </button>
                        <button @click="toggleView('grid')" :class="viewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-2 py-1.5 text-xs rounded-lg hover:shadow-lg transition-all font-semibold">
                            <i class="fas fa-th"></i>
                        </button>
                        <button @click="toggleView('list')" :class="viewMode === 'list' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 border border-gray-200'" class="px-2 py-1.5 text-xs rounded-lg hover:shadow-lg transition-all font-semibold">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="photo in filteredPhotos" :key="photo.id">
            <div class="bg-white border rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 group">
                <div class="relative h-48">
                    <img :src="photo.photo_path" :alt="photo.title" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2 flex gap-2">
                        <span :class="typeBadgeClass(photo.type)" class="px-2 py-1 text-xs rounded-full font-semibold text-white">
                            <span x-text="formatType(photo.type)"></span>
                        </span>
                        <button @click="toggleStatus(photo.id, !photo.is_active)" :class="photo.is_active ? 'bg-green-500' : 'bg-gray-500'" class="px-2 py-1 text-xs rounded-full font-semibold text-white hover:opacity-80">
                            <span x-text="photo.is_active ? 'Active' : 'Inactive'"></span>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-1" x-text="photo.title"></h3>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2" x-text="photo.description || 'No description'"></p>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span><i class="fas fa-sort mr-1"></i>Order: <span x-text="photo.order"></span></span>
                        <span><i class="fas fa-calendar mr-1"></i><span x-text="photo.created_at"></span></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editPhoto(photo)" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-2 rounded-lg hover:shadow-lg transition text-sm font-bold">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <button @click="deletePhoto(photo.id)" class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-3 py-2 rounded-lg hover:shadow-lg transition text-sm font-bold">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <div x-show="filteredPhotos.length === 0" class="col-span-4 bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 p-6 rounded-full mb-4">
                    <i class="fas fa-images text-purple-400 text-5xl"></i>
                </div>
                <p class="text-gray-500 text-lg font-semibold">No photos found</p>
                <p class="text-gray-400 text-sm mt-2">Upload your first photo to get started</p>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div x-show="viewMode === 'list'" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-purple-50 via-indigo-50 to-pink-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Photo</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="photo in filteredPhotos" :key="photo.id">
                        <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-200">
                            <td class="px-4 py-3">
                                <img :src="photo.photo_path" :alt="photo.title" class="w-16 h-16 rounded-lg object-cover shadow-md">
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900" x-text="photo.title"></p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-gray-500 line-clamp-2" x-text="photo.description || 'No description'"></p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="typePillClass(photo.type)" class="px-3 py-1 text-xs font-bold rounded-full border">
                                    <i :class="typeIcon(photo.type)" class="fas text-[6px] mr-1"></i>
                                    <span x-text="formatType(photo.type)"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button @click="toggleStatus(photo.id, !photo.is_active)" :class="photo.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-800 border-gray-200'" class="px-3 py-1 text-xs font-bold rounded-full border hover:shadow-md transition">
                                    <i :class="photo.is_active ? 'fa-check-circle' : 'fa-times-circle'" class="fas text-[6px] mr-1"></i>
                                    <span x-text="photo.is_active ? 'Active' : 'Inactive'"></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-purple-600" x-text="photo.order"></span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-calendar text-purple-500 text-xs"></i>
                                    <span x-text="photo.created_at"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="editPhoto(photo)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-all transform hover:scale-110" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deletePhoto(photo.id)" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all transform hover:scale-110" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredPhotos.length === 0">
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 p-6 rounded-full mb-4">
                                    <i class="fas fa-images text-purple-400 text-5xl"></i>
                                </div>
                                <p class="text-gray-500 text-lg font-semibold">No photos found</p>
                                <p class="text-gray-400 text-sm mt-2">Upload your first photo to get started</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showUploadModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-upload mr-3"></i>Upload Photo(s)
                </h2>
            </div>
            <form action="{{ route('td.photos.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Photo Type *</label>
                    <select name="type" x-model="uploadType" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="project">Project Photo</option>
                        <option value="meeting">Meeting Photo</option>
                        <option value="other">Other Category</option>
                    </select>
                </div>
                <div x-show="uploadType === 'other'" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Category Name *</label>
                    <input
                        type="text"
                        name="custom_type"
                        x-model="customType"
                        :required="uploadType === 'other'"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"
                        placeholder="e.g event, field_visit, workshop"
                    >
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Photos *</label>
                    <input type="file" name="photos[]" accept="image/*" multiple required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Select one or many photos. Max 30 files, 5MB each. Formats: JPG, PNG, GIF.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title / Base Title</label>
                    <input type="text" name="title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Used for single photo, or base name for bulk">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Enter photo description"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Display Order</label>
                    <input type="number" name="order" value="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                    <button type="button" @click="showUploadModal = false" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showEditModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>Edit Photo
                </h2>
            </div>
            <form :action="`{{ route('td.photos.index') }}/${editPhotoId}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" x-model="editPhotoTitle" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" x-model="editPhotoDescription" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Display Order</label>
                    <input type="number" name="order" x-model="editPhotoOrder" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" :checked="editPhotoActive" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <label class="ml-2 text-sm font-semibold text-gray-700">Active (Display on dashboard)</label>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <button type="button" @click="showEditModal = false" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-trash text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-center mb-2">Delete Photo?</h3>
                <p class="text-gray-600 text-center mb-6">This action cannot be undone. The photo will be permanently deleted.</p>
                <form :action="`{{ route('td.photos.index') }}/${deletePhotoId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                        <button type="button" @click="showDeleteModal = false" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function photoManager() {
    return {
        viewMode: 'grid',
        searchQuery: '',
        filterType: 'all',
        filterStatus: 'all',
        showUploadModal: false,
        uploadType: 'project',
        customType: '',
        showEditModal: false,
        showDeleteModal: false,
        editPhotoId: null,
        editPhotoTitle: '',
        editPhotoDescription: '',
        editPhotoOrder: 0,
        editPhotoActive: true,
        deletePhotoId: null,
        allPhotos: {!! json_encode($photos->map(function($photo) {
            return [
                'id' => $photo->id,
                'title' => $photo->title,
                'description' => $photo->description,
                'photo_path' => $photo->photo_path,
                'type' => $photo->type,
                'order' => $photo->order,
                'is_active' => $photo->is_active,
                'created_at' => $photo->created_at->format('M d, Y')
            ];
        })) !!},
        filteredPhotos: [],

        init() {
            this.filteredPhotos = this.allPhotos;
        },

        toggleView(mode) {
            this.viewMode = mode;
        },

        filterPhotos() {
            this.filteredPhotos = this.allPhotos.filter(photo => {
                const matchesSearch = !this.searchQuery || 
                    photo.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (photo.description && photo.description.toLowerCase().includes(this.searchQuery.toLowerCase()));
                
                const matchesType = this.filterType === 'all' || photo.type === this.filterType;
                
                const matchesStatus = this.filterStatus === 'all' || 
                    (this.filterStatus === 'active' && photo.is_active) ||
                    (this.filterStatus === 'inactive' && !photo.is_active);
                
                return matchesSearch && matchesType && matchesStatus;
            });
        },

        formatType(type) {
            if (!type) return 'Other';
            return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        },

        typeBadgeClass(type) {
            if (type === 'project') return 'bg-blue-500';
            if (type === 'meeting') return 'bg-green-500';
            return 'bg-gray-600';
        },

        typePillClass(type) {
            if (type === 'project') return 'bg-blue-100 text-blue-800 border-blue-200';
            if (type === 'meeting') return 'bg-green-100 text-green-800 border-green-200';
            return 'bg-gray-100 text-gray-800 border-gray-200';
        },

        typeIcon(type) {
            if (type === 'project') return 'fa-project-diagram';
            if (type === 'meeting') return 'fa-handshake';
            return 'fa-image';
        },

        resetFilters() {
            this.searchQuery = '';
            this.filterType = 'all';
            this.filterStatus = 'all';
            this.filterPhotos();
        },

        editPhoto(photo) {
            console.log('Edit photo clicked:', photo);
            this.editPhotoId = photo.id;
            this.editPhotoTitle = photo.title;
            this.editPhotoDescription = photo.description || '';
            this.editPhotoOrder = photo.order;
            this.editPhotoActive = photo.is_active;
            this.showEditModal = true;
            console.log('Modal should show:', this.showEditModal);
        },

        deletePhoto(id) {
            this.deletePhotoId = id;
            this.showDeleteModal = true;
        },

        async toggleStatus(id, newStatus) {
            try {
                const response = await fetch(`{{ route('td.photos.index') }}/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error toggling status:', error);
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
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
