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
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Upload error: ' . $this->getUploadErrorMessage($file['error'])];
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

            $storagePath = $user->getStoragePath();
            $relativePath = ltrim($path, '/');

            $relativePath = preg_replace('/\.\./', '', $relativePath);
            $relativePath = preg_replace('/\/+/', '/', $relativePath);

            $fullPath = $storagePath;
            if (!empty($relativePath)) {
                $fullPath .= '/' . $relativePath;
            }

            $originalName = $file['name'];
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);

            $uniqueId = substr(bin2hex(random_bytes(3)), 0, 6);
            $fileName = $nameWithoutExt . '_' . $uniqueId;

            if (strlen($fileName) > 200) {
                $fileName = substr($fileName, 0, 200);
            }

            $fileName = $fileName . '.' . $extension;
            $fullPathWithFile = $fullPath . '/' . $fileName;
            $absolutePath = base_path('storage/' . $fullPathWithFile);

            $directory = dirname($absolutePath);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    return ['success' => false, 'error' => 'Failed to create directory'];
                }
            }

            if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
                return ['success' => false, 'error' => 'Failed to save file'];
            }

            $fileEntity = new File(
                $user->getId(),
                $originalName,
                $file['size'],
                mime_content_type($absolutePath)
            );
            $fileEntity->setStoragePath($fullPathWithFile);

            if (str_starts_with($fileEntity->getMimeType(), 'image/')) {
                $dimensions = getimagesize($absolutePath);
                if ($dimensions) {
                    $fileEntity->setDimensions($dimensions[0], $dimensions[1]);
                }
            }

            $entityManager = ORMHelper::getManager();
            $entityManager->persist($fileEntity);

            $user->addStorageUsedBytes($file['size']);
            $entityManager->persist($user);

            $entityManager->run();

            $publicUrl = $user->getPublicUrl($relativePath ? $relativePath . '/' . $fileName : $fileName);

            return [
                'success' => true,
                'url' => $publicUrl,
                'uuid' => $fileEntity->getUuid(),
                'size' => $file['size'],
                'mime' => $fileEntity->getMimeType(),
                'path' => $relativePath ? $relativePath . '/' . $fileName : $fileName
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getUploadErrorMessage($code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    public function delete(User $user, string $pathOrUuid): array
    {
        try {
            $fileRepo = ORMHelper::getRepository(File::class);

            $file = $fileRepo->findOne(['userId' => $user->getId(), 'uuid' => $pathOrUuid]);

            if (!$file) {
                $storagePath = $user->getStoragePath() . '/' . ltrim($pathOrUuid, '/');
                $file = $fileRepo->findOne(['userId' => $user->getId(), 'storagePath' => $storagePath]);
            }

            if (!$file) {
                return ['success' => false, 'error' => 'File not found'];
            }

            $absolutePath = base_path('storage/' . $file->getStoragePath());
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            $file->delete();
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
}