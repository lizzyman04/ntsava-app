<?php

namespace App\Core;

class Uploader
{
    private const UPLOAD_DIR = 'public/uploads';

    private const MIME_TYPES = [
        'jpg' => ['image/jpeg', 'image/jpg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'svg' => ['image/svg+xml'],
        'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
        'mp4' => ['video/mp4'],
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

    public static function upload(array $file, array $allowedTypes = null, int $maxSize = null): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(self::getUploadErrorMessage($file['error']));
        }

        $maxSize = $maxSize ?? (int) env('UPLOAD_MAX_SIZE', 104857600);
        if ($file['size'] > $maxSize) {
            throw new \RuntimeException(sprintf("File too large. Max size: %.2f MB", $maxSize / 1048576));
        }

        $allowedTypes = $allowedTypes ?? explode(',', env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp,mp4,pdf'));
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedTypes)) {
            throw new \RuntimeException(sprintf("File type '%s' not allowed. Allowed: %s", $extension, implode(', ', $allowedTypes)));
        }

        self::validateMimeType($file['tmp_name'], $extension);
        self::validateFileContent($file['tmp_name'], $extension);

        $hash = hash_file('sha256', $file['tmp_name']);
        $uniqueId = substr(bin2hex(random_bytes(8)), 0, 16);
        $fileName = $uniqueId . '_' . $hash . '.' . $extension;

