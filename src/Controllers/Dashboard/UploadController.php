<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use Source\Services\StorageService;

class UploadController extends DashboardController
{
    public function index()
    {
        return Response::view('dashboard/upload', [
            'title' => 'Upload Files',
            'page_title' => 'Upload Files',
            'active_menu' => 'upload',
            'user' => $this->user,
            'max_size' => env('UPLOAD_MAX_SIZE', 104857600),
            'allowed_types' => env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp,mp4,pdf')
        ]);
    }
    
    public function process()
    {
        $file = $_FILES['file'] ?? null;
        $path = $this->request->input('path', '');
        
        if (!$file) {
            return Response::error('No file uploaded', 400);
        }
        
        // Check storage quota
        if (!$this->user->hasStorageAvailable($file['size'])) {
            return Response::error('Storage quota exceeded', 403, [
                'used' => $this->user->getStorageUsedBytes(),
                'limit' => $this->user->getStorageLimitBytes(),
                'used_gb' => round($this->user->getStorageUsedBytes() / 1073741824, 2),
                'limit_gb' => round($this->user->getStorageLimitBytes() / 1073741824, 2)
            ]);
        }
        
        $storageService = new StorageService();
        $result = $storageService->upload($this->user, $file, $path);
        
        if (!$result['success']) {
            return Response::error($result['error'], 500);
        }
        
        return Response::success([
            'url' => $result['url'],
            'uuid' => $result['uuid'],
            'size' => $result['size'],
            'size_mb' => round($result['size'] / 1048576, 2),
            'mime' => $result['mime'],
            'path' => $result['path']
        ], 'File uploaded successfully');
    }
}