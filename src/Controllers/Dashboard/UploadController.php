<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use Source\Models\Plan;
use Source\Services\StorageService;
use App\Core\ORMHelper;

class UploadController extends DashboardController
{
    public function index()
    {
        $plan = ORMHelper::findByPK(Plan::class, $this->user->getPlanId());
        $maxSize = $plan ? $plan->getMaxFileSizeBytes() : 10485760;
        $allowedTypes = $plan ? implode(',', $plan->getAllowedMimeTypes()) : 'jpg,jpeg,png,gif,webp';

        return Response::view('dashboard/upload', [
            'title' => 'Upload Files',
            'page_title' => 'Upload Files',
            'active_menu' => 'upload',
            'user' => $this->user,
            'plan' => $plan,
            'max_size' => $maxSize,
            'allowed_types' => $allowedTypes
        ]);
    }

    public function process()
    {
        try {
            $file = $_FILES['file'] ?? null;
            $path = $this->request->input('path', '');

            if (!$file) {
                return Response::error('No file uploaded', 400);
            }

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

        } catch (\Exception $e) {
            return Response::error('Server error: ' . $e->getMessage(), 500);
        }
    }
}