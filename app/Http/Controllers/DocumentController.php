<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        return response()->json(Document::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
            'file_type' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $document = Document::create([
            ...$validated,
            'file_path' => '/documents/' . $validated['filename'],
            'file_size' => 1024,
            'uploaded_by' => auth()->user()->id ?? 'system',
            'access_roles' => json_encode(['admin', 'manager'])
        ]);

        return response()->json(['success' => true, 'document' => $document]);
    }

    public function show($id)
    {
        return response()->json(Document::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->update($request->all());
        return response()->json(['success' => true, 'document' => $document]);
    }

    public function destroy($id)
    {
        Document::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        return response()->json(['download_url' => $document->file_path]);
    }
}