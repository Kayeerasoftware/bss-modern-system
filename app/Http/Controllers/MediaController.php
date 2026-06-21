namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Upload a photo, video, or file to Cloudflare R2 and log to Aiven MySQL.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // Limit to 100MB as an example
        ]);

        $file = $request->file('file');

        // Automatically streams the file directly to R2 under the 'uploads' directory
        // putFile handles chunking internally so Render won't crash on large videos
        $path = Storage::disk('s3')->putFile('uploads', $file);

        if (!$path) {
            return response()->json(['error' => 'Upload failed'], 500);
        }

        // Save metadata to Aiven MySQL
        $media = MediaFile::create([
            'original_name' => $file->getClientOriginalName(),
            'r2_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'Uploaded successfully',
            'data' => $media
        ], 201);
    }

    /**
     * Get a downloadable or streamable URL for a file.
     */
    public function show($id)
    {
        $media = MediaFile::findOrFail($id);

        // If your bucket is private, generate a temporary signed URL valid for 15 minutes
        // If your bucket is public via a custom domain, you can just return the direct URL
        $url = Storage::disk('s3')->temporaryUrl($media->r2_path, now()->addMinutes(15));

        return response()->json([
            'file' => $media,
            'stream_url' => $url
        ]);
    }

    /**
     * Delete a file from both R2 and the Database.
     */
    public function destroy($id)
    {
        $media = MediaFile::findOrFail($id);

        // Remove from Cloudflare R2
        if (Storage::disk('s3')->exists($media->r2_path)) {
            Storage::disk('s3')->delete($media->r2_path);
        }

        // Remove from Aiven MySQL
        $media->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
