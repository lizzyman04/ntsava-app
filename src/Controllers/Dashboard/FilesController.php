<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\File;

class FilesController extends DashboardController
{
    public function index()
    {
        $allFiles = ORMHelper::findAll(File::class);
        $files = [];
        
        foreach ($allFiles as $file) {
            if ($file->getUserId() === $this->user->getId() && !$file->isDeleted()) {
                $files[] = $file;
            }
        }
        
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
        $allFiles = ORMHelper::findAll(File::class);
        $file = null;
        
        foreach ($allFiles as $f) {
            if ($f->getUserId() === $this->user->getId() && $f->getUuid() === $uuid) {
                $file = $f;
                break;
            }
        }

        if (!$file) {
            return Response::error('File not found', 404);
        }

        $absolutePath = base_path('storage/' . $file->getStoragePath());
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        $file->delete();
        $this->user->subtractStorageUsedBytes($file->getSizeBytes());

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($file);
        $entityManager->persist($this->user);
        $entityManager->run();

        return Response::success(null, 'File deleted successfully');
    }
}