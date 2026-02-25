@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Import Members</h2>
    <p class="text-gray-600">Bulk import members from CSV or Excel file</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Upload File</h3>
        <form action="{{ route('admin.members.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="skip_duplicates" value="1" checked class="rounded">
                    <span class="ml-2 text-sm">Skip duplicate entries</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="send_welcome_email" value="1" class="rounded">
                    <span class="ml-2 text-sm">Send welcome email to new members</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i>Import Members
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Instructions</h3>
        <div class="space-y-3 text-sm">
            <p>1. Download the sample template file</p>
            <p>2. Fill in member information following the format</p>
            <p>3. Required fields: Name, Email, Phone</p>
            <p>4. Upload the completed file</p>
        </div>
        
        <a href="{{ route('admin.members.import.template') }}" 
           class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>Download Template
        </a>
    </div>
</div>
@endsection
