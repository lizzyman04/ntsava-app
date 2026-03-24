<?php

namespace Source\Services;

use Source\Models\User;
use Source\Models\File;
use App\Core\ORMHelper;

class StorageService
{
    public function upload(User $user, array $file, string $path = ''): array
    {
        try {
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
            }

            $maxSize = (int) env('UPLOAD_MAX_SIZE', 104857600);
            if ($file['size'] > $maxSize) {
                return ['success' => false, 'error' => "File too large. Max size: " . round($maxSize / 1048576, 2) . "MB"];
            }

            $allowedTypes = explode(',', env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp,mp4,pdf'));
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedTypes)) {
                return ['success' => false, 'error' => "File type '{$extension}' not allowed"];
            }

            // Generate storage path
            $storagePath = $user->getStoragePath();
            $relativePath = ltrim($path, '/');
            
            // Clean path (remove .., multiple slashes, etc)
            $relativePath = preg_replace('/\.\./', '', $relativePath);
            $relativePath = preg_replace('/\/+/', '/', $relativePath);
            
            $fullPath = $storagePath . '/' . $relativePath;
            $absolutePath = base_path('storage/' . $fullPath);
            
            // Create directory if needed
            $directory = dirname($absolutePath);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    return ['success' => false, 'error' => 'Failed to create directory'];
                }
            }
            
            // Check if file already exists
            $exists = file_exists($absolutePath);
            if ($exists && !$this->shouldOverwrite($file)) {
                return ['success' => false, 'error' => 'File already exists'];
            }
            
            // Move file
            if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
                return ['success' => false, 'error' => 'Failed to save file'];
            }
            
            // Create file record
            $fileEntity = new File(
                $user->getId(),
                $file['name'],
                $file['size'],
                mime_content_type($absolutePath)
            );
            $fileEntity->setStoragePath($fullPath);
            
            // Get image dimensions if image
            if (str_starts_with($fileEntity->getMimeType(), 'image/')) {
                $dimensions = getimagesize($absolutePath);
                if ($dimensions) {
                    $fileEntity->setDimensions($dimensions[0], $dimensions[1]);
                }
            }
            
            $entityManager = ORMHelper::getManager();
            $entityManager->persist($fileEntity);
            
            // Update user storage usage
            $user->addStorageUsedBytes($file['size']);
            $entityManager->persist($user);
            
            $entityManager->run();
            
            return [
                'success' => true,
                'url' => $user->getPublicUrl($relativePath),
                'uuid' => $fileEntity->getUuid(),
                'size' => $file['size'],
                'mime' => $fileEntity->getMimeType(),
                'path' => $relativePath
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function delete(User $user, string $pathOrUuid): array
    {
        try {
            $fileRepo = ORMHelper::getRepository(File::class);
            
            // Try to find by UUID first, then by path
            $file = $fileRepo->findOne(['userId' => $user->getId(), 'uuid' => $pathOrUuid]);
            
            if (!$file) {
                $storagePath = $user->getStoragePath() . '/' . ltrim($pathOrUuid, '/');
                $file = $fileRepo->findOne(['userId' => $user->getId(), 'storagePath' => $storagePath]);
            }
            
            if (!$file) {
                return ['success' => false, 'error' => 'File not found'];
            }
            
            // Delete physical file if exists
            $absolutePath = base_path('storage/' . $file->getStoragePath());
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }
            
            // Soft delete record
            $file->delete();
            
            // Update user storage
            $user->subtractStorageUsedBytes($file->getSizeBytes());
            
            $entityManager = ORMHelper::getManager();
            $entityManager->persist($file);
            $entityManager->persist($user);
            $entityManager->run();
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getInfo(User $user, string $pathOrUuid): array
    {
        $fileRepo = ORMHelper::getRepository(File::class);
        
        // Try to find by UUID first, then by path
        $file = $fileRepo->findOne(['userId' => $user->getId(), 'uuid' => $pathOrUuid]);
        
        if (!$file) {
            $storagePath = $user->getStoragePath() . '/' . ltrim($pathOrUuid, '/');
            $file = $fileRepo->findOne(['userId' => $user->getId(), 'storagePath' => $storagePath]);
        }
        
        if (!$file || $file->isDeleted()) {
            return ['success' => false, 'error' => 'File not found'];
        }
        
        return [
            'success' => true,
            'data' => [
                'uuid' => $file->getUuid(),
                'name' => $file->getOriginalName(),
                'size' => $file->getSizeBytes(),
                'size_mb' => round($file->getSizeBytes() / 1048576, 2),
                'mime' => $file->getMimeType(),
                'url' => $user->getPublicUrl($file->getStoragePath()),
                'path' => $file->getStoragePath(),
                'width' => $file->getWidth(),
                'height' => $file->getHeight(),
                'is_image' => $file->isImage(),
                'is_video' => $file->isVideo(),
                'created_at' => $file->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ];
    }
    
    private function shouldOverwrite(array $file): bool
    {
        // For now, never overwrite
        return false;
    }
}