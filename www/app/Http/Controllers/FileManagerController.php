<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileManagerController extends Controller
{
    private const GAMES_DIRECTORY = 'games';

    /**
     * Display the file manager page.
     */
    public function index(): \Illuminate\View\View
    {
        return view('file-manager.index', [
            'files' => $this->listFiles(self::GAMES_DIRECTORY),
            'currentPath' => '',
        ]);
    }

    /**
     * List files and directories in a path.
     */
    public function list(Request $request): JsonResponse
    {
        $path = $request->get('path', '');
        $fullPath = $path ? self::GAMES_DIRECTORY . '/' . $path : self::GAMES_DIRECTORY;

        if (!Storage::exists($fullPath)) {
            return response()->json(['error' => 'Directory not found'], 404);
        }

        $directories = $this->getDirectories($fullPath);
        $files = $this->getFiles($fullPath);

        return response()->json([
            'directories' => $directories,
            'files' => $files,
            'path' => $path,
        ]);
    }

    /**
     * Upload files.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file',
            'path' => 'nullable|string',
        ]);

        $path = $request->get('path', '');
        $targetPath = $path ? self::GAMES_DIRECTORY . '/' . $path : self::GAMES_DIRECTORY;

        if (!Storage::exists($targetPath)) {
            Storage::makeDirectory($targetPath);
        }

        $uploaded = [];
        foreach ($request->file('files') as $file) {
            $filename = $file->getClientOriginalName();
            $filePath = $targetPath . '/' . $filename;
            Storage::putFileAs($targetPath, $file, $filename);
            $uploaded[] = $filename;
        }

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Upload folder (as zip archive).
     */
    public function uploadFolder(Request $request): JsonResponse
    {
        $request->validate([
            'folder' => 'required|file',
            'path' => 'nullable|string',
        ]);

        $path = $request->get('path', '');
        $targetPath = $path ? self::GAMES_DIRECTORY . '/' . $path : self::GAMES_DIRECTORY;

        if (!Storage::exists($targetPath)) {
            Storage::makeDirectory($targetPath);
        }

        $file = $request->file('folder');
        $filename = $file->getClientOriginalName();
        $tempPath = storage_path('app/temp_' . time() . '_' . $filename);
        $file->move(dirname($tempPath), basename($tempPath));

        // Extract zip
        $zip = new \ZipArchive();
        if ($zip->open($tempPath) === true) {
            $extractPath = storage_path('app/' . $targetPath);
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
            $zip->extractTo($extractPath);
            $zip->close();

            // Remove .MacOSX and similar unwanted folders
            $this->removeMacOSX($extractPath);

            $uploaded = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $uploaded[] = $zip->getNameIndex($i);
            }
        } else {
            unlink($tempPath);
            return response()->json(['error' => 'Failed to extract archive'], 400);
        }

        unlink($tempPath);

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Remove .DS_Store and __MACOSX folders.
     */
    private function removeMacOSX(string $dir): void
    {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $dir . '/' . $item;
            if (is_dir($fullPath)) {
                if ($item === '.DS_Store' || $item === '__MACOSX') {
                    $this->deleteDirectory($fullPath);
                } else {
                    $this->removeMacOSX($fullPath);
                }
            }
        }
    }

    /**
     * Download a file.
     */
    public function download(Request $request): BinaryFileResponse|JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $filePath = self::GAMES_DIRECTORY . '/' . $request->get('path');

        if (!Storage::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $filename = basename($filePath);
        $fullPath = Storage::path($filePath);

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Download a folder as zip.
     */
    public function downloadFolder(Request $request): BinaryFileResponse|JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $folderPath = self::GAMES_DIRECTORY . '/' . $request->get('path');

        if (!Storage::exists($folderPath)) {
            return response()->json(['error' => 'Folder not found'], 404);
        }

        if (!Storage::isDirectory($folderPath)) {
            return $this->download($request);
        }

        $folderName = basename($folderPath);
        $tempZip = storage_path('app/temp_' . time() . '_' . $folderName . '.zip');

        $this->createZipArchive(Storage::path($folderPath), $tempZip, $folderName);

        return response()->download($tempZip, $folderName . '.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Create a zip archive from a folder.
     */
    private function createZipArchive(string $folderPath, string $zipPath, string $baseName): void
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $baseName . '/' . substr($filePath, strlen($folderPath) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    /**
     * Delete a file or folder.
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'type' => 'required|in:file,folder',
        ]);

        $targetPath = self::GAMES_DIRECTORY . '/' . $request->get('path');

        if (!Storage::exists($targetPath)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($request->get('type') === 'folder') {
            Storage::deleteDirectory($targetPath);
        } else {
            Storage::delete($targetPath);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Rename a file or folder.
     */
    public function rename(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'newName' => 'required|string|max:255',
            'type' => 'required|in:file,folder',
        ]);

        $targetPath = self::GAMES_DIRECTORY . '/' . $request->get('path');
        $newName = $request->get('newName');

        // Validate new name
        if (preg_match('/[\/\\\\:*?"<>|]/', $newName)) {
            return response()->json(['error' => 'Invalid characters in name'], 400);
        }

        if (!Storage::exists($targetPath)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $parentPath = dirname($targetPath);
        if ($parentPath === '.') {
            $parentPath = self::GAMES_DIRECTORY;
        }
        $newPath = $parentPath . '/' . $newName;

        if (Storage::exists($newPath)) {
            return response()->json(['error' => 'A file with this name already exists'], 400);
        }

        Storage::move($targetPath, $newPath);

        return response()->json(['success' => true, 'newPath' => $newName]);
    }

    /**
     * Create a new folder.
     */
    public function createFolder(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'name' => 'required|string|max:255',
        ]);

        $parentPath = $request->get('path');
        $folderName = $request->get('name');

        if (preg_match('/[\/\\\\:*?"<>|]/', $folderName)) {
            return response()->json(['error' => 'Invalid characters in name'], 400);
        }

        $fullPath = self::GAMES_DIRECTORY . '/' . ($parentPath ? $parentPath . '/' : '') . $folderName;

        if (Storage::exists($fullPath)) {
            return response()->json(['error' => 'Folder already exists'], 400);
        }

        Storage::makeDirectory($fullPath);

        return response()->json(['success' => true]);
    }

    /**
     * Get directories that contain installable files.
     */
    private function getDirectories(string $path): array
    {
        $dirs = collect(Storage::directories($path))
            ->filter(fn ($dir) => $this->containsInstallableFiles($dir))
            ->map(fn ($dir) => [
                'name' => basename($dir),
                'path' => substr($dir, strlen(self::GAMES_DIRECTORY) + 1),
                'mtime' => Storage::lastModified($dir),
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();

        // Also include non-nsp directories for management purposes
        $allDirs = collect(Storage::directories($path))
            ->map(fn ($dir) => [
                'name' => basename($dir),
                'path' => substr($dir, strlen(self::GAMES_DIRECTORY) + 1),
                'mtime' => Storage::lastModified($dir),
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();

        return $allDirs;
    }

    /**
     * Get files with allowed extensions.
     */
    private function getFiles(string $path): array
    {
        return collect(Storage::files($path))
            ->map(fn ($file) => [
                'name' => basename($file),
                'path' => substr($file, strlen(self::GAMES_DIRECTORY) + 1),
                'mtime' => Storage::lastModified($file),
                'size' => Storage::size($file),
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();
    }

    /**
     * Check if a directory contains any installable files.
     */
    private function containsInstallableFiles(string $directory): bool
    {
        return collect(Storage::allFiles($directory))
            ->contains(fn ($file) => \App\Enums\NintendoFileExtension::isSupported($file));
    }

    /**
     * List files for the main view.
     */
    private function listFiles(string $path): array
    {
        return [
            'directories' => $this->getDirectories($path),
            'files' => $this->getFiles($path),
        ];
    }

    /**
     * Recursively delete a directory.
     */
    private function deleteDirectory(string $dir): void
    {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $dir . '/' . $item;
            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                unlink($fullPath);
            }
        }
        rmdir($dir);
    }
}
