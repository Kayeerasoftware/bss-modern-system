<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function index()
    {
        $backups = DB::table('backups')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'filename' => $b->filename,
                    'size' => $this->formatBytes($b->size),
                    'type' => $b->type,
                    'status' => $b->status,
                    'created_at' => date('M d, Y g:i A', strtotime($b->created_at)),
                ];
            });

        return response()->json($backups);
    }

    public function create()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.json';
            $filepath = $this->backupPath . '/' . $filename;

            $tables = DB::select('SHOW TABLES');
            $dbName = env('DB_DATABASE');
            $tableKey = 'Tables_in_' . $dbName;

            $backup = [];
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                $backup[$tableName] = DB::table($tableName)->get()->toArray();
            }

            File::put($filepath, json_encode($backup, JSON_PRETTY_PRINT));
            $size = File::size($filepath);

            DB::table('backups')->insert([
                'filename' => $filename,
                'path' => $filepath,
                'size' => $size,
                'type' => 'full',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'filename' => $filename, 'size' => $this->formatBytes($size)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function download($id)
    {
        $backup = DB::table('backups')->find($id);
        if (!$backup || !File::exists($backup->path)) {
            return response()->json(['error' => 'Backup not found'], 404);
        }

        return response()->download($backup->path, $backup->filename);
    }

    public function restore(Request $request)
    {
        $file = $request->file('backup');
        if (!$file || $file->getClientOriginalExtension() !== 'json') {
            return response()->json(['success' => false, 'message' => 'Invalid backup file'], 400);
        }
        
        $data = json_decode(file_get_contents($file->getRealPath()), true);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON format'], 400);
        }
        
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($data as $table => $rows) {
                DB::table($table)->truncate();
                if (!empty($rows)) {
                    DB::table($table)->insert($rows);
                }
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return response()->json(['success' => true, 'message' => 'Database restored successfully']);
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $backup = DB::table('backups')->find($id);
        if (!$backup) {
            return response()->json(['success' => false, 'message' => 'Backup not found'], 404);
        }
        
        $filename = $backup->filename;
        
        if (File::exists($backup->path)) {
            File::delete($backup->path);
        }
        DB::table('backups')->where('id', $id)->delete();
        
        DB::table('audit_logs')->insert([
            'user' => auth()->user()->name ?? 'Admin',
            'action' => 'Backup Deleted',
            'details' => "Deleted backup: {$filename}",
            'timestamp' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
