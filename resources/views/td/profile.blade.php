@extends('layouts.td')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">My Profile</h2>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('td.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Profile Picture</label>
                <div class="w-28 h-28 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                    <img src="{{ auth()->user()->profile_picture_url }}" alt="Profile" class="w-full h-full object-cover">
                </div>
            </div>
            <div></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <input type="file" id="profilePictureInput" accept="image/*" onchange="showCropModal(event)" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Profile</button>
        </div>
    </form>
</div>

<!-- Crop Modal -->
<div id="cropModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-white"><i class="fas fa-crop-alt mr-2"></i>Crop Profile Picture</h3>
            <button onclick="closeCropModal()" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl transition-all">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4 bg-gray-100 rounded-2xl overflow-hidden" style="max-height: 400px;">
                <img id="cropImage" class="max-w-full">
            </div>
            <div class="flex gap-3 justify-end">
                <button onclick="closeCropModal()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-xl font-bold transition-all">
                    Cancel
                </button>
                <button onclick="cropAndUpload()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-check mr-2"></i>Crop & Upload
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cropper = null;
let currentFile = null;

function showCropModal(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    currentFile = file;
    const reader = new FileReader();
    reader.onload = function(e) {
        const image = document.getElementById('cropImage');
        image.src = e.target.result;
        document.getElementById('cropModal').classList.remove('hidden');
        
        if (cropper) cropper.destroy();
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    document.getElementById('cropModal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('profilePictureInput').value = '';
}

function cropAndUpload() {
    if (!cropper) return;
    
    cropper.getCroppedCanvas({
        width: 512,
        height: 512,
        imageSmoothingQuality: 'high'
    }).toBlob(function(blob) {
        const formData = new FormData();
        formData.append('profile_picture', blob, currentFile.name);
        
        fetch('{{ route("td.profile.picture") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCropModal();
                location.reload();
            } else {
                alert(data.message || 'Error uploading picture');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error uploading picture');
        });
    }, 'image/jpeg', 0.95);
}
</script>
@endsection
