<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\File;

class FilesController extends DashboardController
{
    public function index()
    {
        $fileRepo = ORMHelper::getRepository(File::class);
        $files = $fileRepo->findAll(['userId' => $this->user->getId(), 'deletedAt' => null]);

        // Sort by created_at DESC manually
        usort($files, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        return Response::view('dashboard/files', [
            'title' => 'My Files',
            'page_title' => 'My Files',
            'active_menu' => 'files',
            'user' => $this->user,
            'files' => $files
        ]);
    }

    public function delete($uuid)
    {
        $fileRepo = ORMHelper::getRepository(File::class);
        $file = $fileRepo->findOne(['userId' => $this->user->getId(), 'uuid' => $uuid]);

        if (!$file) {
            return Response::error('File not found', 404);
        }

        // Delete physical file
        $absolutePath = base_path('storage/' . $file->getStoragePath());
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        // Soft delete record
        $file->delete();

        // Update user storage
        $this->user->subtractStorageUsedBytes($file->getSizeBytes());

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($file);
        $entityManager->persist($this->user);
        $entityManager->run();

        return Response::success(null, 'File deleted successfully');
    }
}