<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;
        
        $query = Document::where('member_id', $member->member_id ?? null);

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('file_type', 'like', "%{$search}%");
            });
        }

        if (request()->filled('category')) {
            $query->where('category', request('category'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $documents = $query->latest()->get();
        
        $stats = [
            'total' => $documents->count(),
            'verified' => $documents->where('status', 'verified')->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'size' => $this->formatBytes($documents->sum('file_size'))
        ];
        
        return view('member.documents.index', compact('documents', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
            'name' => 'required|string|max:255',
            'category' => 'required|string'
        ]);

        $user = Auth::user();
        $member = $user->member;
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $extension = $file->getClientOriginalExtension();
            $type = in_array($extension, ['jpg', 'jpeg', 'png']) ? 'image' : 
                   ($extension === 'pdf' ? 'pdf' : 'doc');
            
            Document::create([
                'member_id' => $member->member_id ?? null,
                'name' => $request->name,
                'category' => $request->category,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $file->getSize(),
                'type' => $type,
                'status' => 'pending'
            ]);
        }

        return redirect()->back()->with('success', 'Document uploaded successfully');
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return response()->download(storage_path('app/public/' . $document->file_path));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        
        return redirect()->back()->with('success', 'Document deleted successfully');
    }
    
    private function formatBytes($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }
}
