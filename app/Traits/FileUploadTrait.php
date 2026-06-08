<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Upload a file and return the path
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return string|null
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        try {
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, $disk);
            
            return asset('storage/' . $path);
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from storage
     * 
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $filePath, string $disk = 'public'): bool
    {
        try {
            // Extract just the path portion if it's a full URL
            if (str_contains($filePath, 'storage/')) {
                $filePath = str_replace(asset('storage/'), '', $filePath);
            }
            
            if (Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update file - delete old and upload new
     * 
     * @param UploadedFile $newFile
     * @param string|null $oldFilePath
     * @param string $directory
     * @param string $disk
     * @return string|null
     */
    public function updateFile(UploadedFile $newFile, ?string $oldFilePath = null, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        if ($oldFilePath) {
            $this->deleteFile($oldFilePath, $disk);
        }
        
        return $this->uploadFile($newFile, $directory, $disk);
    }
}