        $uploadPath = base_path(self::UPLOAD_DIR);
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
            throw new \RuntimeException("Cannot create upload directory: {$uploadPath}");
        }

        $targetPath = $uploadPath . '/' . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException("Failed to move uploaded file");
        }

        chmod($targetPath, 0644);

        return self::getUrl($fileName);
    }

    private static function validateMimeType(string $filePath, string $extension): void
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $allowedMimes = self::MIME_TYPES[$extension] ?? [];

        if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes)) {
            throw new \RuntimeException(sprintf(
                'File MIME type mismatch: expected %s, got %s',
                implode(' or ', $allowedMimes),
                $mimeType
            ));
        }
    }

    private static function validateFileContent(string $filePath, string $extension): void
    {
        if (in_array($extension, self::DANGEROUS_EXTENSIONS)) {
            $content = file_get_contents($filePath);

            if (preg_match('/<script\b[^>]*>.*?<\/script>/is', $content)) {
                throw new \RuntimeException('File contains potentially malicious script tags');
            }

            if (preg_match('/\bon\w+\s*=/i', $content)) {
                throw new \RuntimeException('File contains potentially malicious event handlers');
            }

            if (preg_match('/javascript\s*:/i', $content)) {
                throw new \RuntimeException('File contains potentially malicious javascript: URLs');
            }
        }

        if ($extension === 'zip') {
            self::validateZipContent($filePath);
        }
    }

    private static function validateZipContent(string $filePath): void
    {
        if (!class_exists('ZipArchive')) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $forbidden = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'inc', 'js', 'html', 'htm'];

                if (in_array($ext, $forbidden)) {
                    $zip->close();
                    throw new \RuntimeException('ZIP file contains forbidden file types');
                }

                if (strpos($name, '../') !== false || strpos($name, '..\\') !== false) {
                    $zip->close();
                    throw new \RuntimeException('ZIP file contains path traversal attempts');
                }
            }
            $zip->close();
        }
    }

    private static function getUploadErrorMessage(int $code): string
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

    public static function delete(string $filePath): bool
    {
        $fullPath = base_path(self::UPLOAD_DIR . '/' . ltrim($filePath, '/'));
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public static function deleteByHash(string $hash, string $extension): bool
    {
        return self::delete($hash . '.' . $extension);
    }

    public static function getUrl(string $filePath): string
    {
        return base_url(self::UPLOAD_DIR . '/' . ltrim($filePath, '/'));
    }

    public static function getPath(string $filePath): string
    {
        return base_path(self::UPLOAD_DIR . '/' . ltrim($filePath, '/'));
    }

    public static function exists(string $filePath): bool
    {
        return file_exists(self::getPath($filePath));
    }

    public static function getHashFromFile(string $filePath): string
    {
        $fullPath = self::getPath($filePath);
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }
        return hash_file('sha256', $fullPath);
    }

    public static function getAll(array $options = []): array
    {
        $uploadPath = base_path(self::UPLOAD_DIR);
        if (!is_dir($uploadPath)) {
            return [];
        }

        $pattern = $options['pattern'] ?? '*';
        $files = glob($uploadPath . '/' . $pattern);
        $result = [];

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $result[] = [
                    'name' => $filename,
                    'hash' => pathinfo($filename, PATHINFO_FILENAME),
                    'extension' => pathinfo($filename, PATHINFO_EXTENSION),
                    'size' => filesize($file),
                    'size_mb' => round(filesize($file) / 1048576, 2),
                    'url' => self::getUrl($filename),
                    'path' => $file,
                    'created' => filectime($file),
                    'created_at' => date('Y-m-d H:i:s', filectime($file)),
                    'modified' => filemtime($file),
                    'mime_type' => mime_content_type($file)
                ];
            }
        }

        if (isset($options['sort'])) {
            usort($result, function ($a, $b) use ($options) {
                $direction = $options['direction'] ?? 'asc';
                $cmp = $a[$options['sort']] <=> $b[$options['sort']];
                return $direction === 'desc' ? -$cmp : $cmp;
            });
        }

        return $result;
    }

    public static function cleanOldFiles(int $olderThan = 86400): int
    {
        $uploadPath = base_path(self::UPLOAD_DIR);
        if (!is_dir($uploadPath)) {
            return 0;
        }

        $now = time();
        $deleted = 0;

        foreach (glob($uploadPath . '/*') as $file) {
            if (is_file($file) && ($now - filectime($file)) > $olderThan && unlink($file)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    public static function validate(array $file, array $allowedTypes = null, int $maxSize = null): array
    {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = self::getUploadErrorMessage($file['error']);
            return $errors;
        }

        $maxSize = $maxSize ?? (int) env('UPLOAD_MAX_SIZE', 104857600);
        if ($file['size'] > $maxSize) {
            $errors[] = sprintf("File too large. Max size: %.2f MB", $maxSize / 1048576);
        }

        $allowedTypes = $allowedTypes ?? explode(',', env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp,mp4,pdf'));
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedTypes)) {
            $errors[] = sprintf("File type '%s' not allowed. Allowed: %s", $extension, implode(', ', $allowedTypes));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = self::MIME_TYPES[$extension] ?? [];
        if (!empty($allowedMimes) && !in_array($mimeType, $allowedMimes)) {
            $errors[] = sprintf('File MIME type mismatch: expected %s, got %s', implode(' or ', $allowedMimes), $mimeType);
        }

        return $errors;
    }

    public static function getFileInfo(string $filePath): ?array
    {
        $fullPath = self::getPath($filePath);
        if (!file_exists($fullPath)) {
            return null;
        }

        $filename = basename($fullPath);
        return [
            'name' => $filename,
            'hash' => pathinfo($filename, PATHINFO_FILENAME),
            'extension' => pathinfo($filename, PATHINFO_EXTENSION),
            'size' => filesize($fullPath),
            'size_mb' => round(filesize($fullPath) / 1048576, 2),
            'url' => self::getUrl($filename),
            'path' => $fullPath,
            'created' => filectime($fullPath),
            'created_at' => date('Y-m-d H:i:s', filectime($fullPath)),
            'modified' => filemtime($fullPath),
            'mime_type' => mime_content_type($fullPath)
        ];
    }

    public static function getExtensionFromMime(string $mimeType): ?string
    {
        foreach (self::MIME_TYPES as $ext => $mimes) {
            if (in_array($mimeType, $mimes)) {
                return $ext;
            }
        }
        return null;
    }

    public static function isImage(string $filePath): bool
    {
        return str_starts_with(mime_content_type($filePath), 'image/');
    }

    public static function getImageDimensions(string $filePath): ?array
    {
        if (!self::isImage($filePath)) {
            return null;
        }

        $dimensions = getimagesize($filePath);
        if ($dimensions === false) {
            return null;
        }

        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'mime' => $dimensions['mime']
        ];
    }
}