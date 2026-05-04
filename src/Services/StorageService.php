<?php

namespace Source\Services;

use Source\Models\User;
use Source\Models\File;
use Source\Models\Plan;
use App\Core\ORMHelper;

class StorageService
{
    private const MIME_TYPES = [
        'jpg' => ['image/jpeg', 'image/jpg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'svg' => ['image/svg+xml'],
        'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
        'mp4' => ['video/mp4'],
        'mov' => ['video/quicktime'],
        'mp3' => ['audio/mpeg'],
        'wav' => ['audio/wav', 'audio/x-wav'],
        'ogg' => ['audio/ogg', 'video/ogg'],
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'txt' => ['text/plain'],
        'json' => ['application/json'],
        'xml' => ['application/xml', 'text/xml'],
        'css' => ['text/css'],
    ];

    private const DANGEROUS_EXTENSIONS = ['svg', 'html', 'htm', 'xml'];

    public function upload(User $user, array $file, string $path = ''): array
    {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Upload error: ' . $this->getUploadErrorMessage($file['error'])];
            }

            $plan = ORMHelper::findByPK(Plan::class, $user->getPlanId());
            $maxSize = $plan ? $plan->getMaxFileSizeBytes() : 10485760;
            $allowedTypes = $plan ? $plan->getAllowedMimeTypes() : ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $planName = $plan ? $plan->getName() : 'current';

            if ($file['size'] > $maxSize) {
                return ['success' => false, 'error' => sprintf(
                    "File too large for the %s plan. Max size: %.0fMB",
                    $planName,
                    $maxSize / 1048576
                )];
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedTypes)) {
                return ['success' => false, 'error' => sprintf(
                    "File type '%s' is not allowed on the %s plan. Allowed: %s",
                    $extension,
                    $planName,
                    implode(', ', $allowedTypes)
                )];
            }

            $mimeValidation = $this->validateMimeType($file['tmp_name'], $extension);
            if ($mimeValidation !== true) {
                return ['success' => false, 'error' => $mimeValidation];
            }

            $contentValidation = $this->validateFileContent($file['tmp_name'], $extension);
            if ($contentValidation !== true) {
                return ['success' => false, 'error' => $contentValidation];
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

    private function validateMimeType(string $filePath, string $extension): true|string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $allowedMimes = self::MIME_TYPES[$extension] ?? [];

        if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes)) {
            return sprintf(
                'File MIME type mismatch: expected %s, got %s',
                implode(' or ', $allowedMimes),
                $mimeType
            );
        }

        return true;
    }

    private function validateFileContent(string $filePath, string $extension): true|string
    {
        if (in_array($extension, self::DANGEROUS_EXTENSIONS)) {
            $content = file_get_contents($filePath);

            if (preg_match('/<script\b[^>]*>.*?<\/script>/is', $content)) {
                return 'File contains potentially malicious script tags';
            }

            if (preg_match('/\bon\w+\s*=/i', $content)) {
                return 'File contains potentially malicious event handlers';
            }

            if (preg_match('/javascript\s*:/i', $content)) {
                return 'File contains potentially malicious javascript: URLs';
            }
        }

        if ($extension === 'zip') {
            $zipValidation = $this->validateZipContent($filePath);
            if ($zipValidation !== true) {
                return $zipValidation;
            }
        }

        return true;
    }

    private function validateZipContent(string $filePath): true|string
    {
        if (!class_exists('ZipArchive')) {
            return true;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $forbidden = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'inc', 'js', 'html', 'htm'];

                if (in_array($ext, $forbidden)) {
                    $zip->close();
                    return 'ZIP file contains forbidden file types';
                }

                if (strpos($name, '../') !== false || strpos($name, '..\\') !== false) {
                    $zip->close();
                    return 'ZIP file contains path traversal attempts';
                }
            }
            $zip->close();
        }

        return true;
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