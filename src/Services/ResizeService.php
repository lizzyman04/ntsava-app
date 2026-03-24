<?php

namespace Source\Services;

use Source\Models\User;
use Source\Models\File;
use App\Core\ORMHelper;

class ResizeService
{
    private string $cacheDir;
    private array $allowedFilters = ['grayscale', 'blur', 'brightness', 'contrast'];
    
    public function __construct()
    {
        $this->cacheDir = base_path('storage/cache');
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function process(User $user, string $path, array $params): array
    {
        // Find file
        $fileRepo = ORMHelper::getRepository(File::class);
        $storagePath = $user->getStoragePath() . '/' . ltrim($path, '/');
        $file = $fileRepo->findOne(['userId' => $user->getId(), 'storagePath' => $storagePath]);
        
        if (!$file || $file->isDeleted()) {
            return ['success' => false, 'error' => 'File not found'];
        }
        
        // Check if it's an image
        if (!$file->isImage()) {
            return ['success' => false, 'error' => 'Resize only available for images'];
        }
        
        $sourcePath = base_path('storage/' . $file->getStoragePath());
        
        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'Source file not found'];
        }
        
        // Generate cache key
        $cacheKey = $this->generateCacheKey($file->getUuid(), $params);
        $cachePath = $this->cacheDir . '/' . $cacheKey;
        
        // Check cache
        if (file_exists($cachePath)) {
            return [
                'success' => true,
                'url' => $this->getCacheUrl($cacheKey),
                'path' => $cachePath,
                'cached' => true
            ];
        }
        
        // Process image
        try {
            $this->processImage($sourcePath, $cachePath, $params);
            return [
                'success' => true,
                'url' => $this->getCacheUrl($cacheKey),
                'path' => $cachePath,
                'cached' => false
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function processImage(string $source, string $target, array $params): void
    {
        // Get source image info
        $info = getimagesize($source);
        if (!$info) {
            throw new \Exception('Invalid image file');
        }
        
        $sourceWidth = $info[0];
        $sourceHeight = $info[1];
        $sourceType = $info[2];
        
        // Load source image
        $sourceImage = $this->loadImage($source, $sourceType);
        if (!$sourceImage) {
            throw new \Exception('Failed to load image');
        }
        
        // Calculate dimensions
        $width = isset($params['w']) ? (int)$params['w'] : $sourceWidth;
        $height = isset($params['h']) ? (int)$params['h'] : $sourceHeight;
        
        // Maintain aspect ratio if only one dimension provided
        if (isset($params['w']) && !isset($params['h'])) {
            $ratio = $sourceHeight / $sourceWidth;
            $height = (int)($width * $ratio);
        } elseif (!isset($params['w']) && isset($params['h'])) {
            $ratio = $sourceWidth / $sourceHeight;
            $width = (int)($height * $ratio);
        }
        
        // Create target image
        $targetImage = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG/GIF
        if ($sourceType == IMAGETYPE_PNG || $sourceType == IMAGETYPE_WEBP) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, $width, $height, $transparent);
        } elseif ($sourceType == IMAGETYPE_GIF) {
            $transparent = imagecolorallocate($targetImage, 0, 0, 0);
            imagecolortransparent($targetImage, $transparent);
        }
        
        // Resize
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        
        // Apply filters
        $this->applyFilters($targetImage, $params);
        
        // Convert format
        $format = isset($params['format']) ? strtolower($params['format']) : null;
        $this->saveImage($targetImage, $target, $format, $sourceType);
        
        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($targetImage);
    }
    
    private function loadImage(string $path, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }
    
    private function applyFilters($image, array $params): void
    {
        // Grayscale
        if (isset($params['filter']) && $params['filter'] === 'grayscale') {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }
        
        // Blur
        if (isset($params['blur'])) {
            $blurLevel = min(10, max(1, (int)$params['blur']));
            for ($i = 0; $i < $blurLevel; $i++) {
                imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }
        
        // Brightness
        if (isset($params['brightness'])) {
            $brightness = max(-255, min(255, (int)$params['brightness']));
            imagefilter($image, IMG_FILTER_BRIGHTNESS, $brightness);
        }
        
        // Contrast
        if (isset($params['contrast'])) {
            $contrast = max(-100, min(100, (int)$params['contrast']));
            imagefilter($image, IMG_FILTER_CONTRAST, $contrast);
        }
        
        // Sharpen
        if (isset($params['sharpen'])) {
            $sharpen = max(1, min(10, (int)$params['sharpen']));
            $matrix = [
                [-1, -1, -1],
                [-1, 16, -1],
                [-1, -1, -1]
            ];
            $divisor = 8;
            $offset = 0;
            imageconvolution($image, $matrix, $divisor, $offset);
        }
    }
    
    private function saveImage($image, string $path, ?string $format, int $originalType): void
    {
        $format = $format ?: $this->getFormatFromType($originalType);
        
        switch ($format) {
            case 'webp':
                imagewebp($image, $path, 80);
                break;
            case 'png':
                imagepng($image, $path, 8);
                break;
            case 'gif':
                imagegif($image, $path);
                break;
            case 'jpg':
            case 'jpeg':
            default:
                imagejpeg($image, $path, 85);
                break;
        }
    }
    
    private function getFormatFromType(int $type): string
    {
        switch ($type) {
            case IMAGETYPE_PNG:
                return 'png';
            case IMAGETYPE_GIF:
                return 'gif';
            case IMAGETYPE_WEBP:
                return 'webp';
            default:
                return 'jpg';
        }
    }
    
    private function generateCacheKey(string $uuid, array $params): string
    {
        ksort($params);
        $key = $uuid . '_' . http_build_query($params);
        return md5($key) . '.' . ($params['format'] ?? 'jpg');
    }
    
    private function getCacheUrl(string $cacheKey): string
    {
        return 'https://cdn.omeu.space/api/v1/cache/' . $cacheKey;
    }
}